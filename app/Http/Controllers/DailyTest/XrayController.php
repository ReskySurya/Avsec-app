<?php

namespace App\Http\Controllers\DailyTest;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
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
     * Display the X-Ray Bagasi layout.
     *
     * @return \Illuminate\View\View
     */

    public function xrayBagasiLayout()
    {
        // Ambil equipment X-Ray Bagasi
        $xrayBagasiEquipment = Equipment::where('name', 'xraybagasi')->first();

        // Ambil lokasi yang terhubung dengan X-Ray Bagasi
        $xrayBagasiLocations = [];
        if ($xrayBagasiEquipment) {
            $xrayBagasiLocations = $xrayBagasiEquipment->locations()
                ->wherePivot('equipment_id', $xrayBagasiEquipment->id)
                ->withPivot('description', 'id')
                ->get();
        }
        return view(
            'daily-test.xrayBagasiLayout',
            [
                'xrayBagasiLocations' => $xrayBagasiLocations,
                'type' => 'xrayBagasi'
            ]
        );
    }

    /**
     * Display the X-Ray Cabin layout.
     *
     * @return \Illuminate\View\View
     */

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
            // Ambil equipment Xray Cabin
            $xrayCabinEquipment = Equipment::where('name', 'xraycabin')->first();

            if (!$xrayCabinEquipment) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Equipment Xray tidak ditemukan'
                ], 404);
            }

            // Ambil equipment_locations_id
            $equipmentLocation = $xrayCabinEquipment->locations()
                ->where('locations.id', $request->location)
                ->first();

            if (!$equipmentLocation) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Relasi equipment dan lokasi tidak ditemukan'
                ], 404);
            }

            $equipmentLocationId = $equipmentLocation->pivot->id;

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
            $report->equipmentLocationID = $equipmentLocationId;
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
            $reportDetail->test1aab_32 = $request->test1aab_32 ? true : false;
            $reportDetail->test1aab_30 = $request->test1aab_30 ? true : false;
            $reportDetail->test1aab_24 = $request->test1aab_24 ? true : false;
            $reportDetail->test1bab_30_1 = $request->test1bab_30_1 ? true : false;
            $reportDetail->test1bab_24_1 = $request->test1bab_24_1 ? true : false;
            $reportDetail->test1bab_30_2 = $request->test1bab_30_2 ? true : false;
            $reportDetail->test1bab_24_2 = $request->test1bab_24_2 ? true : false;
            $reportDetail->test1bab_24_3 = $request->test1bab_24_3 ? true : false;
            $reportDetail->test1ab_32 = $request->test1ab_32 ? true : false;
            $reportDetail->test1ab_30 = $request->test1ab_30 ? true : false;
            $reportDetail->test1ab_24 = $request->test1ab_24 ? true : false;
            $reportDetail->test1bb_30_1 = $request->test1bb_30_1 ? true : false;
            $reportDetail->test1bb_24_1 = $request->test1bb_24_1 ? true : false;
            $reportDetail->test1bb_30_2 = $request->test1bb_30_2 ? true : false;
            $reportDetail->test1bb_24_2 = $request->test1bb_24_2 ? true : false;
            $reportDetail->test1bb_24_3 = $request->test1bb_24_3 ? true : false;
            $reportDetail->test2aab = $request->test2aab ? true : false;
            $reportDetail->test2bab = $request->test2bab ? true : false;
            $reportDetail->test2ab = $request->test2ab ? true : false;
            $reportDetail->test2bb = $request->test2bb ? true : false;
            $reportDetail->test3ab_14 = $request->test3ab_14 ? true : false;
            $reportDetail->test3ab_16 = $request->test3ab_16 ? true : false;
            $reportDetail->test3ab_18 = $request->test3ab_18 ? true : false;
            $reportDetail->test3ab_20 = $request->test3ab_20 ? true : false;
            $reportDetail->test3ab_22 = $request->test3ab_22 ? true : false;
            $reportDetail->test3ab_24 = $request->test3ab_24 ? true : false;
            $reportDetail->test3b_14 = $request->test3b_14 ? true : false;
            $reportDetail->test3b_16 = $request->test3b_16 ? true : false;
            $reportDetail->test3b_18 = $request->test3b_18 ? true : false;
            $reportDetail->test3b_20 = $request->test3b_20 ? true : false;
            $reportDetail->test3b_22 = $request->test3b_22 ? true : false;
            $reportDetail->test3b_24 = $request->test3b_24 ? true : false;
            $reportDetail->test4ab_h15mm = $request->test4ab_h15mm ? true : false;
            $reportDetail->test4ab_v15mm = $request->test4ab_v15mm ? true : false;
            $reportDetail->test4ab_h20mm = $request->test4ab_h20mm ? true : false;
            $reportDetail->test4ab_v20mm = $request->test4ab_v20mm ? true : false;
            $reportDetail->test4b_h15mm = $request->test4b_h15mm ? true : false;
            $reportDetail->test4b_v15mm = $request->test4b_v15mm ? true : false;
            $reportDetail->test4b_h20mm = $request->test4b_h20mm ? true : false;
            $reportDetail->test4b_v20mm = $request->test4b_v20mm ? true : false;
            $reportDetail->test5ab_05mm = $request->test5ab_05mm ? true : false;
            $reportDetail->test5b_05mm = $request->test5b_05mm ? true : false;
            $reportDetail->test5ab_10mm = $request->test5ab_10mm ? true : false;
            $reportDetail->test5b_10mm = $request->test5b_10mm ? true : false;
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

    // Check Location untuk X-Ray Bagasi dan X-Ray Cabin
    public function checkLocation(Request $request)
    {
        $locationId = $request->input('location_id');
        $type = $request->input('type'); // Tidak ada default, harus dikirim dari request

        // Validasi lokasi
        $location = Location::find($locationId);

        if (!$location) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lokasi tidak ditemukan'
            ], 404);
        }

        // Jika type tidak dikirim, cek ke dua equipment
        if (empty($type)) {
            $equipments = ['xraybagasi', 'xraycabin'];
            $found = false;
            foreach ($equipments as $eq) {
                $xrayEquipment = Equipment::where('name', $eq)->first();
                if ($xrayEquipment && $xrayEquipment->locations()->where('locations.id', $locationId)->exists()) {
                    $found = $eq;
                    break;
                }
            }
            if ($found) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Lokasi valid dan terhubung dengan ' . ucfirst($found),
                    'location' => $location,
                    'equipment' => $found
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Lokasi tidak terhubung dengan Xray Bagasi maupun Xray Cabin'
                ], 400);
            }
        }

        // Jika type dikirim, cek sesuai type
        if (!in_array($type, ['xraybagasi', 'xraycabin'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tipe equipment tidak valid'
            ], 400);
        }

        $xrayEquipment = Equipment::where('name', $type)->first();

        if (!$xrayEquipment) {
            return response()->json([
                'status' => 'error',
                'message' => 'Equipment tidak ditemukan'
            ], 404);
        }

        $isConnected = $xrayEquipment->locations()
            ->where('locations.id', $locationId)
            ->exists();

        if (!$isConnected) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lokasi tidak terhubung dengan ' . ucfirst($type)
            ], 400);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Lokasi valid',
            'location' => $location,
            'equipment' => $type
        ]);
    }


    public function xrayCabinLayout()
    {
        // Ambil equipment X-Ray Cabin
        $xrayCabinEquipment = Equipment::where('name', 'xraycabin')->first();

        // Ambil lokasi yang terhubung dengan X-Ray Bagasi
        $xrayCabinLocations = [];
        if ($xrayCabinEquipment) {
            $xrayCabinLocations = $xrayCabinEquipment->locations()
                ->wherePivot('equipment_id', $xrayCabinEquipment->id)
                ->withPivot('description', 'id')
                ->get();
        }
        return view('daily-test.xrayCabinLayout', [
            'xrayCabinLocations' => $xrayCabinLocations,
            'type' => 'xrayCabin'
        ]);
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
            // Ambil equipment Xray Cabin
            $xrayBagasiEquipment = Equipment::where('name', 'xraybagasi')->first();

            if (!$xrayBagasiEquipment) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Equipment XRay tidak ditemukan'
                ], 404);
            }

            // Ambil equipment_locations_id
            $equipmentLocation = $xrayBagasiEquipment->locations()
                ->where('locations.id', $request->location)
                ->first();

            if (!$equipmentLocation) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Relasi equipment dan lokasi tidak ditemukan'
                ], 404);
            }

            $equipmentLocationId = $equipmentLocation->pivot->id;

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
            $report->equipmentLocationID = $equipmentLocationId;
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
            $reportDetail->test1aab_30 = $request->test1aab_30 ? true : false; //
            $reportDetail->test1aab_24 = $request->test1aab_24 ? true : false; //
            $reportDetail->test1bab_30_1 = $request->test1bab_30_1 ? true : false; //
            $reportDetail->test1bab_24_1 = $request->test1bab_24_1 ? true : false; //
            $reportDetail->test1bab_24_2 = $request->test1bab_24_2 ? true : false; // 
            $reportDetail->test1bab_24_3 = $request->test1bab_24_3 ? true : false; //
            $reportDetail->test1ab_30 = $request->test1ab_30 ? true : false; //
            $reportDetail->test1ab_24 = $request->test1ab_24 ? true : false; //
            $reportDetail->test1bb_30_1 = $request->test1bb_30_1 ? true : false; //
            $reportDetail->test1bb_24_1 = $request->test1bb_24_1 ? true : false; //
            $reportDetail->test1bb_24_2 = $request->test1bb_24_2 ? true : false; // 
            $reportDetail->test1bb_24_3 = $request->test1bb_24_3 ? true : false; //
            $reportDetail->test2aab = $request->test2aab ? true : false; //
            $reportDetail->test2bab = $request->test2bab ? true : false; //
            $reportDetail->test2ab = $request->test2ab ? true : false; //
            $reportDetail->test2bb = $request->test2bb ? true : false; //
            $reportDetail->test3ab_14 = $request->test3ab_14 ? true : false; //
            $reportDetail->test3ab_16 = $request->test3ab_16 ? true : false; //
            $reportDetail->test3ab_18 = $request->test3ab_18 ? true : false; //
            $reportDetail->test3ab_20 = $request->test3ab_20 ? true : false; //
            $reportDetail->test3ab_22 = $request->test3ab_22 ? true : false; //
            $reportDetail->test3b_14 = $request->test3b_14 ? true : false; //
            $reportDetail->test3b_16 = $request->test3b_16 ? true : false; //
            $reportDetail->test3b_18 = $request->test3b_18 ? true : false; //
            $reportDetail->test3b_20 = $request->test3b_20 ? true : false; //
            $reportDetail->test3b_22 = $request->test3b_22 ? true : false; //
            $reportDetail->test4ab_h20mm = $request->test4ab_h20mm ? true : false; //
            $reportDetail->test4ab_v20mm = $request->test4ab_v20mm ? true : false; //
            $reportDetail->test4b_h20mm = $request->test4b_h20mm ? true : false; //
            $reportDetail->test4b_v20mm = $request->test4b_v20mm ? true : false; //
            $reportDetail->test5ab_10mm = $request->test5ab_10mm ? true : false; //
            $reportDetail->test5b_10mm = $request->test5b_10mm ? true : false; //
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


}
