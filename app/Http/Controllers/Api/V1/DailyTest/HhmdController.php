<?php

namespace App\Http\Controllers\Api\V1\DailyTest;

use App\Http\Controllers\Controller;
use App\Models\EquipmentLocation;
use Illuminate\Http\Request;
use App\Models\Equipment;
use App\Models\Report;
use App\Models\ReportDetail;
use App\Models\ReportStatus;
use App\Traits\ApiResponse;
use App\Http\Resources\ReportResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class HhmdController extends Controller
{
    use ApiResponse;

    /**
     * Get locations for HHMD daily test
     */
    public function index()
    {
        $hhmdEquipment = Equipment::where('name', 'hhmd')->first();
        
        if (!$hhmdEquipment) {
            return $this->errorResponse('Equipment HHMD tidak ditemukan', 404);
        }

        $locations = EquipmentLocation::where('equipment_id', $hhmdEquipment->id)
            ->with('location')
            ->get()
            ->map(function ($el) {
                return [
                    'id' => $el->id,
                    'location_id' => $el->location_id,
                    'location_name' => $el->location->name ?? 'Tidak tersedia',
                    'merk_type' => $el->merk_type ?? 'Tidak tersedia',
                    'certificate_info' => $el->certificateInfo ?? 'Tidak tersedia',
                ];
            });

        return $this->successResponse($locations, 'Data lokasi HHMD berhasil diambil');
    }

    /**
     * Check if a report was already submitted within the 3 hour window
     */
    public function checkSubmission(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'location_id' => 'required|exists:locations,id',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal', 422, $validator->errors());
        }

        $equipmentLocation = EquipmentLocation::whereHas('equipment', function ($query) {
                $query->where('name', 'hhmd');
            })
            ->where('location_id', $request->location_id)
            ->first();

        if (!$equipmentLocation) {
            return $this->errorResponse('Lokasi equipment tidak ditemukan', 404);
        }

        if ($latestReport = Report::getLatestSubmissionInTimeWindow($equipmentLocation->id)) {
            $submissionTime = $latestReport->created_at;
            $nextSubmissionTime = $submissionTime->addHours(3);
            $remainingMinutes = (int)ceil(now()->diffInSeconds($nextSubmissionTime) / 60);

            return $this->successResponse([
                'can_submit' => false,
                'remaining_minutes' => $remainingMinutes
            ], 'Formulir sudah disubmit. Tunggu ' . $remainingMinutes . ' menit lagi.');
        }

        return $this->successResponse(['can_submit' => true], 'Formulir tersedia');
    }

    /**
     * Store a new HHMD daily test report
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'operatorName' => 'required|string|max:255',
            'testDateTime' => 'required|date',
            'location' => 'required|exists:locations,id',
            'deviceInfo' => 'required|string|max:255',
            'certificateInfo' => 'required|string|max:255',
            'result' => 'required|in:pass,fail',
            'notes' => 'nullable|string',
            'submitterSignature' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal', 422, $validator->errors());
        }

        try {
            DB::beginTransaction();
            
            $equipmentLocation = EquipmentLocation::whereHas('equipment', function ($query) {
                    $query->where('name', 'hhmd');
                })
                ->where('location_id', $request->location)
                ->first();

            if (!$equipmentLocation) {
                return $this->errorResponse('Relasi equipment HHMD dan lokasi tidak ditemukan', 404);
            }

            if ($latestReport = Report::getLatestSubmissionInTimeWindow($equipmentLocation->id)) {
                return $this->errorResponse('Anda telah mengirim formulir untuk lokasi ini dalam waktu dekat.', 429);
            }

            $pendingStatus = ReportStatus::where('name', 'pending')->first();

            $report = new Report();
            $report->testDate = $request->testDateTime;
            $report->equipmentLocationID = $equipmentLocation->id;
            $report->deviceInfo = $request->deviceInfo;
            $report->certificateInfo = $request->certificateInfo;
            $report->isFullFilled = $request->boolean('terpenuhi');
            $report->result = $request->result;
            $report->note = $request->notes;
            $report->statusID = $pendingStatus->id;
            $report->submittedByID = Auth::id();
            $report->submitterSignature = $request->submitterSignature;
            $report->save();

            $reportDetail = new ReportDetail();
            $reportDetail->reportID = $report->reportID;
            $reportDetail->terpenuhi = $request->boolean('terpenuhi');
            $reportDetail->tidakTerpenuhi = $request->boolean('tidakterpenuhi');
            $reportDetail->test1 = $request->boolean('test1');
            $reportDetail->testCondition1 = $request->boolean('testCondition1');
            $reportDetail->testCondition2 = $request->boolean('testCondition2');
            $reportDetail->save();

            DB::commit();

            return $this->successResponse(new ReportResource($report->load('details')), 'Report berhasil disimpan', 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get specific report details
     */
    public function show($id)
    {
        $report = Report::with(['submittedBy', 'status', 'reportDetails'])->find($id);

        if (!$report) {
            return $this->notFoundResponse('Report tidak ditemukan');
        }

        return $this->successResponse(new ReportResource($report), 'Detail report berhasil diambil');
    }

    /**
     * Update report status (Approve/Reject by Supervisor)
     */
    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status_id' => 'required|exists:report_statuses,id',
            'approvalNote' => 'nullable|string|max:500',
            'supervisor_signature' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal', 422, $validator->errors());
        }

        try {
            $report = Report::find($id);
            if (!$report) {
                return $this->notFoundResponse('Report tidak ditemukan');
            }

            $report->statusID = $request->status_id;

            $rejectedStatus = ReportStatus::where('name', 'rejected')->first();
            if ($rejectedStatus && $request->status_id == $rejectedStatus->id) {
                if (empty($request->approvalNote)) {
                    return $this->errorResponse('Catatan penolakan harus diisi', 422);
                }
                $report->approvalNote = $request->approvalNote;
            }

            if (!$report->approverSignature && $request->has('supervisor_signature')) {
                $report->approverSignature = $request->supervisor_signature;
            }

            $report->approvedByID = Auth::id();
            $report->save();

            return $this->successResponse(new ReportResource($report), 'Status laporan berhasil diperbarui');
        } catch (\Exception $e) {
            return $this->errorResponse('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Save supervisor signature
     */
    public function saveSignature(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'signature' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal', 422, $validator->errors());
        }

        try {
            $report = Report::find($id);
            if (!$report) {
                return $this->notFoundResponse('Report tidak ditemukan');
            }

            $report->approverSignature = $request->signature;
            $report->approvedByID = Auth::id();
            $report->save();

            return $this->successResponse(null, 'Tanda tangan berhasil disimpan');
        } catch (\Exception $e) {
            return $this->errorResponse('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update rejected report (by Officer)
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'testDateTime' => 'required|date',
            'location' => 'required|exists:locations,id',
            'deviceInfo' => 'required|string|max:255',
            'certificateInfo' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'terpenuhi' => 'boolean',
            'tidakterpenuhi' => 'boolean',
            'test1' => 'boolean',
            'testCondition1' => 'boolean',
            'testCondition2' => 'boolean',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal', 422, $validator->errors());
        }

        try {
            DB::beginTransaction();

            $report = Report::find($id);
            if (!$report) {
                return $this->notFoundResponse('Report tidak ditemukan');
            }

            if ($report->submittedByID !== Auth::id()) {
                return $this->unauthorizedResponse('Anda tidak memiliki izin untuk mengedit laporan ini.');
            }

            $equipmentLocation = EquipmentLocation::whereHas('equipment', function ($query) {
                $query->where('name', 'hhmd');
            })
            ->where('location_id', $request->location)
            ->first();

            if (!$equipmentLocation) {
                return $this->errorResponse('Lokasi tidak valid', 422);
            }

            $pendingStatus = ReportStatus::where('name', 'pending')->first();

            $report->testDate = $request->testDateTime;
            $report->equipmentLocationID = $equipmentLocation->id;
            $report->deviceInfo = $request->deviceInfo;
            $report->certificateInfo = $request->certificateInfo;
            $report->isFullFilled = $request->boolean('terpenuhi');
            $report->result = $request->boolean('test1') ? 'pass' : 'fail';
            $report->note = $request->notes;
            $report->statusID = $pendingStatus->id;
            $report->approvalNote = null;
            $report->save();

            $reportDetail = ReportDetail::where('reportID', $report->reportID)->first() ?? new ReportDetail(['reportID' => $report->reportID]);
            $reportDetail->terpenuhi = $request->boolean('terpenuhi');
            $reportDetail->tidakterpenuhi = $request->boolean('tidakterpenuhi');
            $reportDetail->test1 = $request->boolean('test1');
            $reportDetail->testCondition1 = $request->boolean('testCondition1');
            $reportDetail->testCondition2 = $request->boolean('testCondition2');
            $reportDetail->save();

            DB::commit();

            return $this->successResponse(new ReportResource($report->load('details')), 'Laporan berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }
}
