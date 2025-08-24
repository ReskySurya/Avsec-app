<?php

namespace App\Http\Controllers\Checklist;

use App\Http\Controllers\Controller;
use App\Models\ChecklistItem;
use App\Models\ChecklistKendaraan;
use App\Models\ChecklistKendaraanDetail;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ChecklistKendaraanController extends Controller
{
    public function indexChecklistKendaraan()
    {
        // 1. Ambil semua item dari database dan kelompokkan langsung berdasarkan tipe
        $items = ChecklistItem::orderBy('id')->get();
        $groupedItems = $items->groupBy('type');

        // 2. Format data untuk checklist mobil - STRUKTUR FLAT (tanpa kategori)
        $mobilItems = $groupedItems->get('mobil', collect());
        $mobilChecklist = $mobilItems->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name
            ];
        })->values()->all();

        // 3. Format data untuk checklist motor - STRUKTUR FLAT (tanpa kategori)
        $motorItems = $groupedItems->get('motor', collect());
        $motorChecklist = $motorItems->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name
            ];
        })->values()->all();

        // 4. Buat instance baru checklist
        $checklist = new ChecklistKendaraan();

        // 5. Ambil data user untuk dropdown
        $officers = User::whereHas('role', fn($q) => $q->where('name', Role::OFFICER))
            ->where('id', '!=', Auth::id())
            ->get();

        $supervisors = User::whereHas('role', fn($q) => $q->where('name', Role::SUPERVISOR))->get();

        // 6. Kirim data yang sudah diformat ke view
        return view('checklist.checklistKendaraan', compact('mobilChecklist', 'motorChecklist', 'checklist', 'officers', 'supervisors'));
    }

    public function store(Request $request)
    {
        // 1. Validasi yang disesuaikan dengan skema database
        $validated = $request->validate([
            'date' => 'required|date',
            'type' => 'required|in:mobil,motor',
            'shift' => 'required|in:pagi,malam',
            'items' => 'required|array',
            'items.*.is_ok' => 'required|boolean',
            'items.*.notes' => 'nullable|string|max:1000', // Validasi tambahan untuk notes
            'signature' => 'required|string',
            'receivedID' => 'required|exists:users,id',
            'approvedID' => 'required|exists:users,id',
        ]);

        DB::beginTransaction();
        try {
            // Hapus prefix dari data base64 signature (sudah benar)
            $signature = $validated['signature'];
            if (str_starts_with($signature, 'data:image/')) {
                $signature = substr($signature, strpos($signature, ',') + 1);
            }

            // 2. Pembuatan ID dinamis sesuai format di migrasi
            $prefix = $validated['type'] === 'mobil' ? 'CML' : 'CMT';
            $datePart = Carbon::parse($validated['date'])->format('ymd');

            // Loop untuk memastikan ID unik (best practice)
            do {
                $randomPart = strtoupper(Str::random(5));
                $id = "{$prefix}-{$datePart}-{$randomPart}";
            } while (ChecklistKendaraan::find($id));

            // 3. Buat record utama ChecklistKendaraan dengan data yang sudah diformat
            $checklist = ChecklistKendaraan::create([
                'id' => $id,
                'date' => Carbon::parse($validated['date'])->format('Y-m-d'),
                'type' => $validated['type'],
                'shift' => $validated['shift'],
                'status' => 'submitted',
                'sender_id' => Auth::id(),
                'received_id' => $validated['receivedID'],
                'approved_id' => $validated['approvedID'],
                'senderSignature' => $signature,
            ]);

            // 5. Simpan detail item checklist (sudah benar)
            foreach ($validated['items'] as $itemId => $result) {
                ChecklistKendaraanDetail::create([
                    'checklist_kendaraan_id' => $checklist->id,
                    'checklist_item_id' => $itemId,
                    'is_ok' => $result['is_ok'],
                    'notes' => $result['notes'] ?? null,
                ]);
            }

            DB::commit();
            return redirect()->route('checklist.kendaraan.index')->with('success', 'Checklist kendaraan berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan checklist: ' . $e->getMessage())->withInput();
        }
    }
}
