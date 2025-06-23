<?php

namespace App\Http\Controllers\DailyTest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Equipment;
use App\Models\Location;
use App\Models\Report;
use App\Models\ReportDetail;
use App\Models\ReportStatus;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class HhmdController extends Controller
{
    public function hhmdLayout()
    {
        // Ambil equipment HHMD
        $hhmdEquipment = Equipment::where('name', 'hhmd')->first();

        // Ambil lokasi yang terhubung dengan HHMD
        $hhmdLocations = [];
        if ($hhmdEquipment) {
            $hhmdLocations = $hhmdEquipment->locations()
                ->wherePivot('equipment_id', $hhmdEquipment->id)
                ->withPivot('description', 'id')
                ->get();
        }

        return view('daily-test.hhmdLayout', [
            'hhmdLocations' => $hhmdLocations
        ]);
    }

    public function get($id)
    {
        Log::info("Accessing HHMD review form with reportID: $id");

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

            // Validate if this is an HHMD report
            if ($equipmentLocationData->equipment_name !== 'hhmd') {
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

            return view('daily-test.review-form.hhmdReviewForm', [
                'form' => $report,
                // 'statuses' => $statuses,
                'supervisor' => $supervisor,
                'location' => $location,
                'equipment' => $equipment,
                'function' => 'update'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error("Report not found with reportID: $id");
            return redirect()->back()->with('error', 'Report tidak ditemukan');
        } catch (\Exception $e) {
            Log::error('Error in HHMD review form: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }


    public function checkLocation(Request $request)
    {
        $locationId = $request->input('location_id');

        // Validasi lokasi
        $location = Location::find($locationId);

        if (!$location) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lokasi tidak ditemukan'
            ], 404);
        }

        // Cek apakah lokasi terhubung dengan HHMD
        $hhmdEquipment = Equipment::where('name', 'hhmd')->first();

        if (!$hhmdEquipment) {
            return response()->json([
                'status' => 'error',
                'message' => 'Equipment HHMD tidak ditemukan'
            ], 404);
        }

        $isConnected = $hhmdEquipment->locations()
            ->where('locations.id', $locationId)
            ->exists();

        if (!$isConnected) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lokasi tidak terhubung dengan HHMD'
            ], 400);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Lokasi valid',
            'location' => $location
        ]);
    }

    public function store(Request $request)
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
            // Ambil equipment HHMD
            $hhmdEquipment = Equipment::where('name', 'hhmd')->first();

            if (!$hhmdEquipment) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Equipment HHMD tidak ditemukan'
                ], 404);
            }

            // Ambil equipment_locations_id
            $equipmentLocation = $hhmdEquipment->locations()
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
            $reportDetail->test1 = $request->test1 ? true : false;
            $reportDetail->testCondition1 = $request->testCondition1 ? true : false;
            $reportDetail->testCondition2 = $request->testCondition2 ? true : false;
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
        Log::info("Accessing HHMD review form with reportID: $id");

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

            // Validate if this is an HHMD report
            if ($equipmentLocationData->equipment_name !== 'hhmd') {
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

            return view('daily-test.review-form.hhmdReviewForm', [
                'form' => $report,
                'statuses' => $statuses,
                'supervisor' => $supervisor,
                'location' => $location,
                'equipment' => $equipment,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error("Report not found with reportID: $id");
            return redirect()->back()->with('error', 'Report tidak ditemukan');
        } catch (\Exception $e) {
            Log::error('Error in HHMD review form: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function showData()
    {
        // Ambil equipment HHMD
        $equipment = Equipment::whereIn('name', ['hhmd', 'wtmd', 'xraycabin', 'xraybagasi'])->get()->keyBy('name');

        $allReports = collect();

        foreach ($equipment as $equipmentType => $equipmentData) {
            $equipmentLocationIds = $equipmentData->locations()
                ->pluck('equipment_locations.id')
                ->toArray();

            if (empty($equipmentLocationIds)) {
                continue;
            }

            $reports = Report::query()
                ->join('equipment_locations', 'reports.equipmentLocationID', '=', 'equipment_locations.id')
                ->join('locations', 'equipment_locations.location_id', '=', 'locations.id')
                ->whereIn('reports.equipmentLocationID', $equipmentLocationIds)
                ->where('reports.approvedByID', Auth::id())
                ->with(['submittedBy', 'status'])
                ->select(
                    'reports.reportID as id',
                    'reports.testDate',
                    'reports.submittedByID',
                    'reports.statusID',
                    'locations.name as location_name'
                )
                ->orderBy('reports.created_at', 'desc')
                ->get()
                ->map(function ($report) use ($equipmentType) {
                    return [
                        'id' => $report->id,
                        'date' => $report->testDate->format('d/m/Y'),
                        'test_type' => strtoupper($equipmentType),
                        'location' => $report->location_name,
                        'status' => $report->status->name ?? 'pending',
                        'operator' => $report->submittedBy->name ?? 'Unknown',
                    ];
                });

            $allReports = $allReports->merge($reports);
        }

        // Urutkan berdasarkan tanggal
        $sortedReports = $allReports->sortByDesc(function ($report) {
            return \Carbon\Carbon::createFromFormat('d/m/Y', $report['date']);
        })->values();

        return view('supervisor.dailyTestForm', [
            'reports' => $sortedReports,
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            // Cari laporan berdasarkan ID
            $report = Report::findOrFail($id);

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
    public function show($id)
    {
        try {
            // Load report dengan relasi yang diperlukan
            $report = Report::with([
                'submittedBy',
                'approvedBy',
                'reviewedBy',
                'status',
                'reportDetails'
            ])->findOrFail($id);

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

            // Create objects for backward compatibility
            $equipment = (object) [
                'id' => $equipmentLocationData->equipment_id,
                'name' => $equipmentLocationData->equipment_name
            ];

            $location = (object) [
                'id' => $equipmentLocationData->location_id,
                'name' => $equipmentLocationData->location_name
            ];

            // Tentukan view berdasarkan jenis equipment
            $viewName = 'daily-test.show';
            if ($equipmentLocationData->equipment_name === 'hhmd') {
                $viewName = 'daily-test.show-hhmd';
            }

            return view($viewName, [
                'report' => $report,
                'location' => $location,
                'equipment' => $equipment
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error("Report not found with reportID: $id");
            return redirect()->back()->with('error', 'Report tidak ditemukan');
        } catch (\Exception $e) {
            Log::error('Error in show report: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function editRejectedReport($id)
    {
        try {
            // Load report dengan relasi yang diperlukan
            $report = Report::with([
                'submittedBy',
                'status',
                'reportDetails'
            ])->findOrFail($id);

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

            // Validate if this is an HHMD report
            if ($equipmentLocationData->equipment_name !== 'hhmd') {
                Log::warning("Invalid report type for reportID: $id. Equipment: " . $equipmentLocationData->equipment_name);
                return redirect()->back()->with('error', 'Invalid report type');
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

            return view('officer.editHhmd', [
                'form' => $report,
                'location' => $location,
                'equipment' => $equipment,
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
}
