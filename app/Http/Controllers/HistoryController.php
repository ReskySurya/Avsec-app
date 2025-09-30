<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\Logbook;
use App\Models\LogbookChief;
use App\Models\LogbookRotasi;
use App\Models\LogbookSweepingPI;
use App\Models\ChecklistKendaraan;
use App\Models\ChecklistPenyisiran;
use App\Models\ChecklistSenpi;
use App\Models\FormPencatatanPI;
use App\Models\ManualBook;
use App\Models\User;
use App\Services\PreviewService;
use Illuminate\Support\Facades\Log;

class HistoryController extends Controller
{
    public function index()
    {
        return view('history.index');
    }

    public function show(Request $request, $category)
    {
        $filters = $request->only(['start_date', 'end_date', 'form_type', 'shift']);
        $data = collect();
        $form_types = [];
        $isShiftFilterable = false;

        switch ($category) {
            case 'daily-test':
                $form_types = ['HHMD', 'WTMD', 'XRAYCABIN', 'XRAYBAGASI'];
                $data = $this->getDailyTestData($filters);
                $isShiftFilterable = false; // Daily test doesn't have shift
                break;

            case 'logbook':
                $form_types = ['pos_jaga', 'sweeping_pi', 'rotasi', 'chief'];
                $data = $this->getLogbookData($filters);
                $isShiftFilterable = true;
                break;

            case 'checklist':
                $form_types = ['kendaraan', 'penyisiran', 'senpi', 'pencatatan_pi', 'manual_book'];
                $data = $this->getChecklistData($filters);
                $isShiftFilterable = true;
                break;

            default:
                abort(404);
        }

        if (empty($filters['form_type'])) {
            $data = $data->groupBy('form_type');
        }

        return view('history.show', [
            'category' => $category,
            'data' => $data,
            'form_types' => $form_types,
            'isShiftFilterable' => $isShiftFilterable,
        ]);
    }
 
    private function getDailyTestData($filters)
    {
        $query = Report::query()
            ->join('equipment_locations', 'reports.equipmentLocationID', '=', 'equipment_locations.id')
            ->join('equipment', 'equipment_locations.equipment_id', '=', 'equipment.id')
            ->join('locations', 'equipment_locations.location_id', '=', 'locations.id')
            ->where('reports.statusID', 2) // Approved
            ->with('submittedBy');

        if (!empty($filters['form_type'])) {
            $query->where('equipment.name', strtolower($filters['form_type']));
        }
        if (!empty($filters['start_date'])) {
            $query->whereDate('reports.testDate', '>=', $filters['start_date']);
        }
        if (!empty($filters['end_date'])) {
            $query->whereDate('reports.testDate', '<=', $filters['end_date']);
        }

        return $query->select('reports.reportID as id', 'reports.testDate as date', 'equipment.name as form_type', 'reports.statusID as status', 'locations.name as location_name', 'reports.submittedByID')
            ->latest('reports.created_at')->get()->map(function ($item) {
                $item->officer = $item->submittedBy->name ?? 'N/A';
                // $item->shift = 'N/A'; // Add a default value for shift
                $item->location = $item->location_name ?? 'N/A';
                return $item;
            });
    }

    private function getLogbookData($filters = [])
    {
        $formType = $filters['form_type'] ?? null;
        $typesToQuery = $formType ? [$formType] : ['pos_jaga', 'sweeping_pi', 'rotasi', 'chief'];
        $data = collect();

        foreach ($typesToQuery as $type) {
            $data = $data->merge($this->fetchLogbookRecords($type, $filters));
        }
        return $data->sortByDesc('date');
    }

    private function fetchLogbookRecords($formType, $filters)
    {
        $query = null;
        $startDate = $filters['start_date'] ?? null;
        $endDate = $filters['end_date'] ?? null;
        $shift = $filters['shift'] ?? null;

        switch ($formType) {
            case 'pos_jaga':
                $query = Logbook::with(['locationArea', 'senderBy'])->where('status', 'approved');
                if ($shift) $query->where('shift', $shift);
                break;
            case 'sweeping_pi':
                $query = LogbookSweepingPI::with('tenant');
                break;
            case 'rotasi':
                $query = LogbookRotasi::with('creator')->where('status', 'approved');
                break;
            case 'chief':
                $query = LogbookChief::with('createdBy')->where('status', 'approved');
                if ($shift) $query->where('shift', $shift);
                break;
            default:
                return collect();
        }

        $dateColumn = ($formType === 'sweeping_pi') ? 'created_at' : 'date';
        if ($startDate) $query->whereDate($dateColumn, '>=', $startDate);
        if ($endDate) $query->whereDate($dateColumn, '<=', $endDate);

        return $query->latest($dateColumn)->get()->map(function ($item) use ($formType) {
            return (object) [
                'id' => $item->id ?? $item->logbookID ?? $item->sweepingpiID,
                'date' => $item->date ?? $item->created_at,
                'form_type' => $formType,
                'officer' => $item->senderBy->name ?? $item->creator->name ?? $item->createdBy->name ?? $item->tenant->name ?? 'N/A',
                'shift' => $item->shift ?? 'N/A',
                'status' => $item->status ?? 'approved',
            ];
        });
    }

    private function getChecklistData($filters = [])
    {
        $formType = $filters['form_type'] ?? null;
        $typesToQuery = $formType ? [$formType] : ['kendaraan', 'penyisiran', 'senpi', 'pencatatan_pi', 'manual_book'];
        $data = collect();

        foreach ($typesToQuery as $type) {
            $data = $data->merge($this->fetchChecklistRecords($type, $filters));
        }
        return $data->sortByDesc('date');
    }

    private function fetchChecklistRecords($formType, $filters)
    {
        $query = null;
        $startDate = $filters['start_date'] ?? null;
        $endDate = $filters['end_date'] ?? null;
        $shift = $filters['shift'] ?? null;

        switch ($formType) {
            case 'kendaraan':
                $query = ChecklistKendaraan::with('sender')->where('status', 'approved');
                break;
            case 'penyisiran':
                $query = ChecklistPenyisiran::with('sender')->where('status', 'approved');
                break;
            case 'senpi':
                $query = ChecklistSenpi::query(); // status is always approved
                break;
            case 'pencatatan_pi':
                $query = FormPencatatanPI::with('sender')->where('status', 'approved');
                break;
            case 'manual_book':
                $query = ManualBook::with('creator')->where('status', 'approved');
                if ($shift) $query->where('shift', $shift);
                break;
            default:
                return collect();
        }

        if ($startDate) $query->whereDate('date', '>=', $startDate);
        if ($endDate) $query->whereDate('date', '<=', $endDate);

        return $query->latest('date')->get()->map(function ($item) use ($formType) {
            return (object) [
                'id' => $item->id,
                'date' => $item->date,
                'form_type' => $formType,
                'officer' => $item->sender->name ?? $item->creator->name ?? $item->name ?? 'N/A',
                'shift' => $item->shift ?? 'N/A',
                'status' => $item->status ?? 'approved',
            ];
        });
    }

    public function preview(Request $request, PreviewService $previewService, $category, $id)
    {
        $formType = $request->query('form_type');
        $preview = $previewService->getPreview($category, $id, $formType);

        // Check if the preview is a View instance
        if ($preview instanceof \Illuminate\View\View) {
            $viewName = $preview->getName();
            $viewData = $preview->getData();

            // Return the wrapper view with the original view's name and data
            return view('history.preview', [
                'content_view' => $viewName,
                'data' => $viewData
            ]);
        }

        // Fallback for non-view responses
        return $preview;
    }
}