<?php

namespace App\Http\Controllers;

use App\Models\ChecklistKendaraan;
use App\Models\ChecklistPenyisiran;
use App\Models\FormPencatatanPI;
use App\Models\EquipmentLocation;
use App\Models\Location;
use App\Models\Report;
use App\Models\Logbook;
use App\Models\LogbookChief;
use App\Models\LogbookRotasi;
use App\Models\ManualBook;
use App\Models\ReportStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{

    public function showDataDailyTest(Request $request)
    {
        $equipmentTypes = ['hhmd', 'wtmd', 'xraycabin', 'xraybagasi'];

        // Jika user tidak mengirim parameter 'status', pakai 'pending' sebagai default
        $statusFilter = $request->has('status') ? $request->query('status') : 'pending';

        $query = Report::query()
            ->join('equipment_locations', 'reports.equipmentLocationID', '=', 'equipment_locations.id')
            ->join('equipment', 'equipment_locations.equipment_id', '=', 'equipment.id')
            ->join('locations', 'equipment_locations.location_id', '=', 'locations.id')
            ->whereIn('equipment.name', $equipmentTypes)
            ->whereIn('reports.statusID', [1, 2, 3])
            ->with(['submittedBy', 'status']);

        // Cek jika user bukan superadmin, maka filter berdasarkan approver
        if (!Auth::user()->isSuperAdmin()) {
            $query->where('reports.approvedByID', Auth::id());
        }

        // Filter hanya jika statusFilter tidak kosong
        if (!empty($statusFilter)) {
            $query->whereHas('status', function ($q) use ($statusFilter) {
                $q->where('name', $statusFilter);
            });
        }

        $reports = $query
            ->select(
                'reports.reportID as id',
                'reports.testDate',
                'reports.submittedByID',
                'reports.statusID',
                'locations.name as location_name',
                'equipment.name as equipment_name'
            )
            ->orderBy('reports.created_at', 'desc')
            ->get()
            ->map(function ($report) {
                return [
                    'id' => $report->id,
                    'date' => $report->testDate->format('d/m/Y'),
                    'test_type' => strtoupper($report->equipment_name),
                    'location' => $report->location_name,
                    'status' => $report->status->name ?? 'pending',
                    'operator' => $report->submittedBy->name ?? 'Unknown',
                ];
            });

        return view('supervisor.dailyTestForm', [
            'reports' => $reports,
            'defaultStatus' => $statusFilter, // dikirim ke view
        ]);
    }
    public function showDataLogbook(Request $request)
    {
        $statusFilter = $request->query('status', ''); // Default kosong untuk menampilkan semua

        $logbookQuery = Logbook::with(['locationArea', 'senderBy', 'receiverBy', 'approverBy'])
            ->whereIn('status', ['submitted', 'approved'])
            ->orderBy('date', 'desc');

        // Cek jika user bukan superadmin, maka filter berdasarkan approver
        if (!Auth::user()->isSuperAdmin()) {
            $logbookQuery->where('approvedID', Auth::id());
        }

        // Filter berdasarkan status jika dipilih
        if ($statusFilter) {
            $logbookQuery->where('status', $statusFilter);
        }


        $logbookEntries = $logbookQuery->get()
            ->map(function ($logbook) {
                return [
                    'id' => $logbook->logbookID,
                    'date' => $logbook->date->format('d/m/Y'),
                    'location' => $logbook->locationArea->name ?? '',
                    'group' => $logbook->grup,
                    'shift' => $logbook->shift,
                    'status' => $logbook->status, // Status asli dari database
                    'sender' => $logbook->senderBy->name ?? '',
                    'receiver' => $logbook->receiverBy->name ?? '',
                    'approver' => $logbook->approverBy->name ?? '',
                    'sender_signature' => $logbook->sender_signature ?? null,
                    'received_signature' => $logbook->received_signature ?? null,
                    'approved_signature' => $logbook->approved_signature ?? null,
                ];
            });

        return view('supervisor.logbookForm', [
            'logbookEntries' => $logbookEntries,
            'defaultStatus' => $statusFilter,
        ]);
    }

    public function index()
    {
        // Ambil ID user yang sedang login
        $currentUserId = Auth::id();

        $rejectedlogbooks = Logbook::with('locationArea')
            ->where('senderID', $currentUserId)
            ->whereNotNull('senderSignature') // Changed to check if senderSignature is not null
            ->where('status', 'draft') // Hanya tampilkan logbook dengan status draft
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $locations = Location::all();
        // ->get();
        $rejectedReports = Report::rejected()
            ->where('submittedByID', Auth::id())
            ->with([
                'status:id,name',
                'equipmentLocation.location:id,name',
                'equipmentLocation.equipment:id,name',
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        $logbookEntries = Logbook::where('receivedID', Auth::id())
            ->whereNull('receivedSignature')
            ->with([
                'locationArea:id,name',
                'senderBy:id,name',
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        $checklistKendaraan = ChecklistKendaraan::where('received_id', Auth::id())
            ->whereNull('receivedSignature')
            ->with(['sender']) // <-- UBAH INI: Muat relasi sender
            ->orderBy('created_at', 'desc')
            ->get();

        $checklistPenyisiran = ChecklistPenyisiran::where('received_id', Auth::id())
            ->whereNull('receivedSignature')
            ->with(['sender']) // <-- UBAH INI: Muat relasi sender
            ->orderBy('created_at', 'desc')
            ->get();



        return view('officer.dashboardOfficer', [
            'rejectedlogbooks' => $rejectedlogbooks,
            'locations' => $locations,
            'rejectedReports' => $rejectedReports,
            'logbookEntries' => $logbookEntries,
            'checklistKendaraan' => $checklistKendaraan,
            'checklistPenyisiran' => $checklistPenyisiran,
        ]);
    }

    public function showDataLogbookRotasi(Request $request)
    {
        // 1. Tentukan jumlah item per halaman untuk pagination
        $status = $request->get('status', ''); // Default kosong untuk menampilkan semua

        $perPage = 10;

        // 2. Query dasar untuk LogbookRotasi
        $pscpQuery = LogbookRotasi::with('creator', 'approver')->where('type', 'pscp');
        $hbscpQuery = LogbookRotasi::with('creator', 'approver')->where('type', 'hbscp');

        // 3. Filter berdasarkan approver jika bukan superadmin
        if (!Auth::user()->isSuperAdmin()) {
            $pscpQuery->where('approved_by', Auth::id());
            $hbscpQuery->where('approved_by', Auth::id());
        }

        // Filter berdasarkan status jika dipilih
        if ($status) {
            $pscpQuery->where('status', $status);
            $hbscpQuery->where('status', $status);
        }

        // 4. Ambil data dengan pagination
        $logbooksPSCP = $pscpQuery->latest('date')->paginate($perPage, ['*'], 'pscp_page');
        $logbooksHBSCP = $hbscpQuery->latest('date')->paginate($perPage, ['*'], 'hbscp_page');

        // 5. Kirim kedua koleksi data ke view
        return view('supervisor.listLogbookRotasi',
        compact(
            'logbooksPSCP',
             'logbooksHBSCP',
        ));
    }

    public function showDataChecklistKendaraan(Request $request)
    {
        $perPage = 10;
        $status = $request->query('status'); // 1. Ambil nilai 'status' dari URL

        // --- Query untuk Mobil ---
        // 2. Mulai bangun query untuk mobil
        $queryMobil = ChecklistKendaraan::with('sender')->where('type', 'mobil');

        // 3. Terapkan filter status jika ada
        if ($status) {
            $queryMobil->where('status', $status);
        }

        // 4. Eksekusi query mobil dengan urutan dan pagination
        $checklistsMobil = $queryMobil->latest('date')
            ->paginate($perPage, ['*'], 'mobil_page');

        // --- Query untuk Motor ---
        // 2. Mulai bangun query untuk motor
        $queryMotor = ChecklistKendaraan::with('sender')->where('type', 'motor');

        // 3. Terapkan filter status yang sama jika ada
        if ($status) {
            $queryMotor->where('status', $status);
        }

        // 4. Eksekusi query motor dengan urutan dan pagination
        $checklistsMotor = $queryMotor->latest('date')
            ->paginate($perPage, ['*'], 'motor_page');

        // 5. (PENTING) Agar link pagination di kedua tabel mengingat filter
        $checklistsMobil->appends($request->query());
        $checklistsMotor->appends($request->query());

        return view('supervisor.listChecklistKendaraan', compact('checklistsMobil', 'checklistsMotor'));
    }

    public function showDataChecklistPenyisiran(Request $request)
    {
        $perPage = 10;
        $status = $request->query('status'); // Ambil nilai 'status' dari URL

        // Mulai bangun query tanpa langsung mengeksekusinya
        $query = ChecklistPenyisiran::with('sender');

        // Tambahkan filter HANYA JIKA parameter 'status' ada di URL
        if ($status) {
            $query->where('status', $status);
        }

        // Setelah filter diterapkan, baru urutkan dan lakukan pagination
        $checklistsPenyisiran = $query->latest('date')
            ->paginate($perPage);

        // Penting: Agar link pagination tetap membawa filter status
        $checklistsPenyisiran->appends($request->query());

        return view('supervisor.listChecklistPenyisiran', compact('checklistsPenyisiran'));
    }

    public function showDataManualBook(Request $request)
    {
        $status = $request->get('status');

        // Query dasar untuk HBSCP
        $queryHBSCP = ManualBook::with('creator', 'details')
            ->where('approved_by', Auth::id())
            ->where('type', 'hbscp');

        // Query dasar untuk PSCP
        $queryPSCP = ManualBook::with('creator', 'details')
            ->where('approved_by', Auth::id())
            ->where('type', 'pscp');

        // Terapkan filter status jika ada, dengan melanjutkan query yang sudah ada
        if ($status) {
            $queryHBSCP->where('status', $status);
            $queryPSCP->where('status', $status);
        }

        $perPage = 10;

        // Eksekusi query HBSCP yang sudah difilter (jika ada) dan tambahkan pagination
        $manualBooksHBSCP = $queryHBSCP->latest('date')
            ->paginate($perPage, ['*'], 'hbscp_page');

        // Eksekusi query PSCP yang sudah difilter (jika ada) dan tambahkan pagination
        $manualBooksPSCP = $queryPSCP->latest('date')
            ->paginate($perPage, ['*'], 'pscp_page');

        if (!Auth::user()->isSuperAdmin()) {
            $manualBooksHBSCP->where('approved_by', Auth::id());
            $manualBooksPSCP->where('approved_by', Auth::id());
        }
        return view('supervisor.listManualBook', compact('manualBooksHBSCP', 'manualBooksPSCP'));
    }

    public function showDataFormPencatatanPI(Request $request)
    {
        $perPage = 10;
        $status = $request->query('status'); // 1. Ambil nilai 'status' dari URL

        // 2. Mulai membangun query, jangan langsung dieksekusi dengan paginate()
        $query = FormPencatatanPI::where('approved_id', Auth::id());

        // 3. Tambahkan filter status HANYA JIKA parameter 'status' ada di URL
        if ($status) {
            $query->where('status', $status);
        }

        // 4. Setelah semua filter diterapkan, baru eksekusi query dengan urutan dan pagination
        $formPencatatanPI = $query->latest('date')
            ->paginate($perPage);

        // 5. (PENTING) Agar link pagination mengingat filter yang sedang aktif
        $formPencatatanPI->appends($request->query());

        return view('supervisor.listFormPencatatanPI', compact('formPencatatanPI'));
    }


    public function indexSupervisor()
    {
        $perPage = 10;

        $logbooksChief = LogbookChief::with('createdBy', 'approvedBy')
            ->where('status', 'submitted')
            ->where('approved_by', Auth::id())
            ->latest('date')
            ->paginate($perPage);

        return view('supervisor.dashboardSupervisor', compact('logbooksChief'));
    }

    public function indexSuperadmin()
    {
        // Daily Test Stats
        $dailyTestStats = [];
        $equipmentTypes = ['hhmd', 'wtmd', 'xraycabin', 'xraybagasi'];
        $approvedStatus = ReportStatus::where('name', 'approved')->first();
        $approvedStatusId = $approvedStatus ? $approvedStatus->id : null;

        foreach ($equipmentTypes as $type) {
            $totalLocationsForType = \App\Models\EquipmentLocation::whereHas('equipment', function ($query) use ($type) {
                $query->where('name', $type);
            })->count();

            $reportsToday = Report::with('equipmentLocation.location')
                ->whereHas('equipmentLocation.equipment', function ($query) use ($type) {
                    $query->where('name', $type);
                })
                ->whereDate('created_at', today())
                ->get();

            $reportsByLocation = $reportsToday->groupBy('equipmentLocation.location.name');

            $locationDetails = [];
            foreach ($reportsByLocation as $locationName => $reports) {
                $locationDetails[$locationName ?: 'Tanpa Lokasi'] = [
                    'total' => $reports->count(),
                    'approved' => $approvedStatusId ? $reports->where('statusID', $approvedStatusId)->count() : 0,
                ];
            }

            $submittedReports = $reportsToday->count();

            $key = ucwords(str_replace(['xraycabin', 'xraybagasi'], ['X-Ray Cabin', 'X-Ray Bagasi'], $type));
            if ($type === 'hhmd' || $type === 'wtmd') $key = strtoupper($type);

            $dailyTestStats[$key] = [
                'total' => $totalLocationsForType,
                'approved' => $submittedReports,
                'percentage' => ($totalLocationsForType > 0) ? round(($submittedReports / $totalLocationsForType) * 100) : 0,
                'breakdown' => $locationDetails,
                'breakdownTitle' => 'Lokasi'
            ];
        }

        // Logbook Stats
        $logbookStats = [];

        $totalLocations = Location::count();
        $logbooksToday = Logbook::with('locationArea')->whereIn('status', ['submitted', 'approved'])->whereDate('created_at', today())->get();
        $logbooksByLocation = $logbooksToday->groupBy('locationArea.name');
        $locationDetailsPosJaga = [];
        foreach ($logbooksByLocation as $locationName => $logbooks) {
            $locationDetailsPosJaga[$locationName ?: 'Tanpa Lokasi'] = ['total' => $logbooks->count(), 'approved' => $logbooks->where('status', 'approved')->count()];
        }
        $submittedPosJaga = $logbooksToday->count();
        $logbookStats['Pos Jaga'] = [
            'total' => $totalLocations,
            'approved' => $submittedPosJaga,
            'percentage' => ($totalLocations > 0) ? round(($submittedPosJaga / $totalLocations) * 100) : 0,
            'breakdown' => $locationDetailsPosJaga,
            'breakdownTitle' => 'Lokasi'
        ];

        $rotasiToday = LogbookRotasi::whereIn('status', ['submitted', 'approved'])->whereDate('created_at', today())->get();
        $rotasiByType = $rotasiToday->groupBy('type');
        $typeDetailsRotasi = [];
        foreach ($rotasiByType as $typeName => $logbooks) {
            $typeDetailsRotasi[strtoupper($typeName)] = ['total' => $logbooks->count(), 'approved' => $logbooks->where('status', 'approved')->count()];
        }
        $totalRotasi = $rotasiToday->count();
        $approvedRotasi = $rotasiToday->where('status', 'approved')->count();
        $logbookStats['Rotasi'] = ['total' => $totalRotasi, 'approved' => $approvedRotasi, 'percentage' => ($totalRotasi > 0) ? round(($approvedRotasi / $totalRotasi) * 100) : 0, 'breakdown' => $typeDetailsRotasi, 'breakdownTitle' => 'Tipe'];

        $totalChief = LogbookChief::whereNotNull('senderSignature')->whereDate('created_at', today())->count();
        $approvedChief = LogbookChief::whereNotNull('approvedSignature')->whereDate('created_at', today())->count();
        $logbookStats['Laporan Chief'] = ['total' => $totalChief, 'approved' => $approvedChief, 'percentage' => ($totalChief > 0) ? round(($approvedChief / $totalChief) * 100) : 0, 'breakdown' => [], 'breakdownTitle' => ''];

        // Checklist Stats
        $checklistStats = [];

        $kendaraanToday = ChecklistKendaraan::whereIn('status', ['submitted', 'approved'])->whereDate('created_at', today())->get();
        $kendaraanByType = $kendaraanToday->groupBy('type');
        $typeDetailsKendaraan = [];
        foreach ($kendaraanByType as $typeName => $checklists) {
            $typeDetailsKendaraan[ucfirst($typeName)] = ['total' => $checklists->count(), 'approved' => $checklists->where('status', 'approved')->count()];
        }
        $totalKendaraan = $kendaraanToday->count();
        $approvedKendaraan = $kendaraanToday->where('status', 'approved')->count();
        $checklistStats['Kendaraan'] = ['total' => $totalKendaraan, 'approved' => $approvedKendaraan, 'percentage' => ($totalKendaraan > 0) ? round(($approvedKendaraan / $totalKendaraan) * 100) : 0, 'breakdown' => $typeDetailsKendaraan, 'breakdownTitle' => 'Tipe Kendaraan'];

        $penyisiranToday = ChecklistPenyisiran::whereIn('status', ['submitted', 'approved'])->whereDate('created_at', today())->get();
        $penyisiranByGrup = $penyisiranToday->groupBy('grup');
        $grupDetailsPenyisiran = [];
        foreach ($penyisiranByGrup as $grupName => $checklists) {
            $grupDetailsPenyisiran['Grup ' . $grupName] = ['total' => $checklists->count(), 'approved' => $checklists->where('status', 'approved')->count()];
        }
        $totalPenyisiran = $penyisiranToday->count();
        $approvedPenyisiran = $penyisiranToday->where('status', 'approved')->count();
        $checklistStats['Penyisiran'] = ['total' => $totalPenyisiran, 'approved' => $approvedPenyisiran, 'percentage' => ($totalPenyisiran > 0) ? round(($approvedPenyisiran / $totalPenyisiran) * 100) : 0, 'breakdown' => $grupDetailsPenyisiran, 'breakdownTitle' => 'Grup'];

        $piToday = FormPencatatanPI::whereIn('status', ['submitted', 'approved'])->whereDate('created_at', today())->get();
        $piByGrup = $piToday->groupBy('grup');
        $grupDetailsPI = [];
        foreach ($piByGrup as $grupName => $forms) {
            $grupDetailsPI['Grup ' . $grupName] = ['total' => $forms->count(), 'approved' => $forms->where('status', 'approved')->count()];
        }
        $totalPI = $piToday->count();
        $approvedPI = $piToday->where('status', 'approved')->count();
        $checklistStats['Pencatatan PI'] = ['total' => $totalPI, 'approved' => $approvedPI, 'percentage' => ($totalPI > 0) ? round(($approvedPI / $totalPI) * 100) : 0, 'breakdown' => $grupDetailsPI, 'breakdownTitle' => 'Grup'];

        $manualBookToday = ManualBook::whereIn('status', ['submitted', 'approved'])->whereDate('created_at', today())->get();
        $manualBookByType = $manualBookToday->groupBy('type');
        $typeDetailsManualBook = [];
        foreach ($manualBookByType as $typeName => $books) {
            $typeDetailsManualBook[strtoupper($typeName)] = ['total' => $books->count(), 'approved' => $books->where('status', 'approved')->count()];
        }
        $totalManualBook = $manualBookToday->count();
        $approvedManualBook = $manualBookToday->where('status', 'approved')->count();
        $checklistStats['Manual Book'] = ['total' => $totalManualBook, 'approved' => $approvedManualBook, 'percentage' => ($totalManualBook > 0) ? round(($approvedManualBook / $totalManualBook) * 100) : 0, 'breakdown' => $typeDetailsManualBook, 'breakdownTitle' => 'Tipe'];

        return view('superadmin.dashboardSuperadmin', [
            'dailyTestStats' => $dailyTestStats,
            'logbookStats' => $logbookStats,
            'checklistStats' => $checklistStats
        ]);
    }
}
