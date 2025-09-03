<?php

namespace App\Http\Controllers;

use App\Models\ChecklistItem;
use App\Models\ChecklistKendaraan;
use App\Models\ChecklistKendaraanDetail;
use App\Models\ChecklistPenyisiran;
use App\Models\ChecklistSenpi;
use App\Models\Equipment;
use App\Models\EquipmentLocation;
use App\Models\FormPencatatanPI;
use App\Models\LogbookRotasi;
use App\Models\ManualBook;
use App\Models\Report;
use App\Models\ReportDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Location;
use App\Models\Logbook;
use App\Models\LogbookSweepingPI;
use App\Models\User;
use App\Services\PdfService;
use Illuminate\Support\Facades\Log;

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
        'Walking Patrol',
    ];

    public function index(Request $request)
    {
        return view('superadmin.export.exportPdf');
    }

    //daily test
    private function prepareFormData(Report $report)
    {
        $form = new \stdClass();

        $detail = $report->reportDetails->first();

        if ($detail instanceof \Illuminate\Support\Collection) {
            $detail = $detail->first();
        }

        if ($detail instanceof \Illuminate\Database\Eloquent\Model) {
            foreach ($detail->toArray() as $key => $value) {
                $form->$key = $value;
            }
        }

        $form->operatorName      = $report->submittedBy->name ?? 'N/A';
        $form->testDateTime      = $report->testDate;
        $form->location          = $report->equipmentLocation->location->name ?? 'N/A';
        $form->deviceInfo        = $report->equipmentLocation->merk_type ?? 'N/A';
        $form->certificateInfo   = $report->equipmentLocation->certificateInfo ?? 'N/A';
        $form->officerName       = $report->submittedBy->name ?? 'N/A';
        $form->supervisor        = $report->approvedBy;
        $form->officer_signature = $report->submitterSignature;
        $form->supervisor_signature = $report->approverSignature;
        $form->result            = strtolower($report->result ?? '');
        $form->notes             = $report->note;
        $form->terpenuhi = ($form->result === 'pass');
        $form->tidakTerpenuhi = ($form->result === 'fail');

        if (!isset($form->testCondition1)) {
            $form->testCondition1 = true;
        }
        if (!isset($form->testCondition2)) {
            $form->testCondition2 = true;
        }
        if (!isset($form->test1)) {
            $form->test1 = ($form->result === 'pass');
        }

        return $form;
    }

    public function reviewDailyTest(Report $report)
    {
        $formType = $report->equipmentLocation->equipment->name;
        $type = strtolower($formType);
        $viewMapping = [
            'hhmd'       => 'superadmin.export.pdf.dailytest.hhmdTemplate',
            'wtmd'       => 'superadmin.export.pdf.dailytest.wtmdTemplate',
            'xraycabin'  => 'superadmin.export.pdf.dailytest.xraycabinTemplate',
            'xraybagasi' => 'superadmin.export.pdf.dailytest.xraybagasiTemplate',
        ];

        if (!isset($viewMapping[$type])) {
            return response('Template not found for this form type.', 404);
        }
        $viewName = $viewMapping[$type];

        $report->load(['reportDetails', 'submittedBy', 'approvedBy', 'equipmentLocation.location']);

        $form = $this->prepareFormData($report);
        $forms = collect([$form]);

        return view($viewName, [
            'forms' => $forms,
        ]);
    }

    public function exportPdfDailyTest(Request $request, PdfService $pdfService)
    {
        if ($request->has('export_type')) {
            $formType = $request->input('form_type');
            if (empty($formType)) {
                return redirect()->back()->with('error', 'Silakan pilih "Jenis Form" pada filter untuk bisa mengekspor.');
            }

            $type = strtolower($formType);
            $viewMapping = [
                'hhmd'       => 'superadmin.export.pdf.dailytest.hhmdTemplate',
                'wtmd'       => 'superadmin.export.pdf.dailytest.wtmdTemplate',
                'xraycabin'  => 'superadmin.export.pdf.dailytest.xraycabinTemplate',
                'xraybagasi' => 'superadmin.export.pdf.dailytest.xraybagasiTemplate',
            ];

            if (!isset($viewMapping[$type])) {
                return redirect()->back()->with('error', 'Jenis Form tidak valid atau tidak memiliki template.');
            }
            $viewName = $viewMapping[$type];

            $exportQuery = Report::query()->where('statusID', 2); // Can only export approved reports.

            if ($request->input('export_type') === 'selected') {
                $selectedIds = $request->input('selected_reports', []);
                if (empty($selectedIds)) {
                    return redirect()->back()->with('error', 'Pilih minimal satu laporan untuk diekspor!');
                }
                $exportQuery->whereIn('reportID', $selectedIds);
            } elseif ($request->input('export_type') === 'all') {
                // Apply filters for 'all' export
                $filters = $request->only(['form_type', 'location', 'start_date', 'end_date']);
                $exportQuery
                    ->join('equipment_locations', 'reports.equipmentLocationID', '=', 'equipment_locations.id')
                    ->join('equipment', 'equipment_locations.equipment_id', '=', 'equipment.id')
                    ->join('locations', 'equipment_locations.location_id', '=', 'locations.id');

                if (!empty($filters['form_type'])) $exportQuery->where('equipment.name', strtolower($filters['form_type']));
                if (!empty($filters['location'])) $exportQuery->where('locations.name', $filters['location']);
                if (!empty($filters['start_date'])) $exportQuery->whereDate('reports.testDate', '>=', $filters['start_date']);
                if (!empty($filters['end_date'])) $exportQuery->whereDate('reports.testDate', '<=', $filters['end_date']);
            }

            $reportsToExport = $exportQuery->with([
                'reportDetails',
                'submittedBy',
                'approvedBy',
                'equipmentLocation.location',
                'equipmentLocation.equipment',
            ])->get();

            if ($reportsToExport->isEmpty()) {
                $errorMessage = $request->input('export_type') === 'selected'
                    ? 'Laporan yang dipilih tidak ada yang berstatus "approved" atau tidak ditemukan.'
                    : 'Tidak ada laporan berstatus "approved" yang ditemukan untuk filter yang dipilih.';
                return redirect()->back()->with('error', $errorMessage);
            }

            $formsForView = $reportsToExport->map(fn($report) => $this->prepareFormData($report));
            return $pdfService->generatePdfFromTemplate($viewName, $formsForView, $formType);
        }

        $equipmentTypes = ['hhmd', 'wtmd', 'xraycabin', 'xraybagasi'];
        $availableEquipments = Equipment::whereIn('name', $equipmentTypes)
            ->orderBy('name')->get(['id', 'name'])
            ->map(fn($eq) => ['value' => strtoupper($eq->name), 'label' => 'Form ' . strtoupper($eq->name), 'name'  => $eq->name]);

        $allLocations = EquipmentLocation::whereHas('equipment', fn($q) => $q->whereIn('name', $equipmentTypes))
            ->with(['location', 'equipment'])->get()
            ->map(fn($el) => ['location_name' => $el->location->name ?? 'N/A', 'equipment_name' => $el->equipment->name ?? '']);

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


    //logbook
    public function reviewLogbook(Request $request, $logbookID)
    {
        // Get form type from request or determine from logbook ID pattern
        $formType = $request->input('form_type');

        if (!$formType) {
            // Auto-detect form type based on ID pattern
            $formType = $this->detectLogbookType($logbookID);
        }

        try {
            switch ($formType) {
                case 'pos_jaga':
                    return $this->reviewPosJagaLogbook($logbookID);

                case 'sweeping_pi':
                    return $this->reviewSweepingPILogbook($logbookID);

                case 'rotasi':
                    return $this->reviewRotasiLogbook($logbookID);

                case 'chief':
                    return $this->reviewChiefLogbook($logbookID);

                default:
                    abort(404, 'Jenis logbook tidak ditemukan');
            }
        } catch (\Exception $e) {
            Log::error("Error reviewing logbook: " . $e->getMessage());
            abort(404, 'Data logbook tidak ditemukan');
        }
    }

    private function detectLogbookType($logbookID)
    {
        // Detect based on ID pattern
        if (str_starts_with($logbookID, 'SPI-')) {
            return 'sweeping_pi';
        } elseif (str_starts_with($logbookID, 'LRH-') || str_starts_with($logbookID, 'LRS-')) {
            return 'rotasi';
        } elseif (str_starts_with($logbookID, 'CHF-')) {
            return 'chief';
        } else {
            return 'pos_jaga'; // default untuk logbook pos jaga
        }
    }

    private function reviewPosJagaLogbook($logbookID)
    {
        $logbook = Logbook::where('logbookID', $logbookID)->firstOrFail();
        $logbook->load(['details', 'senderBy', 'receiverBy', 'approverBy', 'locationArea']);

        $form = $this->prepareLogbookFormData($logbook);
        $forms = collect([$form]);

        return view('superadmin.export.pdf.logbook.logbookPosJagaTemplate', [
            'forms' => $forms,
            'logbook' => $logbook,
            'formType' => 'pos_jaga'
        ]);
    }

    private function reviewSweepingPILogbook($sweepingpiID)
    {
        $sweeping = LogbookSweepingPI::where('sweepingpiID', $sweepingpiID)->firstOrFail();
        $sweeping->load(['tenant', 'sweepingPIDetails', 'notesSweepingPI']);

        $form = $this->prepareSweepingPIFormData($sweeping);
        $forms = collect([$form]);

        return view('superadmin.export.pdf.logbook.logbookSweepingTemplate', [
            'forms' => $forms,
            'sweeping' => $sweeping,
            'formType' => 'sweeping_pi'
        ]);
    }

    private function reviewRotasiLogbook($id)
    {
        $rotasi = LogbookRotasi::where('id', $id)->firstOrFail();

        // Load relationships yang dibutuhkan
        $rotasi->load([
            'creator',
            'submitter',
            'approver',
            'details.officerAssignment'
        ]);

        // Prepare form data
        $form = $this->prepareRotasiFormData($rotasi);

        // Prepare officer log data (struktur seperti di detail view)
        $officerLog = $this->prepareOfficerLogData($rotasi);

        $forms = collect([$form]);

        return view('superadmin.export.pdf.logbook.logbookRotasiTemplate', [
            'forms' => $forms,
            'officerLog' => $officerLog, // Pass officerLog data ke template
        ]);
    }

    private function reviewChiefLogbook($chiefID)
    {
        // Untuk sementara return dummy data karena model chief belum ada
        $form = new \stdClass();
        $form->chiefID = $chiefID;
        $form->date = now();
        $form->aktivitas = 'Inspeksi Rutin';
        $form->lokasi = 'Semua Area';
        $form->chief = 'Chief Security';
        $form->status = 'submitted';

        $forms = collect([$form]);

        return view('superadmin.export.pdf.logbook.logbookChiefTemplate', [
            'forms' => $forms,
            'formType' => 'chief'
        ]);
    }

    private function prepareSweepingPIFormData(LogbookSweepingPI $sweeping)
    {
        $form = new \stdClass();

        // Map sweeping main attributes
        $form->sweepingpiID = $sweeping->sweepingpiID;
        $form->created_at = $sweeping->created_at;
        $form->bulan = $sweeping->bulan;
        $form->tahun = $sweeping->tahun;
        $form->notes = $sweeping->notes;

        // Map tenant relationship
        $form->tenantName = $sweeping->tenant->name ?? 'N/A';
        $form->tenant = $sweeping->tenant;

        // Map details with calendar format
        $form->sweepingDetails = $sweeping->sweepingPIDetails;
        $form->notesSweeping = $sweeping->notesSweepingPI;

        // Calculate completion stats
        $form->completionStats = $sweeping->getCompletionStats();

        // // Calculate calendar data for each prohibited item
        $form->calendarData = $this->prepareCalendarData($sweeping);

        return $form;
    }

    private function prepareCalendarData(LogbookSweepingPI $sweeping)
    {
        $daysInMonth = \Carbon\Carbon::createFromDate($sweeping->tahun, $sweeping->bulan, 1)->daysInMonth;
        $calendarData = [];

        foreach ($sweeping->sweepingPIDetails as $detail) {
            $itemData = [
                'item_name' => $detail->item_name ?? 'Prohibited Item',
                'item_count' => $detail->item_count ?? 0,
                'days' => []
            ];

            for ($day = 1; $day <= $daysInMonth; $day++) {
                $fieldName = 'tanggal_' . $day;
                $noteField = 'note_' . $day;

                $itemData['days'][$day] = [
                    'checked' => isset($detail->$fieldName) && $detail->$fieldName,
                    'has_note' => isset($detail->$noteField) && !empty($detail->$noteField),
                    'note' => $detail->$noteField ?? null,
                    'is_overdue' => $day < now()->day && $sweeping->bulan == now()->month && $sweeping->tahun == now()->year && (!isset($detail->$fieldName) || !$detail->$fieldName)
                ];
            }

            $calendarData[] = $itemData;
        }

        return $calendarData;
    }

    private function prepareRotasiFormData(LogbookRotasi $rotasi)
    {
        $form = new \stdClass();

        // Map rotasi main attributes
        $form->rotasiID = $rotasi->id;
        $form->date = $rotasi->date;
        $form->type = $rotasi->type;
        $form->status = $rotasi->status;
        $form->notes = $rotasi->notes;

        // Map user relationships
        $form->creatorName = $rotasi->creator->name ?? 'N/A';
        $form->approverName = $rotasi->approver->name ?? 'N/A';
        $form->creator = $rotasi->creator;
        $form->approver = $rotasi->approver;

        // Map signatures
        $form->submittedSignature = $rotasi->submittedSignature;
        $form->approvedSignature = $rotasi->approvedSignature;

        // Create location object
        $form->location = (object)[
            'name' => 'Area ' . ucfirst($rotasi->type)
        ];

        // Additional fields for compatibility
        $form->shift = ucfirst($rotasi->type);
        $form->petugas_masuk = $rotasi->creator->name ?? 'N/A';
        $form->petugas_keluar = $rotasi->approver->name ?? 'N/A';

        // Transform details menjadi struktur seperti di detail view
        $form->rotasiDetails = $rotasi->details;

        return $form;
    }

    // Method tambahan untuk membuat struktur officerLog seperti di detail view
    private function prepareOfficerLogData(LogbookRotasi $rotasi)
    {
        $officerLog = [];

        foreach ($rotasi->details as $detail) {
            // Array semua role dan officer IDs
            $roles = [
                'pengatur_flow' => $detail->pengatur_flow,
                'operator_xray' => $detail->operator_xray,
                'manual_bagasi_petugas' => $detail->manual_bagasi_petugas,
                'reunited' => $detail->reunited,
                'pemeriksaan_dokumen' => $detail->pemeriksaan_dokumen,
                'hhmd_petugas' => $detail->hhmd_petugas,
                'manual_kabin_petugas' => $detail->manual_kabin_petugas,
            ];

            foreach ($roles as $roleName => $officerId) {
                if (!$officerId) continue;

                // Initialize officer entry if not exists
                if (!isset($officerLog[$officerId])) {
                    $user = User::find($officerId);
                    $officerLog[$officerId] = [
                        'officer_name' => $user->name ?? 'Officer ' . $officerId,
                        'roles' => [],
                        'keterangan' => []
                    ];
                }

                // Add role data
                if (!isset($officerLog[$officerId]['roles'][$roleName])) {
                    $officerLog[$officerId]['roles'][$roleName] = [];
                }

                $roleData = [
                    'start' => $detail->officerAssignment->start_time ?? '00:00',
                    'end' => $detail->officerAssignment->end_time ?? '23:59',
                ];

                // Add special fields for HHMD
                if ($roleName === 'hhmd_petugas') {
                    $roleData['hhmd_random'] = $detail->hhmd_random ?? '-';
                    $roleData['hhmd_unpredictable'] = $detail->hhmd_unpredictable ?? '-';
                }

                // Add special fields for Manual Kabin
                if ($roleName === 'manual_kabin_petugas') {
                    $roleData['cek_random_barang'] = $detail->cek_random_barang ?? '-';
                    $roleData['barang_unpredictable'] = $detail->barang_unpredictable ?? '-';
                }

                $officerLog[$officerId]['roles'][$roleName][] = $roleData;

                // Add keterangan
                if ($detail->keterangan) {
                    $officerLog[$officerId]['keterangan'][] = $detail->keterangan;
                }
            }
        }

        return $officerLog;
    }

    // Keep existing prepareLogbookFormData function for pos_jaga
    private function prepareLogbookFormData(Logbook $logbook)
    {
        $form = new \stdClass();

        // Map logbook main attributes
        $form->logbookID = $logbook->logbookID;
        $form->date = $logbook->date;
        $form->location = $logbook->locationArea->name ?? 'N/A';
        $form->grup = $logbook->grup;
        $form->shift = $logbook->shift;
        $form->status = $logbook->status;

        // Map user relationships
        $form->senderName = $logbook->senderBy->name ?? 'N/A';
        $form->receiverName = $logbook->receiverBy->name ?? 'N/A';
        $form->approverName = $logbook->approverBy->name ?? 'N/A';

        // Map signatures
        $form->senderSignature = $logbook->senderSignature;
        $form->receivedSignature = $logbook->receivedSignature;
        $form->approvedSignature = $logbook->approvedSignature;

        // Map rejection reason if exists
        $form->rejectedReason = $logbook->rejected_reason;

        // Map logbook details for uraian tugas
        $form->logbookDetails = $logbook->details;
        $form->facility = $logbook->facility;
        $form->personil = $logbook->personil;

        return $form;
    }

    public function exportPdfLogbook(Request $request)
    {
        // Jika request memiliki export_type, proses export
        if ($request->has('export_type')) {
            return $this->processLogbookExport($request);
        }

        $locations = Location::whereIn('name', $this->allowedLocationLogbook)
            ->orderBy('name')
            ->get();

        // Load initial data berdasarkan form type default (pos_jaga)
        $initialFormType = $request->input('form_type', 'pos_jaga');
        $initialData = $this->getLogbookData($initialFormType, $request->only(['location', 'start_date', 'end_date']));

        return view('superadmin.export.exportPdfLogbook', [
            'locations' => $locations,
            'logbooks' => $initialData,
            'formType' => $initialFormType
        ]);
    }

    public function filterLogbook(Request $request)
    {
        $formType = $request->input('form_type', 'pos_jaga');
        $filters = $request->only(['location', 'start_date', 'end_date']);

        $logbooks = $this->getLogbookData($formType, $filters);

        return response()->json([
            'success' => true,
            'form_type' => $formType,
            'logbooks' => $logbooks,
            'count' => $logbooks->count()
        ]);
    }

    // Add this method to your controller or update existing getLogbookData method
    private function getLogbookData($formType, $filters = [])
    {
        switch ($formType) {
            case 'pos_jaga':
                $query = Logbook::with(['locationArea', 'senderBy', 'receiverBy', 'approverBy']);

                if (!empty($filters['location'])) {
                    $query->whereHas('locationArea', function ($q) use ($filters) {
                        $q->where('name', $filters['location']);
                    });
                }

                if (!empty($filters['start_date'])) {
                    $query->whereDate('date', '>=', $filters['start_date']);
                }

                if (!empty($filters['end_date'])) {
                    $query->whereDate('date', '<=', $filters['end_date']);
                }

                return $query->orderBy('date', 'desc')->get();

            case 'sweeping_pi':
                $query = LogbookSweepingPI::with(['tenant']);

                if (!empty($filters['start_date'])) {
                    $query->whereDate('created_at', '>=', $filters['start_date']);
                }

                if (!empty($filters['end_date'])) {
                    $query->whereDate('created_at', '<=', $filters['end_date']);
                }

                return $query->orderBy('created_at', 'desc')->get();

            case 'rotasi':
                $query = LogbookRotasi::with(['creator', 'approver', 'submitter', 'details']);

                // For rotasi, you might filter by type instead of location
                if (!empty($filters['location'])) {
                    $query->where('type', $filters['location']);
                }

                if (!empty($filters['start_date'])) {
                    $query->whereDate('date', '>=', $filters['start_date']);
                }

                if (!empty($filters['end_date'])) {
                    $query->whereDate('date', '<=', $filters['end_date']);
                }

                return $query->orderBy('date', 'desc')->get();

            case 'chief':
                // Return dummy data for chief since model doesn't exist yet
                return collect([
                    (object) [
                        'chiefID' => 'CHF-001',
                        'date' => now(),
                        'aktivitas' => 'Inspeksi Rutin',
                        'lokasi' => 'Semua Area',
                        'chief' => 'Chief Security',
                        'status' => 'submitted'
                    ]
                ]);

            default:
                return collect();
        }
    }
    private function processLogbookExport(Request $request)
    {
        $formType = $request->input('form_type');
        if (empty($formType)) {
            return redirect()->back()->with('error', 'Silakan pilih "Jenis Logbook" untuk bisa mengekspor.');
        }

        // Logic untuk export PDF berdasarkan jenis logbook
        if ($request->input('export_type') === 'selected') {
            $selectedIds = $request->input('selected_reports', []);
            if (empty($selectedIds)) {
                return redirect()->back()->with('error', 'Pilih minimal satu data untuk diekspor!');
            }

            // Export selected data
            return $this->exportSelectedLogbook($formType, $selectedIds);
        } elseif ($request->input('export_type') === 'all') {
            $filters = $request->only(['location', 'start_date', 'end_date']);

            // Export all data with filters
            return $this->exportAllLogbook($formType, $filters);
        }

        return redirect()->back()->with('error', 'Tipe export tidak valid.');
    }
    private function exportSelectedLogbook($formType, $selectedIds)
    {
        // Implementasi export selected logbook
        // Sesuaikan dengan PDF service yang ada

        switch ($formType) {
            case 'pos_jaga':
                $data = Logbook::whereIn('logbookID', $selectedIds)
                    ->with(['locationArea', 'senderBy', 'receiverBy', 'approverBy'])
                    ->get();
                break;

            case 'sweeping_pi':
                $data = LogbookSweepingPI::whereIn('sweepingID', $selectedIds)
                    ->with(['tenant', 'createdBy'])
                    ->get();
                break;

            case 'rotasi':
                $data = LogbookRotasi::whereIn('id', $selectedIds)
                    ->with(['location', 'petugasMasuk', 'petugasKeluar'])
                    ->get();
                break;

            default:
                return redirect()->back()->with('error', 'Jenis logbook tidak valid.');
        }

        if ($data->isEmpty()) {
            return redirect()->back()->with('error', 'Data yang dipilih tidak ditemukan.');
        }

        // Generate PDF menggunakan service yang ada
        return $this->generateLogbookPdf($formType, $data);
    }
    private function exportAllLogbook($formType, $filters)
    {
        // Implementasi export all logbook dengan filter
        $data = $this->getLogbookData($formType, $filters);

        if ($data->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada data yang ditemukan untuk filter yang dipilih.');
        }

        // Generate PDF menggunakan service yang ada
        return $this->generateLogbookPdf($formType, $data);
    }
    private function generateLogbookPdf($formType, $data)
    {
        $pdfService = app(PdfService::class);

        $viewMapping = [
            'pos_jaga' => 'superadmin.export.pdf.logbook.logbookPosJagaTemplate',
            'sweeping_pi' => 'superadmin.export.pdf.logbook.logbookSweepingTemplate',
            'rotasi' => 'superadmin.export.pdf.logbook.logbookRotasiTemplate',
            'chief' => 'superadmin.export.pdf.logbook.logbookChiefTemplate',
        ];

        $viewName = $viewMapping[$formType] ?? null;

        if (!$viewName) {
            return redirect()->back()->with('error', 'Template tidak ditemukan untuk jenis logbook ini.');
        }

        return $pdfService->generatePdfFromTemplate($viewName, $data, $formType);
    }



    //checklist

    public function reviewChecklist(Request $request, $id)
    {
        $formType = $request->input('form_type');

        if (!$formType) {
            abort(400, 'Jenis checklist tidak disediakan.');
        }

        try {
            switch ($formType) {
                case 'kendaraan':
                    return $this->reviewChecklistKendaraan($id);
                case 'penyisiran':
                    return $this->reviewChecklistPenyisiran($id);
                case 'senpi':
                    return $this->reviewChecklistSenpi($id);
                case 'pencatatan_pi':
                    return $this->reviewFormPencatatanPI($id);
                case 'manual_book':
                    return $this->reviewManualBook($id);
                default:
                    abort(404, 'Jenis checklist tidak valid.');
            }
        } catch (\Exception $e) {
            Log::error("Error reviewing checklist: " . $e->getMessage());
            abort(404, 'Data checklist tidak ditemukan.');
        }
    }

    private function reviewChecklistKendaraan($id)
    {
        $checklist = ChecklistKendaraan::with([
            'details.item',
            'sender',
            'receiver',
            'approver'
        ])->findOrFail($id);

        return view('superadmin.export.pdf.checklist.checklistKendaraanTemplate', [
            'forms' => collect([$checklist]),
            'formType' => 'kendaraan'
        ]);
    }

    private function reviewChecklistPenyisiran($id)
    {
        $checklist = ChecklistPenyisiran::with(['details.item', 'sender', 'receiver', 'approver'])->findOrFail($id);
        return view('superadmin.export.pdf.checklist.checklistPenyisiranTemplate', [
            'forms' => collect([$checklist]),
            'formType' => 'penyisiran'
        ]);
    }

    private function reviewChecklistSenpi($id)
    {
        $checklist = ChecklistSenpi::findOrFail($id);
        return view('superadmin.export.pdf.checklist.checklistSenpiTemplate', [
            'forms' => collect([$checklist]),
            'formType' => 'senpi'
        ]);
    }

    private function reviewFormPencatatanPI($id)
    {
        $checklist = FormPencatatanPI::with(['sender', 'approver'])->findOrFail($id);
        return view('superadmin.export.pdf.checklist.checklistPencatatanPITemplate', [
            'forms' => collect([$checklist]),
            'formType' => 'pencatatan_pi'
        ]);
    }

    private function reviewManualBook($id)
    {
        $checklist = ManualBook::with(['details', 'creator', 'approver'])->findOrFail($id);
        return view('superadmin.export.pdf.checklist.checklistManualBookTemplate', [
            'forms' => collect([$checklist]),
            'formType' => 'manual_book'
        ]);
    }

    public function exportPdfChecklist(Request $request)
    {
        if ($request->has('export_type')) {
            return $this->processChecklistExport($request);
        }

        $locations = Location::whereIn('name', $this->allowedLocationLogbook)
            ->orderBy('name')
            ->get();

        $initialFormType = $request->input('form_type', 'kendaraan');
        $initialData = $this->getChecklistData($initialFormType, $request->only(['location', 'start_date', 'end_date']));

        return view('superadmin.export.exportPdfChecklist', [
            'locations' => $locations,
            'checklists' => $initialData,
            'formType' => $initialFormType
        ]);
    }

    public function filterChecklist(Request $request)
    {
        $formType = $request->input('form_type', 'kendaraan');
        $filters = $request->only(['location', 'start_date', 'end_date']);

        $data = $this->getChecklistData($formType, $filters);

        return response()->json([
            'checklists' => $data,
            'form_type' => $formType
        ]);
    }

    private function getChecklistData($formType, $filters = [])
    {
        $startDate = $filters['start_date'] ?? null;
        $endDate = $filters['end_date'] ?? null;
        $locationName = $filters['location'] ?? null;

        switch ($formType) {
            case 'kendaraan':
                $query = ChecklistKendaraan::with(['sender', 'receiver', 'approver'])
                    ->orderBy('date', 'desc');
                if ($startDate && $endDate) {
                    $query->whereBetween('date', [$startDate, $endDate]);
                }
                return $query->get()->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'date' => $item->date,
                        'nomor_polisi' => $item->type,
                        'petugas' => $item->sender,
                        'status' => $item->status,
                    ];
                });

            case 'penyisiran':
                $query = ChecklistPenyisiran::with(['sender', 'receiver', 'approver'])
                    ->orderBy('date', 'desc');
                if ($startDate && $endDate) {
                    $query->whereBetween('date', [$startDate, $endDate]);
                }
                return $query->get()->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'date' => $item->date,
                        'lokasi' => (object)['name' => $item->type],
                        'petugas' => $item->sender,
                        'status' => $item->status,
                    ];
                });

            case 'senpi':
                $query = ChecklistSenpi::orderBy('date', 'desc');
                if ($startDate && $endDate) {
                    $query->whereBetween('date', [$startDate, $endDate]);
                }
                return $query->get()->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'date' => $item->date,
                        'nomor_senpi' => $item->licenseNumber,
                        'petugas' => (object)['name' => $item->name],
                        'status' => 'approved',
                    ];
                });

            case 'pencatatan_pi':
                $query = FormPencatatanPI::with(['sender', 'approver'])
                    ->orderBy('date', 'desc');
                if ($startDate && $endDate) {
                    $query->whereBetween('date', [$startDate, $endDate]);
                }
                return $query->get()->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'date' => $item->date,
                        'petugas' => $item->sender,
                        'jenis_pi' => $item->jenis_PI,
                        'lokasi' => (object)['name' => 'Area ' . $item->grup],
                        'status' => $item->status,
                    ];
                });

            case 'manual_book':
                $query = ManualBook::with(['creator', 'approver', 'details'])
                    ->orderBy('date', 'desc');
                if ($startDate && $endDate) {
                    $query->whereBetween('date', [$startDate, $endDate]);
                }
                if ($locationName) {
                    $query->where('type', 'like', '%' . $locationName . '%');
                }
                return $query->get()->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'date' => $item->date,
                        'petugas' => $item->creator,
                        'shift' => $item->shift,
                        'lokasi' => (object)['name' => $item->type],
                        'status' => $item->status,
                        'total_records' => $item->details->count(),
                    ];
                });

            default:
                return collect();
        }
    }

    private function processChecklistExport(Request $request)
    {
        $formType = $request->input('form_type');
        if (empty($formType)) {
            return redirect()->back()->with('error', 'Silakan pilih "Jenis Checklist" untuk bisa mengekspor.');
        }

        // Logic untuk export PDF berdasarkan jenis checklist
        if ($request->input('export_type') === 'selected') {
            $selectedIds = $request->input('selected_reports', []);
            if (empty($selectedIds)) {
                return redirect()->back()->with('error', 'Pilih minimal satu data untuk diekspor!');
            }

            // Export selected data
            return $this->exportSelectedChecklist($formType, $selectedIds);
        } elseif ($request->input('export_type') === 'all') {
            $filters = $request->only(['location', 'start_date', 'end_date']);

            // Export all data with filters
            return $this->exportAllChecklist($formType, $filters);
        }

        return redirect()->back()->with('error', 'Tipe export tidak valid.');
    }

    private function exportSelectedChecklist($formType, $selectedIds)
    {
        // Implementasi export selected checklist
        // Sesuaikan dengan PDF service yang ada

        switch ($formType) {
            case 'kendaraan':
                $data = ChecklistKendaraan::whereIn('id', $selectedIds)
                    ->with(['sender', 'receiver', 'approver'])
                    ->get();
                break;

            case 'penyisiran':
                $data = ChecklistPenyisiran::whereIn('id', $selectedIds)
                    ->with(['sender', 'receiver', 'approver'])
                    ->get();
                break;

            case 'senpi':
                $data = ChecklistSenpi::whereIn('id', $selectedIds)
                    ->with(['creator'])
                    ->get();
                break;

            case 'pencatatan_pi':
                $data = FormPencatatanPI::whereIn('id', $selectedIds)
                    ->with(['sender', 'approver'])
                    ->get();
                break;

            case 'manual_book':
                $data = ManualBook::whereIn('id', $selectedIds)
                    ->with(['creator', 'approver', 'details'])
                    ->get();
                break;

            default:
                return redirect()->back()->with('error', 'Jenis checklist tidak valid.');
        }

        if ($data->isEmpty()) {
            return redirect()->back()->with('error', 'Data yang dipilih tidak ditemukan.');
        }

        // Generate PDF menggunakan service yang ada
        return $this->generateChecklistPdf($formType, $data);
    }

    private function exportAllChecklist($formType, $filters)
    {
        // Implementasi export all checklist dengan filter
        $data = $this->getChecklistDataForExport($formType, $filters);

        if ($data->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada data yang ditemukan untuk filter yang dipilih.');
        }

        // Generate PDF menggunakan service yang ada
        return $this->generateChecklistPdf($formType, $data);
    }

    private function getChecklistDataForExport($formType, $filters = [])
    {
        $startDate = $filters['start_date'] ?? null;
        $endDate = $filters['end_date'] ?? null;
        $locationName = $filters['location'] ?? null;

        switch ($formType) {
            case 'kendaraan':
                $query = ChecklistKendaraan::with(['sender', 'receiver', 'approver'])
                    ->orderBy('date', 'desc');
                if ($startDate && $endDate) {
                    $query->whereBetween('date', [$startDate, $endDate]);
                }
                return $query->get();

            case 'penyisiran':
                $query = ChecklistPenyisiran::with(['sender', 'receiver', 'approver'])
                    ->orderBy('date', 'desc');
                if ($startDate && $endDate) {
                    $query->whereBetween('date', [$startDate, $endDate]);
                }
                return $query->get();

            case 'senpi':
                $query = ChecklistSenpi::orderBy('date', 'desc');
                if ($startDate && $endDate) {
                    $query->whereBetween('date', [$startDate, $endDate]);
                }
                return $query->get();

            case 'pencatatan_pi':
                $query = FormPencatatanPI::with(['sender', 'approver'])
                    ->orderBy('date', 'desc');
                if ($startDate && $endDate) {
                    $query->whereBetween('date', [$startDate, $endDate]);
                }
                return $query->get();

            case 'manual_book':
                $query = ManualBook::with(['creator', 'approver', 'details'])
                    ->orderBy('date', 'desc');
                if ($startDate && $endDate) {
                    $query->whereBetween('date', [$startDate, $endDate]);
                }
                if ($locationName) {
                    $query->where('type', 'like', '%' . $locationName . '%');
                }
                return $query->get();

            default:
                return collect();
        }
    }

    private function generateChecklistPdf($formType, $data)
    {
        $pdfService = app(PdfService::class);

        $viewMapping = [
            'kendaraan' => 'superadmin.export.pdf.checklist.checklistKendaraanTemplate',
            'penyisiran' => 'superadmin.export.pdf.checklist.checklistPenyisiranTemplate',
            'senpi' => 'superadmin.export.pdf.checklist.checklistSenpiTemplate',
            'pencatatan_pi' => 'superadmin.export.pdf.checklist.checklistPencatatanPITemplate',
            'manual_book' => 'superadmin.export.pdf.checklist.checklistManualBookTemplate',
        ];

        $viewName = $viewMapping[$formType] ?? null;

        if (!$viewName) {
            return redirect()->back()->with('error', 'Template tidak ditemukan untuk jenis checklist ini.');
        }
        
        return $pdfService->generatePdfFromTemplate($viewName, $data, $formType);
    }
}
