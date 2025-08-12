<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\EquipmentLocation;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Location;
use App\Models\Logbook;

class ExportController extends Controller
{
    protected $allowedLocationLogbook = [
        'Pos Kedatangan',
        'Pos Barat',
        'Pos Timur',
        'HBSCP',
        'PSCP',
        'CCTV',
        'Patroli',
        'Walking Patrol'
    ];

    public function index(Request $request)
    {
        return view('superadmin.export.exportPdf');
    }

    public function exportPdfDailyTest(Request $request)
    {
        $equipmentTypes = ['hhmd', 'wtmd', 'xraycabin', 'xraybagasi'];

        // Ambil data untuk dropdown equipment
        $availableEquipments = Equipment::whereIn('name', $equipmentTypes)
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(function ($equipment) {
                return [
                    'value' => strtoupper($equipment->name),
                    'label' => 'Form ' . strtoupper($equipment->name),
                    'name' => $equipment->name
                ];
            });

        // Get ALL locations (akan difilter di frontend)
        $allLocations = EquipmentLocation::whereHas('equipment', function ($query) use ($equipmentTypes) {
            $query->whereIn('name', $equipmentTypes);
        })
            ->with(['location', 'equipment']) // Tambahkan equipment relation
            ->get()
            ->map(function ($el) {
                return [
                    'location_id' => $el->location_id,
                    'location_name' => $el->location->name ?? 'Nama lokasi tidak tersedia',
                    'merk_type' => $el->merk_type ?? 'Merk/Type tidak tersedia',
                    'certificateInfo' => $el->certificateInfo ?? 'Informasi sertifikat tidak tersedia',
                    'equipment_name' => $el->equipment->name ?? '', // Tambahkan equipment name
                ];
            });

        // Group locations by equipment untuk frontend
        $locationsByEquipment = $allLocations->groupBy('equipment_name');

        // Ambil filter dari request
        $formType = $request->get('form_type');
        $location = $request->get('location');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $statusFilter = $request->get('status');

        // Query untuk reports (sama seperti sebelumnya)
        $query = Report::query()
            ->join('equipment_locations', 'reports.equipmentLocationID', '=', 'equipment_locations.id')
            ->join('equipment', 'equipment_locations.equipment_id', '=', 'equipment.id')
            ->join('locations', 'equipment_locations.location_id', '=', 'locations.id')
            ->whereIn('equipment.name', $equipmentTypes)
            ->whereIn('reports.statusID', [2])
            ->with(['submittedBy', 'status']);

        // Filter berdasarkan form type
        if (!empty($formType)) {
            $query->where('equipment.name', strtolower($formType));
        }

        // Filter berdasarkan lokasi
        if (!empty($location)) {
            $query->where('locations.name', $location);
        }

        // Filter berdasarkan tanggal
        if (!empty($startDate)) {
            $query->whereDate('reports.testDate', '>=', $startDate);
        }

        if (!empty($endDate)) {
            $query->whereDate('reports.testDate', '<=', $endDate);
        }

        // Filter berdasarkan status (jika ada)
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
                    'date' => $report->testDate ? $report->testDate->format('d/m/Y') : 'N/A',
                    'test_type' => strtoupper($report->equipment_name),
                    'location' => $report->location_name,
                    'status' => $report->status ? $report->status->name : 'pending',
                    'operator' => $report->submittedBy ? $report->submittedBy->name : 'Unknown',
                ];
            });

        return view('superadmin.export.exportPdfDailyTest', [
            'reports' => $reports,
            'availableEquipments' => $availableEquipments,
            'availableLocations' => $allLocations->unique('location_name'), // Untuk dropdown "Semua Lokasi"
            'locationsByEquipment' => $locationsByEquipment, // Data lokasi berdasarkan equipment
            'filters' => [
                'form_type' => $formType,
                'location' => $location,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'status' => $statusFilter
            ]
        ]);
        return view('superadmin.export.exportPdfDailyTest');
    }



    public function exportPdfLogbook(Request $request)
    {

        return view('superadmin.export.exportPdfLogbook');
        // Ambil data lokasi dari model Location
        $locations = Location::whereIn('name', $this->allowedLocationLogbook)
            ->orderBy('name')
            ->get();

        // Ambil data logbook untuk tabel (contoh dengan filter sederhana)
        $logbooks = Logbook::with(['locationArea', 'senderBy', 'receiverBy', 'approverBy'])
            ->orderBy('date', 'desc')
            ->take(10) // Batasi untuk contoh
            ->get();

        return view('superadmin.export.exportPdfLogbook', [
            'locations' => $locations,
            'logbooks' => $logbooks
        ]);
    }

    public function filterLogbook(Request $request)
    {
        $query = Logbook::with(['locationArea'])
            ->orderBy('date', 'desc');

        if ($request->form_type) {
            // Tambahkan filter berdasarkan form_type jika diperlukan
        }

        if ($request->location) {
            $query->whereHas('locationArea', function ($q) use ($request) {
                $q->where('name', $request->location);
            });
        }

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('date', [
                $request->start_date,
                $request->end_date
            ]);
        }

        $logbooks = $query->get();

        return response()->json([
            'logbooks' => $logbooks
        ]);
    }
}
