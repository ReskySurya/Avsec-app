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
use App\Models\LogbookChief;
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

    protected $locationLogbookRotasi = [
        'HBSCP',
        'PSCP',
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

            $paperSizeMapping = [
                'hhmd'       => 'A4',
                'wtmd'       => 'A4',
                'xraycabin'  => 'F4', // F4 untuk X-Ray Cabin
                'xraybagasi' => 'F4', // F4 untuk X-Ray Bagasi
            ];

            if (!isset($viewMapping[$type])) {
                return redirect()->back()->with('error', 'Jenis Form tidak valid atau tidak memiliki template.');
            }
            $viewName = $viewMapping[$type];
            $paperSize = $paperSizeMapping[$type] ?? 'A4';

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
            return $pdfService->generatePdfFromTemplate($viewName, $formsForView, $formType, $paperSize);
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
    public function reviewLogbook(Request $request, $id)
    {
        $formType = $request->input('form_type');
        if (!$formType) {
            $formType = $this->detectLogbookType($id);
        }

        try {
            switch ($formType) {
                case 'pos_jaga':
                    return $this->reviewPosJagaLogbook($id);
                case 'sweeping_pi':
                    return $this->reviewSweepingPILogbook($id);
                case 'rotasi':
                    return $this->reviewRotasiLogbook($id);
                case 'chief':
                    return $this->reviewChiefLogbook($id);
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
        if (str_starts_with($logbookID, 'SPI-')) return 'sweeping_pi';
        if (str_starts_with($logbookID, 'LRH-') || str_starts_with($logbookID, 'LRP-')) return 'rotasi';
        if (str_starts_with($logbookID, 'CHF-')) return 'chief';
        return 'pos_jaga';
    }

    private function reviewPosJagaLogbook($logbookID)
    {
        $logbook = Logbook::with(['details', 'senderBy', 'receiverBy', 'approverBy', 'locationArea', 'facility', 'personil.user'])->findOrFail($logbookID);
        $form = $this->prepareLogbookFormData($logbook);
        return view('superadmin.export.pdf.logbook.logbookPosJagaTemplate', [
            'forms' => collect([$form]),
        ]);
    }

    private function reviewSweepingPILogbook($sweepingpiID)
    {
        $sweeping = LogbookSweepingPI::with(['tenant', 'sweepingPIDetails', 'notesSweepingPI'])->findOrFail($sweepingpiID);
        $form = $this->prepareSweepingPIFormData($sweeping);
        return view('superadmin.export.pdf.logbook.logbookSweepingTemplate', [
            'forms' => collect([$form]),
        ]);
    }

    private function reviewRotasiLogbook($id)
    {
        $rotasi = LogbookRotasi::with(['creator', 'approver', 'submitter', 'details.officerAssignment.officer'])->findOrFail($id);
        $form = $this->prepareRotasiFormData($rotasi);
        $form->officerLog = $this->prepareOfficerLogData($rotasi);
        return view('superadmin.export.pdf.logbook.logbookRotasiTemplate', [
            'forms' => collect([$form]),
        ]);
    }

    private function reviewChiefLogbook($logbookID)
    {
        $logbook = LogbookChief::with(['details', 'createdBy', 'approvedBy', 'facility', 'personil.user', 'kemajuan'])->findOrFail($logbookID);
        $form = $this->prepareLogbookChief($logbook);
        return view('superadmin.export.pdf.logbook.logbookChiefTemplate', [
            'forms' => collect([$form]),
        ]);
    }

    private function prepareSweepingPIFormData(LogbookSweepingPI $sweeping)
    {
        $form = new \stdClass();
        $form->sweepingpiID = $sweeping->sweepingpiID;
        $form->created_at = $sweeping->created_at;
        $form->bulan = $sweeping->bulan;
        $form->tahun = $sweeping->tahun;
        $form->notes = $sweeping->notes;
        $form->tenant = $sweeping->tenant;
        $form->sweepingDetails = $sweeping->sweepingPIDetails;
        $form->notesSweeping = $sweeping->notesSweepingPI;
        $form->completionStats = $sweeping->getCompletionStats();
        return $form;
    }

    private function prepareRotasiFormData(LogbookRotasi $rotasi)
    {
        $form = new \stdClass();
        $form->rotasiID = $rotasi->id;
        $form->date = $rotasi->date;
        $form->type = $rotasi->type;
        $form->status = $rotasi->status;
        $form->notes = $rotasi->notes;
        $form->creatorName = $rotasi->creator->name ?? 'N/A';
        $form->approverName = $rotasi->approver->name ?? 'N/A';
        $form->submittedSignature = $rotasi->submittedSignature;
        $form->approvedSignature = $rotasi->approvedSignature;
        return $form;
    }

    private function prepareOfficerLogData(LogbookRotasi $rotasi)
    {
        $officerLog = [];
        $officerIds = $rotasi->details->pluck('pemeriksaan_dokumen')
            ->merge($rotasi->details->pluck('pengatur_flow'))
            ->merge($rotasi->details->pluck('operator_xray'))
            ->merge($rotasi->details->pluck('hhmd_petugas'))
            ->merge($rotasi->details->pluck('manual_kabin_petugas'))
            ->filter()->unique();

        $officers = User::whereIn('id', $officerIds)->get()->keyBy('id');

        foreach ($rotasi->details as $detail) {
            $roles = [
                'pemeriksaan_dokumen' => $detail->pemeriksaan_dokumen,
                'pengatur_flow' => $detail->pengatur_flow,
                'operator_xray' => $detail->operator_xray,
                'hhmd_petugas' => $detail->hhmd_petugas,
                'manual_kabin_petugas' => $detail->manual_kabin_petugas,
            ];

            foreach ($roles as $roleName => $officerId) {
                if (!$officerId) continue;

                if (!isset($officerLog[$officerId])) {
                    $officer = $officers->get($officerId);
                    $officerLog[$officerId] = [
                        'officer_name' => $officer ? $officer->name : 'Officer Tidak Dikenal (' . $officerId . ')',
                        'roles' => [],
                        'keterangan' => []
                    ];
                }

                $roleData = [
                    'start' => optional($detail->officerAssignment)->start_time ?? '00:00',
                    'end' => optional($detail->officerAssignment)->end_time ?? '23:59',
                    'hhmd_random' => $detail->hhmd_random ?? '-',
                    'hhmd_unpredictable' => $detail->hhmd_unpredictable ?? '-',
                    'cek_random_barang' => $detail->cek_random_barang ?? '-',
                    'barang_unpredictable' => $detail->barang_unpredictable ?? '-',
                ];

                $officerLog[$officerId]['roles'][$roleName][] = $roleData;

                if ($detail->keterangan) {
                    $officerLog[$officerId]['keterangan'][] = $detail->keterangan;
                }
            }
        }
        return $officerLog;
    }

    private function prepareLogbookFormData(Logbook $logbook)
    {
        $form = new \stdClass();
        $form->logbookID = $logbook->logbookID;
        $form->date = $logbook->date;
        $form->location = $logbook->locationArea->name ?? 'N/A';
        $form->grup = $logbook->grup;
        $form->shift = $logbook->shift;
        $form->status = $logbook->status;
        $form->senderName = $logbook->senderBy->name ?? 'N/A';
        $form->receiverName = $logbook->receiverBy->name ?? 'N/A';
        $form->approverName = $logbook->approverBy->name ?? 'N/A';
        $form->senderSignature = $logbook->senderSignature;
        $form->receivedSignature = $logbook->receivedSignature;
        $form->approvedSignature = $logbook->approvedSignature;
        $form->rejectedReason = $logbook->rejected_reason;
        $form->logbookDetails = $logbook->details;
        $form->facility = $logbook->facility;
        $form->personil = $logbook->personil;
        return $form;
    }


    private function prepareLogbookChief(LogbookChief $logbook)
    {
        $form = new \stdClass();
        $form->logbookID = $logbook->logbookID;
        $form->date = $logbook->date;
        $form->grup = $logbook->grup;
        $form->shift = $logbook->shift;
        $form->status = 'Approved';
        $form->senderName = $logbook->createdBy->name ?? 'N/A';
        $form->receiverName = $logbook->approvedBy->name ?? 'N/A';
        $form->senderSignature = $logbook->senderSignature;
        $form->approvedSignature = $logbook->approvedSignature;
        $form->logbookDetails = $logbook->details;
        $form->facility = $logbook->facility;
        $form->personil = $logbook->personil;
        $form->kemajuan = $logbook->kemajuan;

        // For template compatibility
        $form->lokasi = $logbook->grup;
        $form->chief = $logbook->createdBy->name ?? 'N/A';
        $form->notes = $logbook->notes ?? 'Tidak ada catatan khusus.';
        $form->chiefSignature = $logbook->senderSignature;

        return $form;
    }

    public function exportPdfLogbook(Request $request)
    {
        if ($request->has('export_type')) {
            return $this->processLogbookExport($request);
        }

        $initialFormType = $request->input('form_type', 'pos_jaga');

        $locationsQuery = Location::query();
        if ($initialFormType === 'rotasi') {
            $locationsQuery->whereIn('name', $this->locationLogbookRotasi);
        } else {
            $locationsQuery->whereIn('name', $this->allowedLocationLogbook);
        }
        $locations = $locationsQuery->orderBy('name')->get();

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

    private function getLogbookData($formType, $filters = [])
    {
        $query = null;
        $startDate = $filters['start_date'] ?? null;
        $endDate = $filters['end_date'] ?? null;

        switch ($formType) {
            case 'pos_jaga':
                $query = Logbook::with(['locationArea', 'senderBy', 'receiverBy', 'approverBy', 'details', 'facility', 'personil.user']);
                if (!empty($filters['location'])) {
                    $query->whereHas('locationArea', fn($q) => $q->where('name', $filters['location']));
                }
                if ($startDate) $query->whereDate('date', '>=', $startDate);
                if ($endDate) $query->whereDate('date', '<=', $endDate);
                return $query->orderBy('date', 'desc')->get();

            case 'sweeping_pi':
                $query = LogbookSweepingPI::with(['tenant', 'sweepingPIDetails', 'notesSweepingPI']);
                if ($startDate) $query->whereDate('created_at', '>=', $startDate);
                if ($endDate) $query->whereDate('created_at', '<=', $endDate);
                return $query->orderBy('created_at', 'desc')->get();

            case 'rotasi':
                $query = LogbookRotasi::with(['creator', 'approver', 'submitter', 'details.officerAssignment.officer']);
                if (!empty($filters['location'])) {
                    $query->where('type', $filters['location']);
                }
                if ($startDate) $query->whereDate('date', '>=', $startDate);
                if ($endDate) $query->whereDate('date', '<=', $endDate);
                return $query->orderBy('date', 'desc')->get();

            case 'chief':
                $query = LogbookChief::with(['createdBy']);
                if ($startDate) $query->whereDate('date', '>=', $startDate);
                if ($endDate) $query->whereDate('date', '<=', $endDate);
                return $query->orderBy('date', 'desc')->get()->map(function ($item) {
                    return [
                        'logbookID' => $item->logbookID,
                        'date' => $item->date,
                        'grup' => $item->grup,
                        'shift' => $item->shift,
                        'senderName' => $item->createdBy->name ?? 'N/A',
                        'status' => 'approved',
                    ];
                });

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

        $data = collect();

        if ($request->input('export_type') === 'selected') {
            $selectedIds = $request->input('selected_reports', []);
            if (empty($selectedIds)) {
                return redirect()->back()->with('error', 'Pilih minimal satu data untuk diekspor!');
            }
            $data = $this->getLogbookDataForExport($formType, [], $selectedIds);
        } elseif ($request->input('export_type') === 'all') {
            $filters = $request->only(['location', 'start_date', 'end_date']);
            $data = $this->getLogbookDataForExport($formType, $filters);
        } else {
            return redirect()->back()->with('error', 'Tipe export tidak valid.');
        }

        if ($data->isEmpty()) {
            $errorMessage = $request->input('export_type') === 'selected'
                ? 'Data yang dipilih tidak ditemukan.'
                : 'Tidak ada data yang ditemukan untuk filter yang dipilih.';
            return redirect()->back()->with('error', $errorMessage);
        }

        return $this->generateLogbookPdf($formType, $data);
    }

    private function getLogbookDataForExport($formType, $filters = [], $ids = [])
    {
        $query = null;
        switch ($formType) {
            case 'pos_jaga':
                $query = Logbook::with(['details', 'senderBy', 'receiverBy', 'approverBy', 'locationArea', 'facility', 'personil.user']);
                break;
            case 'sweeping_pi':
                $query = LogbookSweepingPI::with(['tenant', 'sweepingPIDetails', 'notesSweepingPI']);
                break;
            case 'rotasi':
                $query = LogbookRotasi::with(['creator', 'approver', 'submitter', 'details.officerAssignment.officer']);
                break;
            case 'chief':
                $query = LogbookChief::with(['details', 'createdBy', 'approvedBy', 'facility', 'personil.user', 'kemajuan']);
                break;
            default:
                return collect();
        }

        $dateColumn = ($formType === 'sweeping_pi') ? 'created_at' : 'date';

        if (!empty($ids)) {
            $keyName = $query->getModel()->getKeyName();
            $query->whereIn($keyName, $ids);
        } else {
            $startDate = $filters['start_date'] ?? null;
            $endDate = $filters['end_date'] ?? null;

            if ($startDate) {
                $query->whereDate($dateColumn, '>=', $startDate);
            }
            if ($endDate) {
                $query->whereDate($dateColumn, '<=', $endDate);
            }

            if (!empty($filters['location'])) {
                if ($formType === 'pos_jaga') {
                    $query->whereHas('locationArea', fn($q) => $q->where('name', $filters['location']));
                } elseif ($formType === 'rotasi') {
                    $query->where('type', $filters['location']);
                }
            }
        }

        return $query->orderBy($dateColumn, 'desc')->get();
    }

    private function generateLogbookPdf($formType, $data)
    {
        $pdfService = app(PdfService::class);
        $viewMapping = [
            'pos_jaga'    => 'superadmin.export.pdf.logbook.logbookPosJagaTemplate',
            'sweeping_pi' => 'superadmin.export.pdf.logbook.logbookSweepingTemplate',
            'rotasi'      => 'superadmin.export.pdf.logbook.logbookRotasiTemplate',
            'chief'       => 'superadmin.export.pdf.logbook.logbookChiefTemplate',
        ];

        $viewName = $viewMapping[$formType] ?? null;
        if (!$viewName) {
            return redirect()->back()->with('error', 'Template tidak ditemukan untuk jenis logbook ini.');
        }

        $preparedData = $data->map(function ($model) use ($formType) {
            switch ($formType) {
                case 'pos_jaga':
                    return $this->prepareLogbookFormData($model);
                case 'sweeping_pi':
                    return $this->prepareSweepingPIFormData($model);
                case 'rotasi':
                    $form = $this->prepareRotasiFormData($model);
                    $form->officerLog = $this->prepareOfficerLogData($model);
                    return $form;
                case 'chief':
                    return $this->prepareLogbookChief($model);
                default:
                    return $model;
            }
        });

        return $pdfService->generatePdfFromTemplate($viewName, $preparedData, $formType);
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
                $query = ChecklistKendaraan::with(['sender', 'receiver', 'approver', 'details.item'])
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
                $query = ChecklistPenyisiran::with(['sender', 'receiver', 'approver', 'details.item'])
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

        $data = collect();

        if ($request->input('export_type') === 'selected') {
            $selectedIds = $request->input('selected_reports', []);
            if (empty($selectedIds)) {
                return redirect()->back()->with('error', 'Pilih minimal satu data untuk diekspor!');
            }
            $data = $this->getChecklistDataForExport($formType, [], $selectedIds);
        } elseif ($request->input('export_type') === 'all') {
            $filters = $request->only(['location', 'start_date', 'end_date']);
            $data = $this->getChecklistDataForExport($formType, $filters);
        } else {
            return redirect()->back()->with('error', 'Tipe export tidak valid.');
        }

        if ($data->isEmpty()) {
            $errorMessage = $request->input('export_type') === 'selected'
                ? 'Data yang dipilih tidak ditemukan.'
                : 'Tidak ada data yang ditemukan untuk filter yang dipilih.';
            return redirect()->back()->with('error', $errorMessage);
        }

        return $this->generateChecklistPdf($formType, $data);
    }

    private function getChecklistDataForExport($formType, $filters = [], $ids = [])
    {
        $query = null;
        switch ($formType) {
            case 'kendaraan':
                $query = ChecklistKendaraan::with(['details.item', 'sender', 'receiver', 'approver']);
                break;
            case 'penyisiran':
                $query = ChecklistPenyisiran::with(['details.item', 'sender', 'receiver', 'approver']);
                break;
            case 'senpi':
                $query = ChecklistSenpi::query();
                break;
            case 'pencatatan_pi':
                $query = FormPencatatanPI::with(['sender', 'approver']);
                break;
            case 'manual_book':
                $query = ManualBook::with(['creator', 'approver', 'details']);
                break;
            default:
                return collect();
        }

        if (!empty($ids)) {
            return $query->whereIn('id', $ids)->orderBy('date', 'desc')->get();
        }

        if (!empty($filters['start_date'])) {
            $query->whereDate('date', '>=', $filters['start_date']);
        }
        if (!empty($filters['end_date'])) {
            $query->whereDate('date', '<=', $filters['end_date']);
        }
        if (!empty($filters['location']) && $formType === 'manual_book') {
            $query->where('type', 'like', '%' . $filters['location'] . '%');
        }

        return $query->orderBy('date', 'desc')->get();
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

        $paperSizeMapping = [
            'kendaraan' => 'A4',
            'penyisiran' => 'F4',
            'senpi'  => 'F4', 
            'pencatatan_pi' => 'F4', 
            'manual_book' => 'F4', 
        ];

        $viewName = $viewMapping[$formType] ?? null;
        $paperSize = $paperSizeMapping[$formType] ?? 'A4';

        if (!$viewName) {
            return redirect()->back()->with('error', 'Template tidak ditemukan untuk jenis checklist ini.');
        }

        return $pdfService->generatePdfFromTemplate($viewName, $data, $formType, $paperSize);
    }
}
