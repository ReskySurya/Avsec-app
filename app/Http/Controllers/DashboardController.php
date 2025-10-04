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
use App\Models\LogbookSweepingPI;
use App\Models\LogbookSweepingPIDetail;
use App\Models\ManualBook;
use App\Models\ReportStatus;
use App\Models\Tenant;
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
            ->whereIn('status', ['submitted', 'approved', 'draft'])
            ->orderBy('date', 'desc');

        // Cek jika user bukan superadmin, maka filter berdasarkan approver
        if (!Auth::user()->isSuperAdmin()) {
            $currentUserId = Auth::id();

            $logbookQuery->where(function ($query) use ($currentUserId) {
                $query->where('status', 'draft')
                    ->orWhere('approvedID', $currentUserId);
            });
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

        // Daily Test Submission Status
        $equipmentTypes = ['hhmd', 'wtmd', 'xraycabin', 'xraybagasi'];
        $allDailyTestLocations = EquipmentLocation::with(['equipment', 'location'])
            ->whereHas('equipment', function ($q) use ($equipmentTypes) {
                $q->whereIn('name', $equipmentTypes);
            })
            ->get();

        $submittedLocationIds = Report::whereIn('equipmentLocationID', $allDailyTestLocations->pluck('id'))
            ->whereDate('created_at', today())
            ->pluck('equipmentLocationID')
            ->unique();

        $dailyTestStatuses = [
            'submitted' => [],
            'not_submitted' => [],
        ];

        foreach ($allDailyTestLocations as $location) {
            $equipmentName = $location->equipment->name;
            $formLink = '#'; // Default link
            if (in_array($equipmentName, ['hhmd', 'wtmd', 'xraycabin', 'xraybagasi'])) {
                $formLink = route('daily-test.' . $equipmentName);
            }

            $statusData = [
                'equipment_name' => $equipmentName,
                'location_name' => $location->location->name,
                'form_link' => $formLink,
            ];

            if ($submittedLocationIds->contains($location->id)) {
                $dailyTestStatuses['submitted'][] = $statusData;
            } else {
                $dailyTestStatuses['not_submitted'][] = $statusData;
            }
        }

        // Checklist Submission Status
        $checklistStatuses = [
            'submitted' => [],
            'not_submitted' => [],
        ];

        // 1. Checklist Kendaraan
        $kendaraanTasks = [
            ['type' => 'mobil', 'shift' => 'pagi'],
            ['type' => 'mobil', 'shift' => 'malam'],
            ['type' => 'motor', 'shift' => 'pagi'],
            ['type' => 'motor', 'shift' => 'malam'],
        ];
        $submittedKendaraan = ChecklistKendaraan::whereDate('date', today())->get()->keyBy(function ($item) {
            return $item->type . '-' . $item->shift;
        });

        foreach ($kendaraanTasks as $task) {
            $taskKey = $task['type'] . '-' . $task['shift'];
            $taskName = 'Checklist Kendaraan ' . ucfirst($task['type']) . ' (' . ucfirst($task['shift']) . ')';

            if ($submittedKendaraan->has($taskKey)) {
                $checklistStatuses['submitted'][] = ['name' => $taskName];
            } else {
                $checklistStatuses['not_submitted'][] = [
                    'name' => $taskName,
                    'form_link' => route('checklist.kendaraan.index')
                ];
            }
        }

        // 2. Checklist Penyisiran
        $penyisiranSubmitted = ChecklistPenyisiran::whereDate('date', today())->exists();
        $penyisiranTaskName = 'Checklist Penyisiran Ruang Tunggu';

        if ($penyisiranSubmitted) {
            $checklistStatuses['submitted'][] = ['name' => $penyisiranTaskName];
        } else {
            $checklistStatuses['not_submitted'][] = [
                'name' => $penyisiranTaskName,
                'form_link' => route('checklist.penyisiran.index')
            ];
        }

        // 3. Form Pencatatan PI
        $piSubmitted = FormPencatatanPI::whereDate('date', today())->exists();
        $piTaskName = 'Form Pencatatan PI';

        if ($piSubmitted) {
            $checklistStatuses['submitted'][] = ['name' => $piTaskName];
        } else {
            $checklistStatuses['not_submitted'][] = [
                'name' => $piTaskName,
                'form_link' => route('checklist.pencatatanpi.index')
            ];
        }

        // Logbook Sweeping PI Status
        $sweepingStatuses = [
            'submitted' => [],
            'not_submitted' => [],
        ];
        $allTenants = Tenant::all();
        $dayOfMonth = today()->day;
        $dateField = 'tanggal_' . $dayOfMonth;

        $logbookIdsThisMonth = LogbookSweepingPI::where('bulan', today()->month)
            ->where('tahun', today()->year)
            ->pluck('sweepingpiID');

        $sweepingPiIdsCheckedToday = LogbookSweepingPIDetail::whereIn('sweepingpiID', $logbookIdsThisMonth)
            ->where($dateField, 1)
            ->pluck('sweepingpiID')
            ->unique();

        $tenantIdsCheckedToday = LogbookSweepingPI::whereIn('sweepingpiID', $sweepingPiIdsCheckedToday)
            ->pluck('tenantID')
            ->unique();

        foreach ($allTenants as $tenant) {
            $taskName = 'Logbook Sweeping PI - ' . $tenant->tenant_name;

            if ($tenantIdsCheckedToday->contains($tenant->tenantID)) {
                $sweepingStatuses['submitted'][] = ['name' => $taskName];
            } else {
                $sweepingStatuses['not_submitted'][] = [
                    'name' => $taskName,
                    'form_link' => route('logbookSweppingPI.detail.index', ['tenantID' => $tenant->tenantID])
                ];
            }
        }

        $rejectedlogbooks = Logbook::with('locationArea')
            ->where('status', 'draft') // It's a draft
            ->whereNotNull('senderSignature') // It's a rejected one
            ->where(function ($query) use ($currentUserId) {
                $query->where('senderID', $currentUserId) // User is the sender
                    ->orWhereHas('personil', function ($subQuery) use ($currentUserId) { // Or user is in the staff list
                        $subQuery->where('staffID', $currentUserId);
                    });
            })
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

        $draftLogbooks = Logbook::with('locationArea')
            ->where('status', 'draft') // It's a draft
            ->whereNull('senderSignature') // It's a new one
            ->where(function ($query) use ($currentUserId) {
                $query->where('senderID', $currentUserId) // User is the sender
                    ->orWhereHas('personil', function ($subQuery) use ($currentUserId) { // Or user is in the staff list
                        $subQuery->where('staffID', $currentUserId);
                    });
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $draftLogbookRotasi = LogbookRotasi::where('created_by', $currentUserId)
            ->where('status', 'draft')
            ->orderBy('created_at', 'desc')
            ->get();

        $draftManualBooks = ManualBook::where('created_by', $currentUserId)
            ->where('status', 'draft')
            ->orderBy('created_at', 'desc')
            ->get();




        $logbookSubmissionStatuses = [
            'submitted' => [],
        ];

        // Logbook Pos Jaga
        $submittedLogbookPosJaga = Logbook::where(function ($query) use ($currentUserId) {
            $query->where('senderID', $currentUserId)
                ->orWhereHas('personil', function ($subQuery) use ($currentUserId) {
                    $subQuery->where('staffID', $currentUserId);
                });
        })
            ->whereIn('status', ['submitted', 'approved'])
            ->whereDate('created_at', today())
            ->with('locationArea')
            ->get();

        foreach ($submittedLogbookPosJaga as $logbook) {
            $logbookSubmissionStatuses['submitted'][] = [
                'name' => 'Logbook Pos Jaga - ' . ($logbook->locationArea->name ?? 'Tanpa Lokasi'),
            ];
        }

        // Logbook Rotasi
        $submittedLogbookRotasi = LogbookRotasi::where('created_by', $currentUserId)
            ->whereIn('status', ['submitted', 'approved'])
            ->whereDate('created_at', today())
            ->get();

        foreach ($submittedLogbookRotasi as $logbook) {
            $logbookSubmissionStatuses['submitted'][] = [
                'name' => 'Logbook Rotasi - ' . strtoupper($logbook->type),
            ];
        }

        // Manual Book
        $submittedManualBooks = ManualBook::where('created_by', $currentUserId)
            ->whereIn('status', ['submitted', 'approved'])
            ->whereDate('created_at', today())
            ->get();

        foreach ($submittedManualBooks as $logbook) {
            $logbookSubmissionStatuses['submitted'][] = [
                'name' => 'Manual Book - ' . strtoupper($logbook->type),
            ];
        }

        return view('officer.dashboardOfficer', [
            'rejectedlogbooks' => $rejectedlogbooks,
            'locations' => $locations,
            'rejectedReports' => $rejectedReports,
            'logbookEntries' => $logbookEntries,
            'checklistKendaraan' => $checklistKendaraan,
            'checklistPenyisiran' => $checklistPenyisiran,
            'dailyTestStatuses' => $dailyTestStatuses,
            'checklistStatuses' => $checklistStatuses,
            'sweepingStatuses' => $sweepingStatuses,
            'draftLogbooks' => $draftLogbooks,
            'draftLogbookRotasi' => $draftLogbookRotasi,
            'draftManualBooks' => $draftManualBooks,
            'logbookSubmissionStatuses' => $logbookSubmissionStatuses,
        ]);
    }

    public function showDataLogbookRotasi(Request $request)
    {
        // 1. Ambil filter dan siapkan pagination
        $status = $request->get('status', '');
        $perPage = 10;

        // 2. Buat satu query dasar untuk semua logika yang sama
        $baseQuery = LogbookRotasi::with('creator', 'approver')->latest('date');

        // 3. Terapkan filter status jika ada
        if ($status) {
            $baseQuery->where('status', $status);
        }

        // 4. Terapkan logika filter untuk supervisor (non-superadmin)
        if (!Auth::user()->isSuperAdmin()) {
            $currentUserId = Auth::id();
            $baseQuery->where(function ($query) use ($currentUserId) {
                // Tampilkan jika status 'draft' (terbuka untuk semua supervisor)
                $query->where('status', 'draft')
                    // ATAU jika form ditujukan untuk supervisor ini
                    ->orWhere('approved_by', $currentUserId);
            });
        }

        // 5. Duplikasi (clone) query dasar untuk masing-masing tipe
        $pscpQuery = (clone $baseQuery)->where('type', 'pscp');
        $hbscpQuery = (clone $baseQuery)->where('type', 'hbscp');

        // 6. Ambil data dengan pagination
        $logbooksPSCP = $pscpQuery->paginate($perPage, ['*'], 'pscp_page');
        $logbooksHBSCP = $hbscpQuery->paginate($perPage, ['*'], 'hbscp_page');

        // 7. Kirim data ke view
        return view('supervisor.listLogbookRotasi', compact('logbooksPSCP', 'logbooksHBSCP'));
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
        // 1. Ambil filter dan siapkan pagination
        $status = $request->get('status');
        $perPage = 10;

        // 2. Buat satu query dasar untuk semua logika yang sama
        $baseQuery = ManualBook::with('creator', 'details')->latest('date');

        // 3. Terapkan filter status jika ada
        if ($status) {
            $baseQuery->where('status', $status);
        }

        // 4. Terapkan logika filter untuk supervisor (non-superadmin)
        if (!Auth::user()->isSuperAdmin()) {
            $currentUserId = Auth::id();
            $baseQuery->where(function ($query) use ($currentUserId) {
                // Tampilkan jika status 'draft' (terbuka untuk semua supervisor)
                $query->where('status', 'draft')
                    // ATAU jika form ditujukan untuk supervisor ini
                    ->orWhere('approved_by', $currentUserId);
            });
        }

        // 5. Duplikasi (clone) query dasar untuk masing-masing tipe
        $queryHBSCP = (clone $baseQuery)->where('type', 'hbscp');
        $queryPSCP = (clone $baseQuery)->where('type', 'pscp');

        // 6. Eksekusi query dengan pagination
        $manualBooksHBSCP = $queryHBSCP->paginate($perPage, ['*'], 'hbscp_page');
        $manualBooksPSCP = $queryPSCP->paginate($perPage, ['*'], 'pscp_page');

        // 7. Kirim data ke view
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
        $authId = Auth::id();

        // Daily Test
        $pendingDailyTestCount = Report::where('approvedByID', $authId)
            ->whereHas('status', function ($query) {
                $query->where('name', 'pending');
            })
            ->whereHas('equipmentLocation.equipment', function ($query) {
                $query->whereIn('name', ['hhmd', 'wtmd', 'xraycabin', 'xraybagasi']);
            })
            ->count();

        // Logbook with subtypes
        $pendingLogbookCounts = [
            'Pos Jaga' => Logbook::where('approvedID', $authId)->where('status', 'submitted')->count(),
            'Laporan Chief' => LogbookChief::where('approved_by', $authId)->where('status', 'submitted')->count(),
            'Rotasi' => LogbookRotasi::where('approved_by', $authId)->where('status', 'submitted')->count(),
            'Manual Book' => ManualBook::where('approved_by', $authId)->where('status', 'submitted')->count(),
        ];
        $totalPendingLogbooks = array_sum($pendingLogbookCounts);

        // Checklist with subtypes
        $pendingChecklistCounts = [
            'Kendaraan' => ChecklistKendaraan::where('approved_id', $authId)->where('status', 'submitted')->count(),
            'Penyisiran' => ChecklistPenyisiran::where('approved_id', $authId)->where('status', 'submitted')->count(),
            'Pencatatan PI' => FormPencatatanPI::where('approved_id', $authId)->where('status', 'submitted')->count(),
        ];
        $totalPendingChecklists = array_sum($pendingChecklistCounts);


        // Daily Test Stats
        $dailyTestStats = [];
        $equipmentTypes = ['hhmd', 'wtmd', 'xraycabin', 'xraybagasi'];
        $approvedStatus = ReportStatus::where('name', 'approved')->first();
        $approvedStatusId = $approvedStatus ? $approvedStatus->id : null;

        foreach ($equipmentTypes as $type) {
            $totalLocationsForType = EquipmentLocation::whereHas('equipment', function ($query) use ($type) {
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

        $allowedPosJagaLocations = [
            'Pos Kedatangan',
            'Pos Barat',
            'Pos Timur',
            'HBSCP',
            'PSCP',
            'CCTV',
            'Patroli',
            'Walking Patrol'
        ];
        $totalLocations = count($allowedPosJagaLocations);

        $logbooksToday = Logbook::whereHas('locationArea', function ($query) use ($allowedPosJagaLocations) {
            $query->whereIn('name', $allowedPosJagaLocations);
        })
            ->whereRaw('LOWER(status) IN (?, ?)', ['submitted', 'approved'])
            ->whereBetween('date', [today()->startOfDay(), today()->endOfDay()])
            ->get();

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

        $totalRotasiExpected = 2; // Total expected logbooks for PSCP and HBSCP
        $rotasiToday = LogbookRotasi::whereRaw('LOWER(status) IN (?, ?)', ['submitted', 'approved'])
            ->whereBetween('date', [today()->startOfDay(), today()->endOfDay()])
            ->get();

        $rotasiByType = $rotasiToday->groupBy('type');
        $typeDetailsRotasi = [];
        foreach ($rotasiByType as $typeName => $logbooks) {
            $typeDetailsRotasi[strtoupper($typeName)] = ['total' => $logbooks->count(), 'approved' => $logbooks->where('status', 'approved')->count()];
        }

        $submittedOrApprovedRotasi = $rotasiToday->count();

        $logbookStats['Rotasi'] = [
            'total' => $totalRotasiExpected,
            'approved' => $submittedOrApprovedRotasi,
            'percentage' => ($totalRotasiExpected > 0) ? round(($submittedOrApprovedRotasi / $totalRotasiExpected) * 100) : 0,
            'breakdown' => $typeDetailsRotasi,
            'breakdownTitle' => 'Tipe'
        ];

        $totalChief = LogbookChief::whereNotNull('senderSignature')->whereDate('created_at', today())->count();
        $approvedChief = LogbookChief::whereNotNull('approvedSignature')->whereDate('created_at', today())->count();
        $logbookStats['Laporan Chief'] = ['total' => $totalChief, 'approved' => $approvedChief, 'percentage' => ($totalChief > 0) ? round(($approvedChief / $totalChief) * 100) : 0, 'breakdown' => [], 'breakdownTitle' => ''];

        // Checklist Stats
        $checklistStats = [];

        $totalKendaraanExpected = 2; // mobil and motor
        $kendaraanToday = ChecklistKendaraan::whereRaw('LOWER(status) IN (?, ?)', ['submitted', 'approved'])
            ->whereBetween('date', [today()->startOfDay(), today()->endOfDay()])
            ->get();

        $kendaraanByType = $kendaraanToday->groupBy('type');
        $typeDetailsKendaraan = [];
        foreach ($kendaraanByType as $typeName => $checklists) {
            $typeDetailsKendaraan[ucfirst($typeName)] = ['total' => $checklists->count(), 'approved' => $checklists->where('status', 'approved')->count()];
        }

        $submittedOrApprovedKendaraan = $kendaraanToday->count();

        $checklistStats['Kendaraan'] = [
            'total' => $totalKendaraanExpected,
            'approved' => $submittedOrApprovedKendaraan,
            'percentage' => ($totalKendaraanExpected > 0) ? round(($submittedOrApprovedKendaraan / $totalKendaraanExpected) * 100) : 0,
            'breakdown' => $typeDetailsKendaraan,
            'breakdownTitle' => 'Tipe Kendaraan'
        ];

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
        $totalManualBookExpected = 2;
        $submittedManualBook = $manualBookToday->unique('type')->count();
        $checklistStats['Manual Book'] = [
            'total' => $totalManualBookExpected,
            'approved' => $submittedManualBook,
            'percentage' => ($totalManualBookExpected > 0) ? round(($submittedManualBook / $totalManualBookExpected) * 100) : 0,
            'breakdown' => $typeDetailsManualBook,
            'breakdownTitle' => 'Tipe'
        ];

        $draftLogbookChief = LogbookChief::where('status', 'draft')
            ->orderBy('created_at', 'desc')
            ->get();

        $pendingChiefReports = LogbookChief::with('createdBy')
            ->where('approved_by', $authId)
            ->where('status', 'submitted')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('supervisor.dashboardSupervisor', [
            'dailyTestStats' => $dailyTestStats,
            'logbookStats' => $logbookStats,
            'checklistStats' => $checklistStats,
            'pendingDailyTestCount' => $pendingDailyTestCount,
            'pendingLogbookCounts' => $pendingLogbookCounts,
            'totalPendingLogbooks' => $totalPendingLogbooks,
            'pendingChecklistCounts' => $pendingChecklistCounts,
            'totalPendingChecklists' => $totalPendingChecklists,
            'draftLogbookChief' => $draftLogbookChief,
            'pendingChiefReports' => $pendingChiefReports,
        ]);
    }


    public function indexSuperadmin()
    {
        // Daily Test Stats
        $dailyTestStats = [];
        $equipmentTypes = ['hhmd', 'wtmd', 'xraycabin', 'xraybagasi'];
        $approvedStatus = ReportStatus::where('name', 'approved')->first();
        $approvedStatusId = $approvedStatus ? $approvedStatus->id : null;

        foreach ($equipmentTypes as $type) {
            $totalLocationsForType = EquipmentLocation::whereHas('equipment', function ($query) use ($type) {
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

        $allowedPosJagaLocations = [
            'Pos Kedatangan',
            'Pos Barat',
            'Pos Timur',
            'HBSCP',
            'PSCP',
            'CCTV',
            'Patroli',
            'Walking Patrol'
        ];
        $totalLocations = count($allowedPosJagaLocations);

        $logbooksToday = Logbook::whereHas('locationArea', function ($query) use ($allowedPosJagaLocations) {
            $query->whereIn('name', $allowedPosJagaLocations);
        })
            ->whereRaw('LOWER(status) IN (?, ?)', ['submitted', 'approved'])
            ->whereBetween('date', [today()->startOfDay(), today()->endOfDay()])
            ->get();

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

        $totalRotasiExpected = 2; // Total expected logbooks for PSCP and HBSCP
        $rotasiToday = LogbookRotasi::whereRaw('LOWER(status) IN (?, ?)', ['submitted', 'approved'])
            ->whereBetween('date', [today()->startOfDay(), today()->endOfDay()])
            ->get();

        $rotasiByType = $rotasiToday->groupBy('type');
        $typeDetailsRotasi = [];
        foreach ($rotasiByType as $typeName => $logbooks) {
            $typeDetailsRotasi[strtoupper($typeName)] = ['total' => $logbooks->count(), 'approved' => $logbooks->where('status', 'approved')->count()];
        }

        $submittedOrApprovedRotasi = $rotasiToday->count();

        $logbookStats['Rotasi'] = [
            'total' => $totalRotasiExpected,
            'approved' => $submittedOrApprovedRotasi,
            'percentage' => ($totalRotasiExpected > 0) ? round(($submittedOrApprovedRotasi / $totalRotasiExpected) * 100) : 0,
            'breakdown' => $typeDetailsRotasi,
            'breakdownTitle' => 'Tipe'
        ];

        $totalChief = LogbookChief::whereNotNull('senderSignature')->whereDate('created_at', today())->count();
        $approvedChief = LogbookChief::whereNotNull('approvedSignature')->whereDate('created_at', today())->count();
        $logbookStats['Laporan Chief'] = ['total' => $totalChief, 'approved' => $approvedChief, 'percentage' => ($totalChief > 0) ? round(($approvedChief / $totalChief) * 100) : 0, 'breakdown' => [], 'breakdownTitle' => ''];

        // Checklist Stats
        $checklistStats = [];

        $totalKendaraanExpected = 2; // mobil and motor
        $kendaraanToday = ChecklistKendaraan::whereRaw('LOWER(status) IN (?, ?)', ['submitted', 'approved'])
            ->whereBetween('date', [today()->startOfDay(), today()->endOfDay()])
            ->get();

        $kendaraanByType = $kendaraanToday->groupBy('type');
        $typeDetailsKendaraan = [];
        foreach ($kendaraanByType as $typeName => $checklists) {
            $typeDetailsKendaraan[ucfirst($typeName)] = ['total' => $checklists->count(), 'approved' => $checklists->where('status', 'approved')->count()];
        }

        $submittedOrApprovedKendaraan = $kendaraanToday->count();

        $checklistStats['Kendaraan'] = [
            'total' => $totalKendaraanExpected,
            'approved' => $submittedOrApprovedKendaraan,
            'percentage' => ($totalKendaraanExpected > 0) ? round(($submittedOrApprovedKendaraan / $totalKendaraanExpected) * 100) : 0,
            'breakdown' => $typeDetailsKendaraan,
            'breakdownTitle' => 'Tipe Kendaraan'
        ];

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
        $totalManualBookExpected = 2;
        $submittedManualBook = $manualBookToday->unique('type')->count();
        $checklistStats['Manual Book'] = [
            'total' => $totalManualBookExpected,
            'approved' => $submittedManualBook,
            'percentage' => ($totalManualBookExpected > 0) ? round(($submittedManualBook / $totalManualBookExpected) * 100) : 0,
            'breakdown' => $typeDetailsManualBook,
            'breakdownTitle' => 'Tipe'
        ];

        return view('superadmin.dashboardSuperadmin', [
            'dailyTestStats' => $dailyTestStats,
            'logbookStats' => $logbookStats,
            'checklistStats' => $checklistStats
        ]);
    }
}
