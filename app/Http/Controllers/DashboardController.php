<?php

namespace App\Http\Controllers;

use App\Models\ChecklistKendaraan;
use App\Models\ChecklistPenyisiran;
use App\Models\FormPencatatanPI;
use App\Models\Location;
use App\Models\Report;
use App\Models\Logbook;
use App\Models\LogbookChief;
use App\Models\LogbookRotasi;
use App\Models\LogbookRotasiHBSCP;
use App\Models\LogbookRotasiPSCP;
use App\Models\ManualBook;
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

    public function showDataLogbookRotasi()
    {
        // 1. Tentukan jumlah item per halaman untuk pagination
        $perPage = 10;

        // 2. Query dasar untuk LogbookRotasi
        $pscpQuery = LogbookRotasi::with('creator', 'approver')->where('type', 'pscp');
        $hbscpQuery = LogbookRotasi::with('creator', 'approver')->where('type', 'hbscp');

        // 3. Filter berdasarkan approver jika bukan superadmin
        if (!Auth::user()->isSuperAdmin()) {
            $pscpQuery->where('approved_by', Auth::id());
            $hbscpQuery->where('approved_by', Auth::id());
        }

        // 4. Ambil data dengan pagination
        $logbooksPSCP = $pscpQuery->latest('date')->paginate($perPage, ['*'], 'pscp_page');
        $logbooksHBSCP = $hbscpQuery->latest('date')->paginate($perPage, ['*'], 'hbscp_page');

        // 5. Kirim kedua koleksi data ke view
        return view('supervisor.listLogbookRotasi', compact('logbooksPSCP', 'logbooksHBSCP'));
    }

    public function showDataChecklistKendaraan()
    {
        $perPage = 10;

        $checklistsMobil = ChecklistKendaraan::with('sender')
            ->where('type', 'mobil')
            ->latest('date')
            ->paginate($perPage, ['*'], 'mobil_page');

        $checklistsMotor = ChecklistKendaraan::with('sender')
            ->where('type', 'motor')
            ->latest('date')
            ->paginate($perPage, ['*'], 'motor_page');

        return view('supervisor.listChecklistKendaraan', compact('checklistsMobil', 'checklistsMotor'));
    }

    public function showDataChecklistPenyisiran()
    {
        $perPage = 10;

        $checklistsPenyisiran = ChecklistPenyisiran::with('sender')
            ->latest('date')
            ->paginate($perPage, ['*']);

        return view('supervisor.listChecklistPenyisiran', compact('checklistsPenyisiran'));
    }

    public function showDataManualBook()
    {
        $perPage = 10;

        $manualBooksHBSCP = ManualBook::with('creator', 'details')
            // ->where('approved_by', Auth::id())
            ->where('type', 'hbscp')
            ->latest('date')
            ->paginate($perPage, ['*'], 'hbscp_page');


        $manualBooksPSCP = ManualBook::with('creator', 'details')
            // ->where('approved_by', Auth::id())
            ->where('type', 'pscp')
            ->latest('date')
            ->paginate($perPage, ['*'], 'pscp_page');

        if (!Auth::user()->isSuperAdmin()) {
            $manualBooksHBSCP->where('approved_by', Auth::id());
            $manualBooksPSCP->where('approved_by', Auth::id());
        }
        return view('supervisor.listManualBook', compact('manualBooksHBSCP', 'manualBooksPSCP'));
    }

    public function showDataFormPencatatanPI()
    {
        $perPage = 10;

        $query = FormPencatatanPI::query();

        if (!Auth::user()->isSuperAdmin()) {
            $query->where('approved_id', Auth::id());
        }

        $formPencatatanPI = $query
            ->latest('date')
            ->paginate($perPage, ['*']);

        return view('supervisor.listFormPencatatanPI', compact('formPencatatanPI'));
    }


    public function indexSupervisor()
    {
        $perPage = 10;

        $logbooksChief = LogbookChief::with('createdBy', 'approvedBy')
            ->where('approved_by', Auth::id())
            ->latest('date')
            ->paginate($perPage);

        return view('supervisor.dashboardSupervisor', compact('logbooksChief'));
    }
}
