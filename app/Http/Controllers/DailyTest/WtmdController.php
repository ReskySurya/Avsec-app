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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class WtmdController extends Controller
{
    public function wtmdLayout()
    {
        // Ambil equipment WTMD
        $wtmdEquipment = Equipment::where('name', 'wtmd')->first();


        $wtmdLocations = collect();

        if ($wtmdEquipment) {
            // Gunakan EquipmentLocation untuk mendapatkan data yang lebih lengkap
            $wtmdLocations = EquipmentLocation::where('equipment_id', $wtmdEquipment->id)
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
            'daily-test.wtmdLayout',
            [
                'wtmdLocations' => $wtmdLocations
            ]
        );
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
            // Ambil equipment_location berdasarkan location_id dan equipment WTMD
            $equipmentLocation = EquipmentLocation::whereHas('equipment', function ($query) {
                $query->where('name', 'wtmd');
            })
                ->where('location_id', $request->location)
                ->first();

            if (!$equipmentLocation) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Relasi equipment WTMD dan lokasi tidak ditemukan'
                ], 404);
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
            $reportDetail->test1_in_depan = $request->test1_in_depan ? true : false;
            $reportDetail->test1_out_depan = $request->test1_out_depan ? true : false;
            $reportDetail->test2_in_depan = $request->test2_in_depan ? true : false;
            $reportDetail->test2_out_depan = $request->test2_out_depan ? true : false;
            $reportDetail->test3_in_belakang = $request->test3_in_belakang ? true : false;
            $reportDetail->test3_out_belakang = $request->test3_out_belakang ? true : false;
            $reportDetail->test4_in_depan = $request->test4_in_depan ? true : false;
            $reportDetail->test4_out_depan = $request->test4_in_depan ? true : false;
            $reportDetail->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Report berhasil disimpan',
                'report' => $report
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'report' => $report,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reviewForm($id)
    {
        Log::info("Accessing WTMD review form with reportID: $id");

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

            // Validate if this is an WTMD report
            if ($equipmentLocationData->equipment_name !== 'wtmd') {
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

            return view('daily-test.review-form.wtmdReviewForm', [
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

    public function editRejectedReport($id)
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

            // Pastikan ini adalah laporan WTMD
            if ($report->equipmentLocation->equipment->name !== 'wtmd') {
                Log::warning("Invalid report type for reportID: $id. Equipment: " . $report->equipmentLocation->equipment->name);
                return redirect()->back()->with('error', 'Invalid report type');
            }

            // Ambil semua lokasi yang berelasi dengan equipment WTMD
            $wtmdEquipment = Equipment::where('name', 'wtmd')->first();
            $wtmdLocations = collect();
            if ($wtmdEquipment) {
                $wtmdLocations = EquipmentLocation::where('equipment_id', $wtmdEquipment->id)
                    ->with('location')
                    ->get()
                    ->map(function ($el) {
                        return $el->location;
                    })
                    ->unique('id');
            }

            // Ambil detail report
            $details = $report->reportDetails->first();
            return view('officer.editWtmd', [
                'form' => $report,
                'details' => $details,
                'wtmdLocations' => $wtmdLocations,
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
 
    
    public function update(Request $request, $id)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'testDateTime' => 'required|date',
            'location' => 'required|exists:locations,id',
            'deviceInfo' => 'required|string|max:255',
            'certificateInfo' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'terpenuhi' => 'boolean',
            'tidakterpenuhi' => 'boolean',
            'test1_in_depan' => 'boolean',
            'test1_out_depan' => 'boolean',
            'test2_in_depan' => 'boolean',
            'test2_out_depan' => 'boolean',
            'test3_in_belakang' => 'boolean',
            'test3_out_belakang' => 'boolean',
            'test4_in_depan' => 'boolean',
            'test4_out_depan' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            Log::info('Updating WTMD Report ID: ' . $id);
            Log::info('Request data:', $request->all());

            DB::beginTransaction();

            $report = Report::findOrFail($id);

            // Pastikan pengguna yang login adalah yang mengirim laporan
            if ($report->submittedByID !== Auth::id()) {
                return redirect()->route('dashboard.officer')->with('error', 'Anda tidak memiliki izin untuk mengedit laporan ini.');
            }

            $equipmentLocation = EquipmentLocation::whereHas('equipment', function ($query) {
                $query->where('name', 'wtmd');
            })
            ->where('location_id', $request->location)
            ->firstOrFail();

            // Ambil status 'pending'
            $pendingStatus = ReportStatus::where('name', 'pending')->firstOrFail();

            // Update report
            $report->testDate = $request->testDateTime;
            $report->equipmentLocationID = $equipmentLocation->id;
            $report->deviceInfo = $request->deviceInfo;
            $report->certificateInfo = $request->certificateInfo;
            $report->isFullFilled = $request->boolean('terpenuhi');
            $report->result = $request->result;
            $report->note = $request->note;
            $report->statusID = $pendingStatus->id;
            $report->approvalNote = null; // Hapus catatan penolakan sebelumnya
            $report->save();

            // Update report detail secara eksplisit
            $reportDetail = ReportDetail::where('reportID', $report->reportID)->first();
            if (!$reportDetail) {
                // Fallback jika detail tidak ditemukan, meskipun seharusnya tidak terjadi
                $reportDetail = new ReportDetail();
                $reportDetail->reportID = $report->reportID;
            }

            $reportDetail->terpenuhi = $request->boolean('terpenuhi');
            $reportDetail->tidakterpenuhi = $request->boolean('tidakterpenuhi');
            $reportDetail->test1_in_depan = $request->boolean('test1_in_depan');
            $reportDetail->test1_out_depan = $request->boolean('test1_out_depan');
            $reportDetail->test2_in_depan = $request->boolean('test2_in_depan');
            $reportDetail->test2_out_depan = $request->boolean('test2_out_depan');
            $reportDetail->test3_in_belakang = $request->boolean('test3_in_belakang');
            $reportDetail->test3_out_belakang = $request->boolean('test3_out_belakang');    
            $reportDetail->test4_in_depan = $request->boolean('test4_in_depan');
            $reportDetail->test4_out_depan = $request->boolean('test4_out_depan');
            $reportDetail->save();

            DB::commit();

            return redirect()->route('dashboard.officer')->with('success', 'Laporan berhasil diperbarui dan dikirim ulang untuk ditinjau.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating WTMD report: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui laporan: ' . $e->getMessage())->withInput();
        }
    }
}
