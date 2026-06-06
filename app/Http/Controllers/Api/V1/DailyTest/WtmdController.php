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

class WtmdController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $wtmdEquipment = Equipment::where('name', 'wtmd')->first();
        
        if (!$wtmdEquipment) {
            return $this->errorResponse('Equipment WTMD tidak ditemukan', 404);
        }

        $locations = EquipmentLocation::where('equipment_id', $wtmdEquipment->id)
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

        return $this->successResponse($locations, 'Data lokasi WTMD berhasil diambil');
    }

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
                    $query->where('name', 'wtmd');
                })
                ->where('location_id', $request->location)
                ->first();

            if (!$equipmentLocation) {
                return $this->errorResponse('Relasi equipment WTMD dan lokasi tidak ditemukan', 404);
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
            // Store specific WTMD details here
            // Example: test1_in_depan, test2_in_depan, dll...
            // $reportDetail->test1_in_depan = $request->boolean('test1_in_depan');
            $reportDetail->save();

            DB::commit();

            return $this->successResponse(new ReportResource($report->load('details')), 'Report WTMD berhasil disimpan', 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }
}
