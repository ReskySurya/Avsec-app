<?php

namespace App\Http\Controllers\Checklist;

use App\Http\Controllers\Controller;
use App\Models\ChecklistItem;
use App\Models\ChecklistItemKendaraan;
use App\Models\ChecklistKendaraan;
use App\Models\ChecklistKendaraanDetail;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ChecklistKendaraanController extends Controller
{
    public function indexChecklistKendaraan()
    {
        // 1. Ambil semua item dari database (Pastikan nama model benar: ChecklistItem)
        $items = ChecklistItem::orderBy('category')->orderBy('id')->get();

        // 2. Kelompokkan item berdasarkan tipe kendaraan (mobil/motor)
        $groupedItems = $items->groupBy('type');

        // 3. Format data untuk checklist mobil sesuai struktur yang diharapkan view
        $mobilItems = $groupedItems->get('mobil', collect());
        $mobilChecklist = $mobilItems->groupBy('category')
            ->map(function ($items, $categoryName) {
                return [
                    'name' => strtoupper($categoryName),
                    'items' => $items->map(fn($item) => ['id' => $item->id, 'name' => $item->name])->values()->all(),
                ];
            })
            ->values()
            ->map(function ($category, $index) {
                $category['letter'] = chr(65 + $index); // Menghasilkan A, B, C, ...
                return $category;
            })
            ->all();

        // 4. Format data untuk checklist motor
        $motorItems = $groupedItems->get('motor', collect());
        $motorChecklist = $motorItems->groupBy('category')
            ->map(function ($items, $categoryName) {
                return [
                    'name' => strtoupper($categoryName),
                    'items' => $items->map(fn($item) => ['id' => $item->id, 'name' => $item->name])->values()->all(),
                ];
            })
            ->values()
            ->all();

        // INI PERUBAHAN UTAMA: Buat instance baru, bukan mengambil data lama
        $checklist = new ChecklistKendaraan();

        // Ambil data user untuk dropdown
        $officers = User::whereHas('role', fn($q) => $q->where('name', Role::OFFICER))
            ->where('id', '!=', Auth::id()) // Pastikan tidak mengambil user yang sedang login
            ->get();

        $supervisors = User::whereHas('role', fn($q) => $q->where('name', Role::SUPERVISOR))->get();

        // 5. Kirim data yang sudah diformat ke view
        return view('checklist.kendaraan.checklistKendaraan', compact('mobilChecklist', 'motorChecklist', 'checklist', 'officers', 'supervisors'));
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

    public function showReceivedChecklist($type, $id)
    {
        try {
            // Validasi tipe kendaraan
            if (!in_array($type, ['mobil', 'motor'])) {
                abort(404, 'Tipe kendaraan tidak valid.');
            }

            // Ambil data checklist
            $checklist = ChecklistKendaraan::with(['sender', 'receiver', 'approver', 'details.item'])
                ->find($id);

            // Cek apakah data ditemukan
            if (!$checklist) {
                return redirect()->back()->with('error', 'Checklist kendaraan tidak ditemukan.');
            }

            // Verifikasi tipe dari URL cocok dengan tipe di database
            if ($checklist->type !== $type) {
                abort(404, 'Tipe kendaraan tidak cocok.');
            }

            // Buat variabel untuk menampung item yang sudah dikelompokkan
            $groupedItems = null;

            // Hanya kelompokkan jika tipenya mobil
            if ($type === 'mobil') {
                // Mengelompokkan relasi 'details' berdasarkan 'item.category'
                $groupedItems = $checklist->details->groupBy(function ($detail) {
                    // Mengambil kategori dari relasi item
                    return $detail->item->category;
                });
            }

            // PERBAIKAN: Nama view yang sesuai dengan struktur file Anda
            $viewName = 'officer.checklist.receivedChecklistKendaraan' . ucfirst($type);

            // Debug: Cek nama view yang dibuat
            Log::info('View name generated', [
                'viewName' => $viewName,
                'type' => $type,
                'exists' => view()->exists($viewName)
            ]);

            // Cek apakah view ada
            if (!view()->exists($viewName)) {
                Log::error('View not found', ['viewName' => $viewName]);
                return redirect()->back()->with('error', 'Template tidak ditemukan.');
            }

            return view($viewName, compact('checklist', 'groupedItems'));
        } catch (\Exception $e) {
            Log::error('Error in showReceivedChecklist', [
                'error' => $e->getMessage(),
                'type' => $type,
                'id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat data.');
        }
    }

    public function storeSignatureReceived(Request $request, ChecklistKendaraan $checklist)
    {
        // 1. Validasi input
        $request->validate([
            'receivedSignature' => 'required|string',
        ]);

        // 2. Ambil dan proses data base64
        $signature = $request->input('receivedSignature');
        if (str_starts_with($signature, 'data:image/')) {
            $signature = substr($signature, strpos($signature, ',') + 1);
        }

        // 3. Update signature dan status pada checklist
        $checklist->receivedSignature = $signature;
        $checklist->status = 'submitted';
        $checklist->save();

        // 4. (PENTING) Muat relasi yang dibutuhkan oleh view
        $checklist->load(['sender', 'receiver', 'approver', 'details.item']);

        $groupedItems = null;
        $type = $checklist->type;

        if ($type === 'mobil') {
            $groupedItems = $checklist->details->groupBy(function ($detail) {
                return $detail->item->category;
            });
        }

        // 5. Bangun nama view
        $viewName = 'officer.checklist.receivedChecklistKendaraan' . ucfirst($type);

        // 6. Cek apakah view ada
        if (!view()->exists($viewName)) {
            Log::error('View not found after saving signature', ['viewName' => $viewName]);
            return redirect()->route('dashboard.officer')->with('error', 'Template tidak ditemukan, namun data berhasil disimpan.');
        }

        // 7. Ganti return view() untuk menyertakan KEDUA variabel
        return view($viewName, compact('checklist', 'groupedItems'))
            ->with('success', 'Tanda tangan berhasil disimpan dan checklist telah disetujui.');
    }

    public function storeSignatureApproved(Request $request, ChecklistKendaraan $checklist)
    {
        // 1. Validasi input
        $request->validate([
            'approvedSignature' => 'required|string',
        ]);

        // 2. Ambil dan proses data base64
        $signature = $request->input('approvedSignature');
        if (str_starts_with($signature, 'data:image/')) {
            $signature = substr($signature, strpos($signature, ',') + 1);
        }

        // 3. Update signature dan status pada checklist
        $checklist->approvedSignature = $signature;
        $checklist->status = 'approved';
        $checklist->save();

        // 4. (PENTING) Muat relasi yang dibutuhkan oleh view
        $checklist->load(['sender', 'receiver', 'approver', 'details.item']);

        $groupedItems = null;
        $type = $checklist->type;

        if ($type === 'mobil') {
            $groupedItems = $checklist->details->groupBy(function ($detail) {
                return $detail->item->category;
            });
        }

        // 5. Bangun nama view
        $viewName = 'officer.checklist.detailChecklistKendaraan' . ucfirst($type);

        // 6. Cek apakah view ada
        if (!view()->exists($viewName)) {
            Log::error('View not found after saving signature', ['viewName' => $viewName]);
            return redirect()->route('supervisor.checklist-kendaraan.list')->with('error', 'Template tidak ditemukan, namun data berhasil disimpan.');
        }

        // 7. Ganti return view() untuk menyertakan KEDUA variabel
        return view($viewName, compact('checklist', 'groupedItems'))
            ->with('success', 'Tanda tangan berhasil disimpan dan checklist telah disetujui.');
    }

    public function show(ChecklistKendaraan $checklist)
    {
        // 1. Eager load relasi 'details' dan juga relasi 'item' di dalam details
        // Ini penting untuk menghindari N+1 query problem dan membuat performa lebih cepat.
        $checklist->load('details.item');

        // 2. Ambil semua detail dan kelompokkan berdasarkan kategori dari relasi item.
        // Hasilnya akan menjadi collection yang key-nya adalah nama kategori ('mesin', 'mekanik', 'lainlain')
        // dan value-nya adalah item-item yang termasuk dalam kategori tersebut.
        $groupedItems = $checklist->details->groupBy('item.category');

        // 3. Kirim data '$checklist' dan '$groupedItems' ke view.
        // Anda tidak perlu lagi melakukan query atau pengelompokan di dalam view.
        if ($checklist->type === 'mobil') {
            return view('supervisor.detailChecklistKendaraanMobil', compact('checklist', 'groupedItems'));
        } else {
            // Lakukan hal yang sama jika Anda punya view untuk motor
            // return view('supervisor.detailChecklistKendaraanMotor', compact('checklist', 'groupedItems'));

            // Sementara bisa diarahkan ke view yang sama jika strukturnya mirip
            return view('supervisor.detailChecklistKendaraanMotor', compact('checklist'));
        }
    }
}
