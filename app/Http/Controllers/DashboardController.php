<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Report;
use App\Models\Logbook;
use App\Models\LogbookRotasiHBSCP;
use App\Models\LogbookRotasiPSCP;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    // protected $allowedLocations = [
    //     'Pos Kedatangan',
    //     'Pos Barat',
    //     'Pos Timur',
    //     'HBSCP',
    //     'PSCP',
    //     'CCTV',
    //     'Patroli',
    //     'Walking Patrol'
    // ];
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
            ->where('reports.approvedByID', Auth::id())
            ->whereIn('reports.statusID', [1, 2, 3])
            ->with(['submittedBy', 'status']);

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
        $userId = Auth::id(); // ID user yang sedang login

        $logbookQuery = Logbook::with(['locationArea', 'senderBy', 'receiverBy', 'approverBy'])
            ->where('approvedID', $userId) // hanya data dengan approver/supervisor sesuai ID user
            ->whereIn('status', ['submitted', 'approved'])
            ->orderBy('date', 'desc');

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
        $rejectedReports = \App\Models\Report::rejected()
            ->where('submittedByID', Auth::id())
            ->with([
                'status:id,name',
                'equipmentLocation.location:id,name',
                'equipmentLocation.equipment:id,name',
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        $logbookEntries = \App\Models\Logbook::where('receivedID', Auth::id())
            ->whereNull('receivedSignature')
            ->with([
                'locationArea:id,name',
                'senderBy:id,name',
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('officer.dashboardOfficer', [
            'rejectedlogbooks' => $rejectedlogbooks,
            'locations' => $locations,
            'rejectedReports' => $rejectedReports,
            'logbookEntries' => $logbookEntries,
        ]);
    }

    public function showDataLogbookRotasi()
    {
        $logbooksHBSCP = LogbookRotasiHBSCP::with('details')->get();

        $logbooksPSCP = LogbookRotasiPSCP::with('details')->get();

        return view('supervisor.listLogbookRotasi', compact('logbooksPSCP', 'logbooksHBSCP'));
    }
}
