<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\EquipmentLocation;
use App\Models\Report;
use App\Models\ReportDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Location;
use App\Models\Logbook;
use App\Services\PdfService;

class ExportPdfController extends Controller
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

    /**
     * Menampilkan halaman filter ekspor PDF dan menangani logika ekspor.
     *
     * @param Request $request
     * @param PdfService $pdfService
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function exportPdfDailyTest(Request $request, PdfService $pdfService)
    {
        // ====================================================================
        // BAGIAN 1: LOGIKA UNTUK MENANGANI PERMINTAAN EKSPOR PDF
        // ====================================================================
        if ($request->has('export_type')) {

            if ($request->input('export_type') === 'selected') {

                // 1. Ambil Jenis Form dari filter
                $formType = $request->input('form_type');
                if (empty($formType)) {
                    return redirect()->back()->with('error', 'Silakan pilih "Jenis Form" pada filter untuk bisa mengekspor.');
                }

                // 2. Ambil ID laporan yang dicentang
                $selectedIds = $request->input('selected_reports', []);
                if (empty($selectedIds)) {
                    return redirect()->back()->with('error', 'Pilih minimal satu laporan untuk diekspor!');
                }

                // 3. Petakan Tipe Form ke View yang sesuai
                $type = strtolower($formType);
                $viewMapping = [
                    'hhmd'       => 'superadmin.export.pdf.hhmdTemplate',
                    'wtmd'       => 'pdf.templates.wtmd',
                    'xraycabin'  => 'pdf.templates.xraycabin',
                    'xraybagasi' => 'pdf.templates.xraybagasi',
                ];

                if (!isset($viewMapping[$type])) {
                    return redirect()->back()->with('error', 'Jenis Form tidak valid atau tidak memiliki template.');
                }
                $viewName = $viewMapping[$type];

                // 4. Ambil data dari model Report dan relasi ReportDetail
                $reportsToExport = Report::whereIn('reportID', $selectedIds)
                    ->with([
                        'reportDetails', // <-- KUNCI UTAMA: Ambil data dari tabel report_details
                        'submittedBy',
                        'approvedBy',
                        'equipmentLocation.location',
                    ])
                    ->get();

                // 5. Gabungkan data dari Report dan ReportDetail menjadi satu objek untuk view
                $formsForView = $reportsToExport->map(function ($report) {
                    // Buat objek baru untuk menghindari modifikasi data asli
                    $form = new \stdClass();

                    // Ambil semua atribut dari reportDetails jika ada
                    if ($report->reportDetails) {
                        foreach ($report->reportDetails->getAttributes() as $key => $value) {
                            $form->$key = $value;
                        }
                    }

                    // Tambahkan/Timpa dengan data dari report utama dan relasinya
                    $form->operatorName      = $report->submittedBy->name ?? 'N/A';
                    $form->testDateTime      = $report->testDate;
                    $form->location          = $report->equipmentLocation->location->name ?? 'N/A';
                    $form->deviceInfo        = $report->equipmentLocation->merk_type ?? 'N/A';
                    $form->certificateInfo   = $report->equipmentLocation->certificateInfo ?? 'N/A';
                    $form->officerName       = $report->submittedBy->name ?? 'N/A';
                    $form->supervisor        = $report->approvedBy;
                    $form->officer_signature = $report->submitterSignature; // Nama kolom dari model Report
                    $form->supervisor_signature = $report->approverSignature; // Nama kolom dari model Report
                    $form->result            = $report->result; // Ambil dari tabel reports
                    $form->notes             = $report->note; // Ambil dari tabel reports

                    return $form;
                });

                if ($formsForView->isEmpty()) {
                    return redirect()->back()->with('error', 'Data laporan yang dipilih tidak ditemukan.');
                }

                // 6. Panggil service dengan view dan data yang sudah benar
                return $pdfService->generatePdfFromTemplate($viewName, $formsForView, $formType);
            }
        }

        // ====================================================================
        // BAGIAN 2: LOGIKA UNTUK MENAMPILKAN HALAMAN FILTER
        // ====================================================================
        $equipmentTypes = ['hhmd', 'wtmd', 'xraycabin', 'xraybagasi'];

        $availableEquipments = Equipment::whereIn('name', $equipmentTypes)
            ->orderBy('name')->get(['id', 'name'])
            ->map(fn($eq) => ['value' => strtoupper($eq->name), 'label' => 'Form ' . strtoupper($eq->name), 'name'  => $eq->name]);

        $allLocations = EquipmentLocation::whereHas('equipment', fn($q) => $q->whereIn('name', $equipmentTypes))
            ->with(['location', 'equipment'])->get()
            ->map(fn($el) => ['location_name' => $el->location->name ?? 'N/A', 'equipment_name'=> $el->equipment->name ?? '']);

        $locationsByEquipment = $allLocations->groupBy('equipment_name');
        $filters = $request->only(['form_type', 'location', 'start_date', 'end_date', 'status']);

        $query = Report::query()
            ->join('equipment_locations', 'reports.equipmentLocationID', '=', 'equipment_locations.id')
            ->join('equipment', 'equipment_locations.equipment_id', '=', 'equipment.id')
            ->join('locations', 'equipment_locations.location_id', '=', 'locations.id')
            ->whereIn('equipment.name', $equipmentTypes)
            ->with('status');

        if (!empty($filters['form_type'])) $query->where('equipment.name', strtolower($filters['form_type']));
        if (!empty($filters['location'])) $query->where('locations.name', $filters['location']);
        if (!empty($filters['start_date'])) $query->whereDate('reports.testDate', '>=', $filters['start_date']);
        if (!empty($filters['end_date'])) $query->whereDate('reports.testDate', '<=', $filters['end_date']);
        if (!empty($filters['status'])) $query->whereHas('status', fn($q) => $q->where('name', $filters['status']));

        $reports = $query
            ->select('reports.reportID as id', 'reports.testDate', 'reports.statusID', 'locations.name as location_name', 'equipment.name as equipment_name')
            ->orderBy('reports.created_at', 'desc')->get()
            ->map(fn($r) => [
                'id' => $r->id,
                'date' => $r->testDate ? \Carbon\Carbon::parse($r->testDate)->format('d/m/Y') : 'N/A',
                'test_type' => strtoupper($r->equipment_name),
                'location' => $r->location_name,
                'status' => $r->status->name ?? 'pending',
            ]);

        return view('superadmin.export.exportPdfDailyTest', [
            'reports' => $reports,
            'availableEquipments' => $availableEquipments,
            'availableLocations' => $allLocations->unique('location_name')->sortBy('location_name')->values(),
            'locationsByEquipment' => $locationsByEquipment,
            'filters' => $filters,
        ]);
    }

    public function exportPdfLogbook(Request $request)
    {
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
