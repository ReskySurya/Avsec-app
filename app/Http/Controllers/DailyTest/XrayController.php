<?php

namespace App\Http\Controllers\DailyTest;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\Location;
use App\Models\Report;
use App\Models\ReportDetail;
use App\Models\ReportStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
                'xrayBagasiLocations' => $xrayBagasiLocations
            ]
        );
    }

    /**
     * Display the X-Ray Cabin layout.
     *
     * @return \Illuminate\View\View
     */

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
                    'message' => 'Equipment HHMD tidak ditemukan'
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
}
