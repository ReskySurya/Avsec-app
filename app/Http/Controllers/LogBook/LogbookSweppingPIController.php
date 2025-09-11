<?php

namespace App\Http\Controllers\LogBook;

use App\Http\Controllers\Controller;
use App\Models\Logbook;
use App\Models\Location;
use App\Models\LogbookSweepingPI;
use App\Models\LogbookSweepingPIDetail;
use App\Models\NoteSweepingPI;
use App\Models\ProhibitedItem;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class LogbookSweppingPIController extends Controller
{
    //superadmin tampilan sweeping PI
    public function indexSweepingPI(Request $request)
    {
        $search = $request->input('search');
        $tenantQuery = Tenant::query();
        if ($search) {
            $tenantQuery->where('tenant_name', 'like', '%' . $search . '%')
                        ->orWhere('tenantID', 'like', '%' . $search . '%');
        }
        $tenantList = $tenantQuery->orderBy('tenantID')->get();

        return view('superadmin.sweeping-pi.index', [
            'tenantList' => $tenantList,
            'search' => $search
        ]);
    }
    public function indexSweepingPIManage(Request $request, $tenantID)
    {
        $tenant = Tenant::where('tenantID', $tenantID)->firstOrFail();

        $prohibitedItems = ProhibitedItem::where('tenantID', $tenantID)
            ->orderBy('items_name')
            ->get();

        // Get filter inputs
        $filterBulan = $request->input('filter_bulan');
        $filterTahun = $request->input('filter_tahun');

        $sweepingPI = LogbookSweepingPI::where('tenantID', $tenantID)
            ->when($filterBulan, function ($query, $bulan) {
                return $query->where('bulan', $bulan);
            })
            ->when($filterTahun, function ($query, $tahun) {
                return $query->where('tahun', $tahun);
            })
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->get();

        return view('superadmin.sweeping-pi.sweepingPIManage', [
            'tenant' => $tenant,
            'tenantID' => $tenantID,
            'prohibitedItems' => $prohibitedItems,
            'sweepingPI' => $sweepingPI,
            'filterBulan' => $filterBulan, // Pass filter values back
            'filterTahun' => $filterTahun,
        ]);
    }
    public function storeSweepingPI(Request $request)
    {
        $request->validate([
            'tenantID' => 'required|exists:tenants,tenantID',
            'bulan' => 'required|integer|between:1,12',
            'tahun' => 'required|integer|between:2020,2030',
        ]);

        $tenantID = $request->tenantID;
        $month = (int) $request->bulan;
        $year = (int) $request->tahun;

        // Validate tenant exists
        $tenant = Tenant::where('tenantID', $tenantID)->firstOrFail();

        // Check if entry already exists for this tenant, month, and year
        $existing = LogbookSweepingPI::where('tenantID', $tenantID)
            ->where('bulan', $month)
            ->where('tahun', $year)
            ->first();

        if ($existing) {
            return redirect()->back()->with('error', 'Data untuk tenant, bulan, dan tahun ini sudah ada!');
        }

        // Get prohibited items from master data for this tenant
        $prohibitedItems = ProhibitedItem::where('tenantID', $tenantID)
            ->orderBy('items_name')
            ->get(['id', 'items_name', 'quantity']);

        // If no prohibited items found for this tenant, use default items or throw error
        if ($prohibitedItems->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada prohibited items yang dikonfigurasi untuk tenant ini. Silakan hubungi administrator.');
        }

        // Create logbook entry
        $logbook = LogbookSweepingPI::create([
            'tenantID' => $tenantID,
            'bulan' => $month,
            'tahun' => $year,
        ]);

        // Initialize detail data for the new logbook
        Log::info("Initializing checklist data for new logbook: {$logbook->sweepingpiID}");

        DB::beginTransaction();
        try {
            foreach ($prohibitedItems as $item) {
                // Create new detail record with all days set to false (0)
                $detailData = [
                    'sweepingpiID' => $logbook->sweepingpiID,
                    'item_name_pi' => $item->items_name,
                    'quantity' => $item->quantity
                ];

                // Initialize all days for this month as unchecked (0)
                for ($day = 1; $day <= 31; $day++) {
                    $field = 'tanggal_' . $day;
                    $detailData[$field] = 0; // false/unchecked
                }

                // Create the detail record
                LogbookSweepingPIDetail::create($detailData);
            }

            DB::commit();
            Log::info("Successfully initialized checklist data for {$prohibitedItems->count()} items");

            // Add success message for new initialization
            $successMessage = 'Logbook baru berhasil diinisialisasi untuk ' .
                \Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y') .
                '. Anda dapat mulai melakukan checklist.';

            return redirect()->route('sweepingPI.manage.index', ['tenantID' => $tenantID])
                ->with('success', $successMessage);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error("Failed to initialize checklist data: " . $e->getMessage());

            return redirect()->back()->with('error', 'Gagal menginisialisasi data checklist. Silakan coba lagi.');
        }
    }
    public function deleteSweepingPI($sweepingpiID)
    {
        $sweepingPI = LogbookSweepingPI::find($sweepingpiID);

        if (!$sweepingPI) {
            return redirect()->back()->with('error', 'Data tidak ditemukan!');
        }

        $tenantID = $sweepingPI->tenantID;
        $sweepingPI->delete();

        return redirect()->route('sweepingPI.manage.index', ['tenantID' => $tenantID])->with('success', 'Data berhasil dihapus!');
    }
    // superadmin tampilan sweeping PI detail
    public function indexSweepingPIDetail(Request $request, $tenantID, $month)
    {
        // Get parameters with defaults
        // $month = (int) ($request->get('month', date('n')));
        $year = (int) ($request->get('year', date('Y')));

        // Validate tenant exists
        $tenant = Tenant::where('tenantID', $tenantID)->firstOrFail();

        // Find existing logbook for this month/year
        $logbook = LogbookSweepingPI::where([
            'tenantID' => $tenantID,
            'bulan' => $month,
            'tahun' => $year,
        ])->first();

        // Get prohibited items from master data for this tenant
        $prohibitedItems = ProhibitedItem::where('tenantID', $tenantID)
            ->orderBy('items_name')
            ->get(['id', 'items_name', 'quantity']);

        // If no prohibited items found for this tenant, show error
        if ($prohibitedItems->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada prohibited items yang dikonfigurasi untuk tenant ini. Silakan hubungi administrator.');
        }

        // Get all sweeping PI records for this tenant (for history/navigation)
        $sweepingPI = LogbookSweepingPI::where('tenantID', $tenantID)
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->get();

        // Initialize variables
        $checklistData = [];
        $daysInMonth = \Carbon\Carbon::createFromDate($year, $month, 1)->daysInMonth;
        $totalMissed = 0;

        // If logbook exists, load the checklist data
        if ($logbook) {
            // Get existing detail records
            $existingDetails = LogbookSweepingPIDetail::where('sweepingpiID', $logbook->sweepingpiID)->get();

            Log::info("Loading existing checklist data for logbook: {$logbook->sweepingpiID}");

            // Build checklist data from existing records
            foreach ($existingDetails as $detail) {
                $itemIndex = $prohibitedItems->search(function ($item) use ($detail) {
                    return $item->items_name === $detail->item_name_pi;
                });

                if ($itemIndex !== false) {
                    $checklistData[$itemIndex] = [];
                    for ($day = 1; $day <= $daysInMonth; $day++) {
                        $field = 'tanggal_' . $day;
                        // Convert database value to boolean
                        $checklistData[$itemIndex][$day - 1] = (bool) $detail->$field;
                    }
                } else {
                    // Item exists in detail but not in current prohibited items
                    Log::warning("Found detail for item '{$detail->item_name_pi}' but item not found in current prohibited items list");
                }
            }

            // Calculate missed items (for statistics)
            $today = now();
            $currentDate = \Carbon\Carbon::createFromDate($year, $month, 1);

            if ($currentDate->month <= $today->month && $currentDate->year <= $today->year) {
                $maxDay = ($currentDate->month == $today->month && $currentDate->year == $today->year)
                    ? min($today->day, $daysInMonth)
                    : $daysInMonth;

                foreach ($prohibitedItems as $index => $item) {
                    if (isset($checklistData[$index])) {
                        for ($day = 0; $day < $maxDay; $day++) {
                            if (!($checklistData[$index][$day] ?? false)) {
                                $totalMissed++;
                            }
                        }
                    } else {
                        // If no data for this item, count all days as missed
                        $totalMissed += $maxDay;
                    }
                }
            }
        } else {
            // No logbook exists, initialize empty checklist data
            foreach ($prohibitedItems as $index => $item) {
                $checklistData[$index] = [];
                for ($day = 0; $day < $daysInMonth; $day++) {
                    $checklistData[$index][$day] = false;
                }
            }

            // Calculate missed items for empty logbook
            $today = now();
            $currentDate = \Carbon\Carbon::createFromDate($year, $month, 1);

            if ($currentDate->month <= $today->month && $currentDate->year <= $today->year) {
                $maxDay = ($currentDate->month == $today->month && $currentDate->year == $today->year)
                    ? min($today->day, $daysInMonth)
                    : $daysInMonth;

                $totalMissed = $prohibitedItems->count() * $maxDay;
            }
        }

        $dailyNotes = [];
        $notesFromDB = NoteSweepingPI::where('sweepingpiID', $logbook->sweepingpiID)
            ->orderBy('tanggal')
            ->get();

        // Convert notes dari database ke format yang diharapkan frontend
        foreach ($notesFromDB as $note) {
            // Extract day dari tanggal database
            $date = Carbon::parse($note->tanggal);
            if ($date->month == $month && $date->year == $year) {
                $dayIndex = $date->day - 1; // Convert ke 0-based index
                $dailyNotes[$dayIndex] = $note->notes;
            }
        }

        // Initialize empty notes untuk hari yang tidak ada notes
        for ($day = 0; $day < $daysInMonth; $day++) {
            if (!isset($dailyNotes[$day])) {
                $dailyNotes[$day] = '';
            }
        }

        return view('.superadmin.sweeping-pi.detailSweepingPI', [
            'tenant' => $tenant,
            'logbook' => $logbook,
            'prohibitedItems' => $prohibitedItems,
            'sweepingPI' => $sweepingPI,
            'checklistData' => $checklistData,
            'dailyNotes' => $dailyNotes,
            'month' => $month,
            'year' => $year,
            'daysInMonth' => $daysInMonth,
            'totalMissed' => $totalMissed,
        ]);
    }

    //officer tampilan logbook sweeping PI
    public function index(Request $request)
    {
        $search = $request->input('search');

        $tenantQuery = Tenant::query();

        if ($search) {
            $tenantQuery->where('tenant_name', 'like', '%' . $search . '%')
                        ->orWhere('tenantID', 'like', '%' . $search . '%');
        }

        $tenantList = $tenantQuery->orderBy('tenantID')->get();

        return view('logbook.sweepingpi.logbookSweppingPI', [
            'tenantList' => $tenantList,
            'search' => $search // Pass search term back
        ]);
    }
    public function indexLogbookSweepingPIDetail(Request $request, $tenantID): View
    {
        // Get parameters with defaults
        $tenantID = $request->get('tenantID', $tenantID);
        $month = (int) ($request->get('month', date('n')));
        $year = (int) ($request->get('year', date('Y')));

        // Validate tenant exists
        $tenant = Tenant::where('tenantID', $tenantID)->firstOrFail();

        // Get or create logbook for this month/year
        $logbook = LogbookSweepingPI::firstOrCreate([
            'tenantID' => $tenantID,
            'bulan' => $month,
            'tahun' => $year,
        ]);

        // Get prohibited items from master data for this tenant
        $prohibitedItems = ProhibitedItem::where('tenantID', $tenantID)
            ->orderBy('items_name')
            ->get(['id', 'items_name', 'quantity']);

        // If no prohibited items found for this tenant, use default items or throw error
        if ($prohibitedItems->isEmpty()) {
            // Option 2: Throw error if no items configured
            throw new \Exception("Tidak ada prohibited items yang dikonfigurasi untuk tenant ini. Silakan hubungi administrator.");
        }

        // Check if detail data already exists
        $existingDetails = LogbookSweepingPIDetail::where('sweepingpiID', $logbook->sweepingpiID)->get();

        // Initialize checklist data array
        $checklistData = [];
        $daysInMonth = \Carbon\Carbon::createFromDate($year, $month, 1)->daysInMonth;

        if ($existingDetails->isEmpty()) {
            // Data belum ada - Inisiasi data baru
            Log::info("Initializing checklist data for new logbook: {$logbook->sweepingpiID}");

            DB::beginTransaction();
            try {
                foreach ($prohibitedItems as $index => $item) {
                    // Create new detail record with all days set to false (0)
                    $detailData = [
                        'sweepingpiID' => $logbook->sweepingpiID,
                        'item_name_pi' => $item->items_name,
                        'quantity' => $item->quantity
                    ];

                    // Initialize all days for this month as unchecked (0)
                    for ($day = 1; $day <= 31; $day++) {
                        $field = 'tanggal_' . $day;
                        $detailData[$field] = 0; // false/unchecked
                    }

                    // Create the detail record
                    LogbookSweepingPIDetail::create($detailData);

                    // Initialize checklistData array for frontend
                    $checklistData[$index] = [];
                    for ($day = 0; $day < $daysInMonth; $day++) {
                        $checklistData[$index][$day] = false;
                    }
                }

                DB::commit();
                Log::info("Successfully initialized checklist data for {$prohibitedItems->count()} items");
            } catch (\Exception $e) {
                DB::rollback();
                Log::error("Failed to initialize checklist data: " . $e->getMessage());
                throw $e;
            }
        } else {
            // Data sudah ada - Load existing data
            Log::info("Loading existing checklist data for logbook: {$logbook->sweepingpiID}");

            foreach ($existingDetails as $detail) {
                $itemIndex = $prohibitedItems->search(function ($item) use ($detail) {
                    return $item->items_name === $detail->item_name_pi;
                });

                if ($itemIndex !== false) {
                    $checklistData[$itemIndex] = [];
                    for ($day = 1; $day <= $daysInMonth; $day++) {
                        $field = 'tanggal_' . $day;
                        // Convert database value to boolean
                        $checklistData[$itemIndex][$day - 1] = (bool) $detail->$field;
                    }
                } else {
                    // Item exists in detail but not in current prohibited items
                    // This might happen if prohibited items are updated after data creation
                    Log::warning("Found detail for item '{$detail->item_name_pi}' but item not found in current prohibited items list");
                }
            }

            // Handle case where new prohibited items were added after initial creation
            foreach ($prohibitedItems as $index => $item) {
                if (!isset($checklistData[$index])) {
                    Log::info("Adding new prohibited item to existing logbook: {$item->items_name}");

                    // Create detail record for new item
                    $detailData = [
                        'sweepingpiID' => $logbook->sweepingpiID,
                        'item_name_pi' => $item->items_name,
                        'quantity' => $item->quantity
                    ];

                    // Initialize all days as unchecked
                    for ($day = 1; $day <= 31; $day++) {
                        $field = 'tanggal_' . $day;
                        $detailData[$field] = 0;
                    }

                    LogbookSweepingPIDetail::create($detailData);

                    // Initialize frontend data
                    $checklistData[$index] = [];
                    for ($day = 0; $day < $daysInMonth; $day++) {
                        $checklistData[$index][$day] = false;
                    }
                }
            }
        }

        $dailyNotes = [];
        $notesFromDB = NoteSweepingPI::where('sweepingpiID', $logbook->sweepingpiID)
            ->orderBy('tanggal')
            ->get();

        // Convert notes dari database ke format yang diharapkan frontend
        foreach ($notesFromDB as $note) {
            // Extract day dari tanggal database
            $date = Carbon::parse($note->tanggal);
            if ($date->month == $month && $date->year == $year) {
                $dayIndex = $date->day - 1; // Convert ke 0-based index
                $dailyNotes[$dayIndex] = $note->notes;
            }
        }

        // Initialize empty notes untuk hari yang tidak ada notes
        for ($day = 0; $day < $daysInMonth; $day++) {
            if (!isset($dailyNotes[$day])) {
                $dailyNotes[$day] = '';
            }
        }

        // Calculate statistics
        // $statistics = $this->calculateLogbookStatistics($logbook, $prohibitedItems, $checklistData, $daysInMonth);

        // Add success message for new initialization
        if ($existingDetails->isEmpty()) {
            session()->flash('success', 'Logbook baru berhasil diinisialisasi untuk ' .
                \Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y') .
                '. Anda dapat mulai melakukan checklist.');
        }

        return view('logbook.sweepingpi.detailLogbookSweepingPI', compact(
            'tenant',
            'logbook',
            'month',
            'year',
            'prohibitedItems',
            'checklistData',
            'dailyNotes', 
            'daysInMonth'
        ));
    }
    public function saveProgressSweepingPI(Request $request)
    {
        try {
            // Validate request
            $validator = Validator::make($request->all(), [
                'logbook_id' => 'required|string|exists:logbook_sweeping_pi,sweepingpiID',
                'items_name' => 'required|array|min:1',
                'items_name.*' => 'required|string|max:255',
                'quantity' => 'required|array|min:1',
                'quantity.*' => 'required|integer|min:1',
                'checklist_data' => 'required|array',
                // 'notes' => 'nullable|string|max:1000',
                // New validation for daily notes
                'daily_notes' => 'nullable|array',
                // 'daily_notes.*.tanggal' => 'required|date',
                // 'daily_notes.*.notes' => 'required|string|max:2000'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak valid: ' . $validator->errors()->first(),
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            // Get logbook
            $logbook = LogbookSweepingPI::where('sweepingpiID', $request->logbook_id)->firstOrFail();

            // Update main notes (existing functionality)
            // $logbook->update([
            //     'notes' => $request->notes
            // ]);

            // Process daily notes (new functionality)
            // Process daily notes (FIXED VERSION)
            $savedNotes = 0;
            if ($request->has('daily_notes') && is_array($request->daily_notes)) {
                Log::info('Daily notes received:', ['daily_notes' => $request->daily_notes]);

                foreach ($request->daily_notes as $dailyNote) {
                    // Pastikan ini adalah array dengan key 'tanggal' dan 'notes'
                    if (
                        is_array($dailyNote) &&
                        isset($dailyNote['tanggal']) &&
                        isset($dailyNote['notes']) &&
                        !empty($dailyNote['notes'])
                    ) {

                        // Check if note already exists for this date
                        $existingNote = NoteSweepingPI::where('sweepingpiID', $logbook->sweepingpiID)
                            ->where('tanggal', $dailyNote['tanggal'])
                            ->first();

                        if ($existingNote) {
                            // Update existing note
                            $existingNote->update([
                                'notes' => $dailyNote['notes']
                            ]);
                            Log::info('Updated existing note', [
                                'date' => $dailyNote['tanggal'],
                                'note' => $dailyNote['notes']
                            ]);
                        } else {
                            // Create new note
                            NoteSweepingPI::create([
                                'sweepingpiID' => $logbook->sweepingpiID,
                                'tanggal' => $dailyNote['tanggal'],
                                'notes' => $dailyNote['notes']
                            ]);
                            Log::info('Created new note', [
                                'date' => $dailyNote['tanggal'],
                                'note' => $dailyNote['notes']
                            ]);
                        }
                        $savedNotes++;
                    }
                }
            }

            Log::info('Total notes saved:', ['count' => $savedNotes]);

            // Validate checklist data structure (existing functionality)
            $this->validateChecklistData($request->checklist_data, $request->items_name);

            // Process checklist data (existing functionality)
            $savedItems = 0;
            foreach ($request->items_name as $index => $itemName) {
                if (isset($request->checklist_data[$index])) {

                    // Find or create detail record
                    $detail = LogbookSweepingPIDetail::where('sweepingpiID', $logbook->sweepingpiID)
                        ->where('item_name_pi', $itemName)
                        ->first();

                    if (!$detail) {
                        // Create new detail record
                        $detailData = [
                            'sweepingpiID' => $logbook->sweepingpiID,
                            'item_name_pi' => $itemName,
                            'quantity' => $request->quantity[$index] ?? 0
                        ];

                        // Initialize all days as unchecked first
                        for ($day = 1; $day <= 31; $day++) {
                            $field = 'tanggal_' . $day;
                            $detailData[$field] = 0;
                        }

                        $detail = LogbookSweepingPIDetail::create($detailData);
                    }

                    // Update checklist data for this item
                    $updateData = [];
                    foreach ($request->checklist_data[$index] as $dayIndex => $isChecked) {
                        $day = $dayIndex + 1; // Convert 0-based to 1-based
                        if ($day >= 1 && $day <= 31) {
                            $field = 'tanggal_' . $day;
                            $updateData[$field] = $isChecked ? 1 : 0;
                        }
                    }

                    if (!empty($updateData)) {
                        $detail->update($updateData);
                        $savedItems++;
                    }
                }
            }

            DB::commit();

            // Calculate updated statistics
            // $statistics = $this->calculateUpdatedStatistics($logbook);

            Log::info("Sweeping PI progress saved successfully", [
                'logbook_id' => $logbook->sweepingpiID,
                'tenant_id' => $logbook->tenantID,
                'saved_items' => $savedItems,
                'saved_notes' => $savedNotes,
                'month' => $logbook->bulan,
                'year' => $logbook->tahun
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disimpan',
                'data' => [
                    'saved_items' => $savedItems,
                    'saved_notes' => $savedNotes,
                    // 'statistics' => $statistics,
                    'last_updated' => now()->format('Y-m-d H:i:s')
                ]
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollback();
            Log::error("Logbook not found for save progress", [
                'logbook_id' => $request->logbook_id ?? 'null'
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Logbook tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error saving sweeping PI progress: ' . $e->getMessage(), [
                'logbook_id' => $request->logbook_id ?? 'null',
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }
    private function validateChecklistData($checklistData, $itemsName)
    {
        if (!is_array($checklistData)) {
            throw new \Exception("Checklist data harus berupa array");
        }

        foreach ($checklistData as $itemIndex => $itemData) {
            if (!isset($itemsName[$itemIndex])) {
                throw new \Exception("Item index {$itemIndex} tidak ditemukan dalam daftar items");
            }

            if (!is_array($itemData)) {
                throw new \Exception("Data untuk item '{$itemsName[$itemIndex]}' harus berupa array");
            }

            // Validate day data
            foreach ($itemData as $dayIndex => $isChecked) {
                if (!is_numeric($dayIndex) || $dayIndex < 0 || $dayIndex > 30) {
                    throw new \Exception("Day index tidak valid: {$dayIndex}");
                }

                if (!is_bool($isChecked) && !in_array($isChecked, [0, 1, '0', '1', true, false])) {
                    throw new \Exception("Nilai checklist harus boolean atau 0/1");
                }
            }
        }

        return true;
    }
}
