<?php

namespace App\Http\Controllers\DailyTest;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\EquipmentLocation;
use App\Models\Location;
use App\Models\Report;
use App\Models\ReportDetail;
use App\Models\ReportStatus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class XrayController extends Controller
{
    /**
     * Display the X-Ray Cabin layout.
     *
     * @return \Illuminate\View\View
     */

    public function xrayCabinLayout()
    {
        $xrayCabinEquipment = Equipment::where('name', 'xraycabin')->first();
        // Ambil lokasi yang terhubung dengan X-Ray Bagasi
        $xrayCabinLocations = collect();
        if ($xrayCabinEquipment) {
            // Gunakan EquipmentLocation untuk mendapatkan data yang lebih lengkap
            $xrayCabinLocations = EquipmentLocation::where('equipment_id', $xrayCabinEquipment->id)
                ->with(['location']) // Eager loading untuk menghindari N+1 query
                ->get()
                ->map(function ($el) {
                    return [
                        'location_id' => $el->location_id,
                        'location_name' => $el->location->name ?? 'Nama lokasi tidak tersedia',
                        'merk_type' => $el->merk_type ?? 'Merk/Type tidak tersedia',
                        'certificateInfo' => $el->certificateInfo ?? 'Informasi sertifikat tidak tersedia',
                    ];
                });
        }
        return view('daily-test.xrayCabinLayout', [
            'xrayCabinLocations' => $xrayCabinLocations,
            'type' => 'xrayCabin'
        ]);
    }

    public function storeXrayCabin(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'operatorName' => 'required|string|max:255',
            'testDateTime' => 'required|date',
            'location' => 'required|exists:locations,id',
            'deviceInfo' => 'required|string|max:255',
            'certificateInfo' => 'required|string|max:255',
            'result' => 'required|in:pass,fail',
            'notes' => 'nullable|string',
            'submittedByID' => 'nullable|exists:users,id',
            'submitterSignature' => 'required|string',
            'approvedByID ' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {

            $equipmentLocation = EquipmentLocation::whereHas('equipment', function ($query) {
                $query->where('name', 'xraycabin');
            })
                ->where('location_id', $request->location)
                ->first();

            if (!$equipmentLocation) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Relasi equipment XRAY CABIN dan lokasi tidak ditemukan'
                ], 404);
            }

            if ($latestReport = Report::getLatestSubmissionInTimeWindow($equipmentLocation->id)) {
                $submissionTime = $latestReport->created_at;
                $nextSubmissionTime = $submissionTime->addHours(3);
                $remainingMinutes = (int)ceil(now()->diffInSeconds($nextSubmissionTime) / 60);

                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda telah mengirim formulir untuk lokasi ini. Coba lagi dalam ' . $remainingMinutes . ' menit.'
                ], 429);
            }

            // Ambil status 'pending_supervisor'
            $pendingStatus = ReportStatus::where('name', 'pending')->first();

            if (!$pendingStatus) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Status report tidak ditemukan'
                ], 404);
            }

            // Buat report baru
            $report = new Report();
            $report->testDate = $request->testDateTime;
            $report->equipmentLocationID = $equipmentLocation->id;
            $report->deviceInfo = $request->deviceInfo;
            $report->certificateInfo = $request->certificateInfo;
            $report->isFullFilled = $request->terpenuhi ? true : false;
            $report->result = $request->result;
            $report->note = $request->notes;
            $report->statusID = $pendingStatus->id;
            $report->submittedByID = $request->submittedByID ?? Auth::id();
            $report->submitterSignature = $request->submitterSignature;
            $report->approvedByID = $request->approvedByID;
            $report->save();

            // Buat report detail
            $reportDetail = new ReportDetail();
            $reportDetail->reportID = $report->reportID;
            $reportDetail->terpenuhi = $request->terpenuhi ? true : false;
            $reportDetail->tidakTerpenuhi = $request->tidakterpenuhi ? true : false;
            $reportDetail->test1ab_36 = $request->test1ab_36 ? true : false;
            $reportDetail->test1ab_32 = $request->test1ab_32 ? true : false;
            $reportDetail->test1ab_30 = $request->test1ab_30 ? true : false;
            $reportDetail->test1ab_24 = $request->test1ab_24 ? true : false;
            $reportDetail->test1bb_36_1 = $request->test1bb_36_1 ? true : false;
            $reportDetail->test1bb_32_1 = $request->test1bb_32_1 ? true : false;
            $reportDetail->test1bb_30_1 = $request->test1bb_30_1 ? true : false;
            $reportDetail->test1bb_24_1 = $request->test1bb_24_1 ? true : false;
            $reportDetail->test1bb_36_2 = $request->test1bb_36_2 ? true : false;
            $reportDetail->test1bb_32_2 = $request->test1bb_32_2 ? true : false;
            $reportDetail->test1bb_30_2 = $request->test1bb_30_2 ? true : false;
            $reportDetail->test1bb_24_2 = $request->test1bb_24_2 ? true : false;
            $reportDetail->test1bb_36_3 = $request->test1bb_36_3 ? true : false;
            $reportDetail->test1bb_32_3 = $request->test1bb_32_3 ? true : false;
            $reportDetail->test1bb_30_3 = $request->test1bb_30_3 ? true : false;
            $reportDetail->test1bb_24_3 = $request->test1bb_24_3 ? true : false;
            $reportDetail->test1aab_36 = $request->test1aab_36 ? true : false;
            $reportDetail->test1aab_32 = $request->test1aab_32 ? true : false;
            $reportDetail->test1aab_30 = $request->test1aab_30 ? true : false;
            $reportDetail->test1aab_24 = $request->test1aab_24 ? true : false;
            $reportDetail->test1bab_36_1 = $request->test1bab_36_1 ? true : false;
            $reportDetail->test1bab_32_1 = $request->test1bab_32_1 ? true : false;
            $reportDetail->test1bab_30_1 = $request->test1bab_30_1 ? true : false;
            $reportDetail->test1bab_24_1 = $request->test1bab_24_1 ? true : false;
            $reportDetail->test1bab_36_2 = $request->test1bab_36_2 ? true : false;
            $reportDetail->test1bab_32_2 = $request->test1bab_32_2 ? true : false;
            $reportDetail->test1bab_30_2 = $request->test1bab_30_2 ? true : false;
            $reportDetail->test1bab_24_2 = $request->test1bab_24_2 ? true : false;
            $reportDetail->test1bab_36_3 = $request->test1bab_36_3 ? true : false;
            $reportDetail->test1bab_32_3 = $request->test1bab_32_3 ? true : false;
            $reportDetail->test1bab_30_3 = $request->test1bab_30_3 ? true : false;
            $reportDetail->test1bab_24_3 = $request->test1bab_24_3 ? true : false;
            $reportDetail->test2ab = $request->test2ab ? true : false;
            $reportDetail->test2bb = $request->test2bb ? true : false;
            $reportDetail->test2aab = $request->test2aab ? true : false;
            $reportDetail->test2bab = $request->test2bab ? true : false;
            $reportDetail->test3ab_14 = $request->test3ab_14 ? true : false;
            $reportDetail->test3ab_16 = $request->test3ab_16 ? true : false;
            $reportDetail->test3ab_18 = $request->test3ab_18 ? true : false;
            $reportDetail->test3ab_20 = $request->test3ab_20 ? true : false;
            $reportDetail->test3ab_22 = $request->test3ab_22 ? true : false;
            $reportDetail->test3ab_24 = $request->test3ab_24 ? true : false;
            $reportDetail->test3ab_26 = $request->test3ab_26 ? true : false;
            $reportDetail->test3ab_28 = $request->test3ab_28 ? true : false;
            $reportDetail->test3ab_30 = $request->test3ab_30 ? true : false;
            $reportDetail->test3b_14 = $request->test3b_14 ? true : false;
            $reportDetail->test3b_16 = $request->test3b_16 ? true : false;
            $reportDetail->test3b_18 = $request->test3b_18 ? true : false;
            $reportDetail->test3b_20 = $request->test3b_20 ? true : false;
            $reportDetail->test3b_22 = $request->test3b_22 ? true : false;
            $reportDetail->test3b_24 = $request->test3b_24 ? true : false;
            $reportDetail->test3b_26 = $request->test3b_26 ? true : false;
            $reportDetail->test3b_28 = $request->test3b_28 ? true : false;
            $reportDetail->test3b_30 = $request->test3b_30 ? true : false;
            $reportDetail->test4ab_h10mm = $request->test4ab_h10mm ? true : false;
            $reportDetail->test4ab_v10mm = $request->test4ab_v10mm ? true : false;
            $reportDetail->test4ab_h15mm = $request->test4ab_h15mm ? true : false;
            $reportDetail->test4ab_v15mm = $request->test4ab_v15mm ? true : false;
            $reportDetail->test4ab_h20mm = $request->test4ab_h20mm ? true : false;
            $reportDetail->test4ab_v20mm = $request->test4ab_v20mm ? true : false;
            $reportDetail->test4b_h10mm = $request->test4b_h10mm ? true : false;
            $reportDetail->test4b_v10mm = $request->test4b_v10mm ? true : false;
            $reportDetail->test4b_h15mm = $request->test4b_h15mm ? true : false;
            $reportDetail->test4b_v15mm = $request->test4b_v15mm ? true : false;
            $reportDetail->test4b_h20mm = $request->test4b_h20mm ? true : false;
            $reportDetail->test4b_v20mm = $request->test4b_v20mm ? true : false;
            $reportDetail->test5ab_05mm = $request->test5ab_05mm ? true : false;
            $reportDetail->test5ab_10mm = $request->test5ab_10mm ? true : false;
            $reportDetail->test5ab_15mm = $request->test5ab_15mm ? true : false;
            $reportDetail->test5b_05mm = $request->test5b_05mm ? true : false;
            $reportDetail->test5b_10mm = $request->test5b_10mm ? true : false;
            $reportDetail->test5b_15mm = $request->test5b_15mm ? true : false;
            $reportDetail->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Report berhasil disimpan',
                'report' => $report
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function editRejectedReportCabin($id)
    {
        try {
            // Load report dengan relasi yang diperlukan
            $report = Report::with([
                'submittedBy',
                'status',
                'reportDetails',
                'equipmentLocation.location',
                'equipmentLocation.equipment'
            ])->findOrFail($id);

            // Pastikan ini adalah laporan X-Ray Cabin
            if ($report->equipmentLocation->equipment->name !== 'xraycabin') {
                Log::warning("Invalid report type for reportID: $id. Equipment: " . $report->equipmentLocation->equipment->name);
                return redirect()->back()->with('error', 'Invalid report type');
            }

            // Ambil semua lokasi yang berelasi dengan equipment X-Ray Cabin
            $xrayEquipment = Equipment::where('name', 'xraycabin')->first();
            $xrayLocations = collect();
            if ($xrayEquipment) {
                $xrayLocations = EquipmentLocation::where('equipment_id', $xrayEquipment->id)
                    ->with('location')
                    ->get()
                    ->map(function ($el) {
                        return $el->location;
                    })
                    ->unique('id');
            }

             // Ambil detail report
            $details = $report->reportDetails->first();

            return view('officer.editXrayCabin', [
                'form' => $report,
                'details' => $details,
                'xrayLocations' => $xrayLocations,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error("Report not found with reportID: $id");
            return redirect()->back()->with('error', 'Report tidak ditemukan');
        } catch (\Exception $e) {
            Log::error('Error in edit rejected report: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function updateCabin(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'testDateTime' => 'required|date',
            'deviceInfo' => 'required|string|max:255',
            'certificateInfo' => 'required|string|max:255',
            'result' => 'required|in:pass,fail',
            'note' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $report = Report::with('reportDetails')->findOrFail($id);
            $reportDetail = $report->reportDetails->first();

            if (!$reportDetail) {
                throw new \Exception('Detail laporan tidak ditemukan.');
            }

            // Update Report
            $report->testDate = $request->testDateTime;
            $report->deviceInfo = $request->deviceInfo;
            $report->certificateInfo = $request->certificateInfo;
            $report->isFullFilled = $request->input('terpenuhi', 0);
            $report->result = $request->result;
            $report->note = $request->note;

            // Reset status to pending for re-approval
            $pendingStatus = ReportStatus::where('name', 'pending')->firstOrFail();
            $report->statusID = $pendingStatus->id;
            $report->approvalNote = null;
            $report->save();

            // Prepare data for ReportDetail update
            $detailsData = [];
            $checkboxFields = [
                'terpenuhi', 'tidakterpenuhi',
                'test2aab', 'test2bab',
                'test3ab_14', 'test3ab_16', 'test3ab_18', 'test3ab_20', 'test3ab_22', 'test3ab_24', 'test3ab_26', 'test3ab_28', 'test3ab_30',
                'test1aab_36', 'test1aab_32', 'test1aab_30', 'test1aab_24',
                'test1bab_36_1', 'test1bab_32_1', 'test1bab_30_1', 'test1bab_24_1', 'test1bab_36_2', 'test1bab_32_2', 'test1bab_30_2', 'test1bab_24_2', 'test1bab_36_3', 'test1bab_32_3', 'test1bab_30_3', 'test1bab_24_3',
                'test4ab_h15mm', 'test4ab_v15mm', 'test4ab_h20mm', 'test4ab_v20mm', 'test4ab_h10mm', 'test4ab_v10mm',
                'test5ab_05mm', 'test5ab_10mm', 'test5ab_15mm',
                'test2ab', 'test2bb',
                'test3b_14', 'test3b_16', 'test3b_18', 'test3b_20', 'test3b_22', 'test3b_24', 'test3b_26', 'test3b_28', 'test3b_30',
                'test1ab_36', 'test1ab_32', 'test1ab_30', 'test1ab_24',
                'test1bb_36_1', 'test1bb_32_1', 'test1bb_30_1', 'test1bb_24_1', 'test1bb_36_2', 'test1bb_32_2', 'test1bb_30_2', 'test1bb_24_2', 'test1bb_36_3', 'test1bb_32_3', 'test1bb_30_3', 'test1bb_24_3',
                'test4b_h15mm', 'test4b_v15mm', 'test4b_h20mm', 'test4b_v20mm', 'test4b_h10mm', 'test4b_v10mm',
                'test5b_05mm', 'test5b_10mm', 'test5b_15mm'
            ];

            foreach ($checkboxFields as $field) {
                $detailsData[$field] = $request->input($field, 0);
            }

            $reportDetail->update($detailsData);

            DB::commit();

            return redirect()->route('dashboard.officer')->with('success', 'Laporan X-Ray Cabin berhasil diperbarui dan dikirim kembali untuk persetujuan.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating X-Ray Cabin report: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui laporan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the X-Ray Bagasi layout.
     *
     * @return \Illuminate\View\View
     */

    public function xrayBagasiLayout()
    {
        // Ambil equipment X-Ray Bagasi
        $xrayBagasiEquipment = Equipment::where('name', 'xraybagasi')->first();
        // Ambil lokasi yang terhubung dengan X-Ray Bagasi
        $xrayBagasiLocations = collect();
        if ($xrayBagasiEquipment) {
            // Gunakan EquipmentLocation untuk mendapatkan data yang lebih lengkap
            $xrayBagasiLocations = EquipmentLocation::where('equipment_id', $xrayBagasiEquipment->id)
                ->with(['location']) // Eager loading untuk menghindari N+1 query
                ->get()
                ->map(function ($el) {
                    return [
                        'location_id' => $el->location_id,
                        'location_name' => $el->location->name ?? 'Nama lokasi tidak tersedia',
                        'merk_type' => $el->merk_type ?? 'Merk/Type tidak tersedia',
                        'certificateInfo' => $el->certificateInfo ?? 'Informasi sertifikat tidak tersedia',
                    ];
                });
        }


        return view(
            'daily-test.xrayBagasiLayout',
            [
                'xrayBagasiLocations' => $xrayBagasiLocations,
                'type' => 'xrayBagasi'
            ]
        );
    }

    public function storeXrayBagasi(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'operatorName' => 'required|string|max:255',
            'testDateTime' => 'required|date',
            'location' => 'required|exists:locations,id',
            'deviceInfo' => 'required|string|max:255',
            'certificateInfo' => 'required|string|max:255',
            'result' => 'required|in:pass,fail',
            'notes' => 'nullable|string',
            'submittedByID' => 'nullable|exists:users,id',
            'submitterSignature' => 'required|string',
            'approvedByID ' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Ambil equipment_location berdasarkan location_id dan equipment xraybagasi
            $equipmentLocation = EquipmentLocation::whereHas('equipment', function ($query) {
                $query->where('name', 'xraybagasi');
            })
                ->where('location_id', $request->location)
                ->first();

            if (!$equipmentLocation) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Relasi equipment XRAY BAGASI dan lokasi tidak ditemukan'
                ], 404);
            }

            if ($latestReport = Report::getLatestSubmissionInTimeWindow($equipmentLocation->id)) {
                $submissionTime = $latestReport->created_at;
                $nextSubmissionTime = $submissionTime->addHours(3);
                $remainingMinutes = (int)ceil(now()->diffInSeconds($nextSubmissionTime) / 60);

                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda telah mengirim formulir untuk lokasi ini. Coba lagi dalam ' . $remainingMinutes . ' menit.'
                ], 429);
            }

            // Ambil status 'pending_supervisor'
            $pendingStatus = ReportStatus::where('name', 'pending')->first();

            if (!$pendingStatus) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Status report tidak ditemukan'
                ], 404);
            }

            // Buat report baru
            $report = new Report();
            $report->testDate = $request->testDateTime;
            $report->equipmentLocationID = $equipmentLocation->id;
            $report->deviceInfo = $request->deviceInfo;
            $report->certificateInfo = $request->certificateInfo;
            $report->isFullFilled = $request->terpenuhi ? true : false;
            $report->result = $request->result;
            $report->note = $request->notes;
            $report->statusID = $pendingStatus->id;
            $report->submittedByID = $request->submittedByID ?? Auth::id();
            $report->submitterSignature = $request->submitterSignature;
            $report->approvedByID = $request->approvedByID;
            $report->save();

            // Buat report detail
            $reportDetail = new ReportDetail();
            $reportDetail->reportID = $report->reportID;
            $reportDetail->terpenuhi = $request->terpenuhi ? true : false;
            $reportDetail->tidakTerpenuhi = $request->tidakterpenuhi ? true : false;
            $reportDetail->test1ab_36 = $request->test1ab_36 ? true : false;
            $reportDetail->test1ab_32 = $request->test1ab_32 ? true : false;
            $reportDetail->test1ab_30 = $request->test1ab_30 ? true : false;
            $reportDetail->test1ab_24 = $request->test1ab_24 ? true : false;
            $reportDetail->test1bb_36_1 = $request->test1bb_36_1 ? true : false;
            $reportDetail->test1bb_32_1 = $request->test1bb_32_1 ? true : false;
            $reportDetail->test1bb_30_1 = $request->test1bb_30_1 ? true : false;
            $reportDetail->test1bb_24_1 = $request->test1bb_24_1 ? true : false;
            $reportDetail->test1bb_36_2 = $request->test1bb_36_2 ? true : false;
            $reportDetail->test1bb_32_2 = $request->test1bb_32_2 ? true : false;
            $reportDetail->test1bb_30_2 = $request->test1bb_30_2 ? true : false;
            $reportDetail->test1bb_24_2 = $request->test1bb_24_2 ? true : false;
            $reportDetail->test1bb_36_3 = $request->test1bb_36_3 ? true : false;
            $reportDetail->test1bb_32_3 = $request->test1bb_32_3 ? true : false;
            $reportDetail->test1bb_30_3 = $request->test1bb_30_3 ? true : false;
            $reportDetail->test1bb_24_3 = $request->test1bb_24_3 ? true : false;
            $reportDetail->test1aab_36 = $request->test1aab_36 ? true : false;
            $reportDetail->test1aab_32 = $request->test1aab_32 ? true : false;
            $reportDetail->test1aab_30 = $request->test1aab_30 ? true : false;
            $reportDetail->test1aab_24 = $request->test1aab_24 ? true : false;
            $reportDetail->test1bab_36_1 = $request->test1bab_36_1 ? true : false;
            $reportDetail->test1bab_32_1 = $request->test1bab_32_1 ? true : false;
            $reportDetail->test1bab_30_1 = $request->test1bab_30_1 ? true : false;
            $reportDetail->test1bab_24_1 = $request->test1bab_24_1 ? true : false;
            $reportDetail->test1bab_36_2 = $request->test1bab_36_2 ? true : false;
            $reportDetail->test1bab_32_2 = $request->test1bab_32_2 ? true : false;
            $reportDetail->test1bab_30_2 = $request->test1bab_30_2 ? true : false;
            $reportDetail->test1bab_24_2 = $request->test1bab_24_2 ? true : false;
            $reportDetail->test1bab_36_3 = $request->test1bab_36_3 ? true : false;
            $reportDetail->test1bab_32_3 = $request->test1bab_32_3 ? true : false;
            $reportDetail->test1bab_30_3 = $request->test1bab_30_3 ? true : false;
            $reportDetail->test1bab_24_3 = $request->test1bab_24_3 ? true : false;
            $reportDetail->test2ab = $request->test2ab ? true : false;
            $reportDetail->test2bb = $request->test2bb ? true : false;
            $reportDetail->test2aab = $request->test2aab ? true : false;
            $reportDetail->test2bab = $request->test2bab ? true : false;
            $reportDetail->test3ab_14 = $request->test3ab_14 ? true : false;
            $reportDetail->test3ab_16 = $request->test3ab_16 ? true : false;
            $reportDetail->test3ab_18 = $request->test3ab_18 ? true : false;
            $reportDetail->test3ab_20 = $request->test3ab_20 ? true : false;
            $reportDetail->test3ab_22 = $request->test3ab_22 ? true : false;
            $reportDetail->test3ab_24 = $request->test3ab_24 ? true : false;
            $reportDetail->test3ab_26 = $request->test3ab_26 ? true : false;
            $reportDetail->test3ab_28 = $request->test3ab_28 ? true : false;
            $reportDetail->test3ab_30 = $request->test3ab_30 ? true : false;
            $reportDetail->test3b_14 = $request->test3b_14 ? true : false;
            $reportDetail->test3b_16 = $request->test3b_16 ? true : false;
            $reportDetail->test3b_18 = $request->test3b_18 ? true : false;
            $reportDetail->test3b_20 = $request->test3b_20 ? true : false;
            $reportDetail->test3b_22 = $request->test3b_22 ? true : false;
            $reportDetail->test3b_24 = $request->test3b_24 ? true : false;
            $reportDetail->test3b_26 = $request->test3b_26 ? true : false;
            $reportDetail->test3b_28 = $request->test3b_28 ? true : false;
            $reportDetail->test3b_30 = $request->test3b_30 ? true : false;
            $reportDetail->test4ab_h10mm = $request->test4ab_h10mm ? true : false;
            $reportDetail->test4ab_v10mm = $request->test4ab_v10mm ? true : false;
            $reportDetail->test4ab_h15mm = $request->test4ab_h15mm ? true : false;
            $reportDetail->test4ab_v15mm = $request->test4ab_v15mm ? true : false;
            $reportDetail->test4ab_h20mm = $request->test4ab_h20mm ? true : false;
            $reportDetail->test4ab_v20mm = $request->test4ab_v20mm ? true : false;
            $reportDetail->test4b_h10mm = $request->test4b_h10mm ? true : false;
            $reportDetail->test4b_v10mm = $request->test4b_v10mm ? true : false;
            $reportDetail->test4b_h15mm = $request->test4b_h15mm ? true : false;
            $reportDetail->test4b_v15mm = $request->test4b_v15mm ? true : false;
            $reportDetail->test4b_h20mm = $request->test4b_h20mm ? true : false;
            $reportDetail->test4b_v20mm = $request->test4b_v20mm ? true : false;
            $reportDetail->test5ab_05mm = $request->test5ab_05mm ? true : false;
            $reportDetail->test5ab_10mm = $request->test5ab_10mm ? true : false;
            $reportDetail->test5ab_15mm = $request->test5ab_15mm ? true : false;
            $reportDetail->test5b_05mm = $request->test5b_05mm ? true : false;
            $reportDetail->test5b_10mm = $request->test5b_10mm ? true : false;
            $reportDetail->test5b_15mm = $request->test5b_15mm ? true : false;
            $reportDetail->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Report berhasil disimpan',
                'report' => $report
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function editRejectedReportBagasi($id)
    {
        try {
            // Load report dengan relasi yang diperlukan
            $report = Report::with([
                'submittedBy',
                'status',
                'reportDetails',
                'equipmentLocation.location',
                'equipmentLocation.equipment'
            ])->findOrFail($id);

            // Pastikan ini adalah laporan X-Ray Bagasi
            if ($report->equipmentLocation->equipment->name !== 'xraybagasi') {
                Log::warning("Invalid report type for reportID: $id. Equipment: " . $report->equipmentLocation->equipment->name);
                return redirect()->back()->with('error', 'Invalid report type');
            }

           // Ambil semua lokasi yang berelasi dengan equipment X-Ray bagasi
            $xrayEquipment = Equipment::where('name', 'xraybagasi')->first();
            $xrayLocations = collect();
            if ($xrayEquipment) {
                $xrayLocations = EquipmentLocation::where('equipment_id', $xrayEquipment->id)
                    ->with('location')
                    ->get()
                    ->map(function ($el) {
                        return $el->location;
                    })
                    ->unique('id');
            }

             // Ambil detail report
            $details = $report->reportDetails->first();

            return view('officer.editXrayBagasi', [
                'form' => $report,
                'details' => $details,
                'xrayLocations' => $xrayLocations,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error("Report not found with reportID: $id");
            return redirect()->back()->with('error', 'Report tidak ditemukan');
        } catch (\Exception $e) {
            Log::error('Error in edit rejected report: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function updateBagasi(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'testDateTime' => 'required|date',
            'deviceInfo' => 'required|string|max:255',
            'certificateInfo' => 'required|string|max:255',
            'result' => 'required|in:pass,fail',
            'note' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $report = Report::with('reportDetails')->findOrFail($id);
            $reportDetail = $report->reportDetails->first();

            if (!$reportDetail) {
                throw new \Exception('Detail laporan tidak ditemukan.');
            }

            // Update Report
            $report->testDate = $request->testDateTime;
            $report->deviceInfo = $request->deviceInfo;
            $report->certificateInfo = $request->certificateInfo;
            $report->isFullFilled = $request->input('terpenuhi', 0);
            $report->result = $request->result;
            $report->note = $request->note;

            // Reset status to pending for re-approval
            $pendingStatus = ReportStatus::where('name', 'pending')->firstOrFail();
            $report->statusID = $pendingStatus->id;
            $report->approvalNote = null;
            $report->save();

            // Prepare data for ReportDetail update
            $detailsData = [];
            $checkboxFields = [
                'terpenuhi', 'tidakterpenuhi',
                'test2aab', 'test2bab',
                'test3ab_14', 'test3ab_16', 'test3ab_18', 'test3ab_20', 'test3ab_22', 'test3ab_24', 'test3ab_26', 'test3ab_28', 'test3ab_30',
                'test1aab_36', 'test1aab_32', 'test1aab_30', 'test1aab_24',
                'test1bab_36_1', 'test1bab_32_1', 'test1bab_30_1', 'test1bab_24_1', 'test1bab_36_2', 'test1bab_32_2', 'test1bab_30_2', 'test1bab_24_2', 'test1bab_36_3', 'test1bab_32_3', 'test1bab_30_3', 'test1bab_24_3',
                'test4ab_h15mm', 'test4ab_v15mm', 'test4ab_h20mm', 'test4ab_v20mm', 'test4ab_h10mm', 'test4ab_v10mm',
                'test5ab_05mm', 'test5ab_10mm', 'test5ab_15mm',
                'test2ab', 'test2bb',
                'test3b_14', 'test3b_16', 'test3b_18', 'test3b_20', 'test3b_22', 'test3b_24', 'test3b_26', 'test3b_28', 'test3b_30',
                'test1ab_36', 'test1ab_32', 'test1ab_30', 'test1ab_24',
                'test1bb_36_1', 'test1bb_32_1', 'test1bb_30_1', 'test1bb_24_1', 'test1bb_36_2', 'test1bb_32_2', 'test1bb_30_2', 'test1bb_24_2', 'test1bb_36_3', 'test1bb_32_3', 'test1bb_30_3', 'test1bb_24_3',
                'test4b_h15mm', 'test4b_v15mm', 'test4b_h20mm', 'test4b_v20mm', 'test4b_h10mm', 'test4b_v10mm',
                'test5b_05mm', 'test5b_10mm', 'test5b_15mm'
            ];

            foreach ($checkboxFields as $field) {
                $detailsData[$field] = $request->input($field, 0);
            }

            $reportDetail->update($detailsData);

            DB::commit();

            return redirect()->route('dashboard.officer')->with('success', 'Laporan X-Ray Cabin berhasil diperbarui dan dikirim kembali untuk persetujuan.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating X-Ray Cabin report: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui laporan: ' . $e->getMessage())->withInput();
        }
    }

    public function reviewForm($id)
    {
        Log::info("Accessing XRAY review form with reportID: $id");

        try {
            // Load report tanpa relasi yang bermasalah dulu
            $report = Report::with([
                'submittedBy',
                'status',
                'reportDetails'
            ])->findOrFail($id);

            Log::info("Report found: " . $report->reportID);

            // Get equipment and location data manually
            $equipmentLocationData = DB::table('equipment_locations')
                ->select(
                    'equipment_locations.id',
                    'equipment.id as equipment_id',
                    'equipment.name as equipment_name',
                    'locations.id as location_id',
                    'locations.name as location_name'
                )
                ->join('equipment', 'equipment_locations.equipment_id', '=', 'equipment.id')
                ->join('locations', 'equipment_locations.location_id', '=', 'locations.id')
                ->where('equipment_locations.id', $report->equipmentLocationID)
                ->first();

            if (!$equipmentLocationData) {
                Log::error("Equipment location data not found for reportID: $id");
                return redirect()->back()->with('error', 'Equipment location not found');
            }

            // Validate if this is an XRAY report
            if (!in_array($equipmentLocationData->equipment_name, ['xraycabin', 'xraybagasi'])) {
                Log::warning("Invalid report type for reportID: $id. Equipment: " . $equipmentLocationData->equipment_name);
                return redirect()->back()->with('error', 'Invalid report type');
            }

            // Get all possible statuses for the dropdown
            $statuses = ReportStatus::all();

            // Get assigned supervisor details
            $supervisor = null;
            if ($report->approvedByID) {
                $supervisor = User::find($report->approvedByID);
            }

            // Additional data validation
            if (!$report->reportDetails || $report->reportDetails->isEmpty()) {
                Log::warning("Report details not found for reportID: $id");
                return redirect()->back()->with('error', 'Report details not found');
            }

            // Create objects for backward compatibility
            $equipment = (object) [
                'id' => $equipmentLocationData->equipment_id,
                'name' => $equipmentLocationData->equipment_name
            ];

            $location = (object) [
                'id' => $equipmentLocationData->location_id,
                'name' => $equipmentLocationData->location_name
            ];

            $viewName = $equipmentLocationData->equipment_name === 'xraybagasi'
                ? 'daily-test.review-form.xrayBagasiReviewForm'
                : 'daily-test.review-form.xrayCabinReviewForm';

            return view($viewName, [
                'form' => $report,
                'statuses' => $statuses,
                'supervisor' => $supervisor,
                'location' => $location,
                'equipment' => $equipment
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error("Report not found with reportID: $id");
            return redirect()->back()->with('error', 'Report tidak ditemukan');
        } catch (\Exception $e) {
            Log::error('Error in WTMD review form: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            Log::info("Updating XRAY report status with reportID: $id");
            // Find the report by reportID
            $report = Report::where('reportID', $id)->firstOrFail();

            // Validasi input
            $validator = Validator::make($request->all(), [
                'status_id' => 'required|exists:report_statuses,id',
                'approvalNote' => 'nullable|string|max:500',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            // Update status laporan
            $report->statusID = $request->status_id;

            // Jika status adalah rejected, simpan catatan penolakan
            $rejectedStatus = ReportStatus::where('name', 'rejected')->first();
            if ($rejectedStatus && $request->status_id == $rejectedStatus->id) {
                if (empty($request->approvalNote)) {
                    return redirect()->back()
                        ->withErrors(['approvalNote' => 'Catatan penolakan harus diisi'])
                        ->withInput();
                }
                $report->approvalNote = $request->approvalNote;
            }

            // Simpan tanda tangan supervisor jika belum ada
            if (!$report->approverSignature && $request->has('supervisor_signature')) {
                $report->approverSignature = $request->supervisor_signature;
            }

            // Set ID supervisor yang menyetujui/menolak
            $report->approvedByID = Auth::id();

            // Simpan perubahan
            $report->save();

            return redirect()->route('supervisor.dailytest-form')
                ->with('success', 'Status laporan berhasil diperbarui');
        } catch (\Exception $e) {
            Log::error('Error updating report status: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function saveSupervisorSignature(Request $request, $id)
    {
        try {
            // Validasi input
            $validator = Validator::make($request->all(), [
                'signature' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Cari laporan berdasarkan ID
            $report = Report::findOrFail($id);

            // Simpan tanda tangan supervisor
            $report->approverSignature = $request->signature;
            $report->approvedByID = Auth::id();
            $report->save();

            return response()->json([
                'success' => true,
                'message' => 'Tanda tangan berhasil disimpan'
            ]);
        } catch (\Exception $e) {
            Log::error('Error saving supervisor signature: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
