<?php

namespace App\Http\Controllers\Checklist;

use App\Http\Controllers\Controller;
use App\Models\ChecklistItem;
use App\Models\ChecklistPenyisiran;
use App\Models\ChecklistPenyisiranDetail;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ChecklistPenyisiranController extends Controller
{
    public function indexChecklistPenyisiran()
    {
        // 1. Ambil semua item dari database, diurutkan berdasarkan ID, lalu kelompokkan berdasarkan kategori
        $penyisiranChecklist = ChecklistItem::where('type', 'penyisiran')
            ->orderBy('id')
            ->get()
            ->groupBy('category');

        // 3. Buat instance baru checklist
        $checklist = new ChecklistPenyisiran();

        // 4. Ambil data user untuk dropdown
        $currentOfficer = User::whereHas('role', fn($q) => $q->where('name', Role::OFFICER))
            ->where('id', Auth::id())
            ->first();

        // Ambil semua officer untuk dropdown "Diserahkan kepada"
        $allOfficers = User::whereHas('role', fn($q) => $q->where('name', Role::OFFICER))
            ->where('id', '!=', Auth::id())
            ->get();

        // Ambil semua supervisor untuk dropdown "Diketahui oleh"
        $supervisors = User::whereHas('role', fn($q) => $q->where('name', Role::SUPERVISOR))
            ->get();

        // 5. Kirim data yang sudah diformat ke view
        return view('checklist.penyisiran.checklistPenyisiran', compact(
            'penyisiranChecklist',
            'currentOfficer',
            'allOfficers',
            'supervisors',
            'checklist'
        ));
    }

    public function storeChecklistPenyisiran(Request $request)
    {

        $existingChecklist = ChecklistPenyisiran::where('date', $request->date)->first();

        if ($existingChecklist) {
            // Format tanggal agar mudah dibaca
            $formattedDate = Carbon::parse($existingChecklist->date)->isoFormat('D MMMM YYYY');

            // Buat pesan error sesuai permintaan
            $errorMessage = "Data untuk tanggal {$formattedDate} sudah diisi oleh Grup {$existingChecklist->grup}.";

            // Kembalikan ke halaman form dengan pesan error khusus untuk SweetAlert
            return redirect()
                ->back()
                ->with('duplicate_error', $errorMessage)
                ->withInput(); // withInput() agar data yang sudah diisi tidak hilang
        }

        Log::info('Request data:', $request->all());

        // 1. Validasi input dengan field names yang sesuai
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'jam' => 'required|string',
            'grup' => 'required|in:A,B,C',
            'sender_id' => 'required|exists:users,id',
            'signature' => 'required|string',
            'received_id' => 'required|exists:users,id',
            'approved_id' => 'required|exists:users,id',
            'items' => 'required|array|min:1',
        ]);

        // Custom validation untuk items
        $validator->after(function ($validator) use ($request) {
            if (!$request->has('items') || empty($request->items)) {
                $validator->errors()->add('items', 'Tidak ada data checklist yang dikirim');
                return;
            }

            foreach ($request->items as $itemId => $item) {
                // Periksa apakah item memiliki data
                $hasTemuan = isset($item['temuan']) && in_array($item['temuan'], ['ya', 'tidak']);
                $hasKondisi = isset($item['kondisi']) && in_array($item['kondisi'], ['baik', 'rusak']);

                // Berdasarkan aturan bisnis dari keterangan di form:
                // No. 1, 2 dan 3 di isi tanda centang pada kolom TEMUAN
                // No. 4 di isi tanda centang pada kolom KONDISI

                // Cari item di database untuk mengetahui urutannya
                $checklistItem = ChecklistItem::find($itemId);
                if (!$checklistItem) {
                    $validator->errors()->add("items.{$itemId}", "Item dengan ID {$itemId} tidak ditemukan");
                    continue;
                }

                // Implementasi aturan validasi berdasarkan urutan item
                // Anda perlu menyesuaikan logika ini dengan kebutuhan bisnis Anda
                // Contoh: jika item 1-3 harus punya temuan, item 4 harus punya kondisi

                if (!$hasTemuan && !$hasKondisi) {
                    $validator->errors()->add(
                        "items.{$itemId}",
                        "Item '{$checklistItem->name}' harus memiliki pilihan temuan atau kondisi"
                    );
                }
            }
        });

        if ($validator->fails()) {
            Log::error('Validation failed:', $validator->errors()->toArray());
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();
        Log::info('Validated data:', $validated);

        DB::beginTransaction();

        try {
            // 3. Generate unique ID
            $dateString = Carbon::parse($validated['date'])->format('ymd');
            $grupString = $validated['grup'];

            do {
                $randomPart = strtoupper(Str::random(4));
                $id = "PSR-{$dateString}-{$grupString}-{$randomPart}";
            } while (ChecklistPenyisiran::find($id));

            Log::info('Generated ID:', ['id' => $id]);

            // 4. Process signature data
            $signature = $validated['signature'];

            // Remove data URL prefix if exists to store pure base64
            if (preg_match('/^data:image\/(\w+);base64,/', $signature, $type)) {
                $signature = substr($signature, strpos($signature, ',') + 1);
            }

            // 5. Combine date and time
            $dateTime = Carbon::parse($validated['date'] . ' ' . $validated['jam']);

            // 6. Create main checklist record
            $checklist = ChecklistPenyisiran::create([
                'id' => $id,
                'date' => $dateTime->format('Y-m-d'),
                'time' => $dateTime->format('H:i:s'),
                'type' => 'penyisiran',
                'grup' => $validated['grup'],
                'status' => 'submitted',
                'sender_id' => $validated['sender_id'],
                'senderSignature' => $signature,
                'received_id' => $validated['received_id'],
                'approved_id' => $validated['approved_id'],
            ]);

            Log::info('Checklist created:', $checklist->toArray());

            // 7. Save checklist item details
            foreach ($validated['items'] as $itemId => $result) {
                Log::info("Processing item {$itemId}:", $result);

                // Verify checklist item exists
                if (!ChecklistItem::where('id', $itemId)->exists()) {
                    throw new \Exception("Checklist item dengan ID {$itemId} tidak ditemukan di database");
                }

                // Determine findings and condition values
                $isfindings = null;
                $iscondition = null;

                if (isset($result['temuan'])) {
                    $isfindings = ($result['temuan'] === 'ya') ? true : false;
                }

                if (isset($result['kondisi'])) {
                    $iscondition = ($result['kondisi'] === 'baik') ? true : false;
                }

                // Prepare detail data
                $detailData = [
                    'checklist_penyisiran_id' => $checklist->id,
                    'checklist_item_id' => (int) $itemId,
                    'isfindings' => $isfindings,
                    'iscondition' => $iscondition,
                    'notes' => !empty($result['notes']) ? $result['notes'] : null,
                ];

                Log::info("Detail data for item {$itemId}:", $detailData);

                ChecklistPenyisiranDetail::create($detailData);
            }

            DB::commit();

            return redirect()
                ->route('checklist.penyisiran.index')
                ->with('success', 'Checklist penyisiran berhasil disimpan dengan ID: ' . $id);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saving penyisiran checklist: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return redirect()
                ->back()
                ->with('error', 'Gagal menyimpan checklist: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function showReceivedChecklistPenyisiran($id)
    {
        // Eager load relasi yang dibutuhkan untuk efisiensi
        $checklist = ChecklistPenyisiran::with([
            'sender',
            'receiver',
            'approver',
            'details.item'
        ])
            ->find($id);

        // Cek apakah data ditemukan
        if (!$checklist) {
            return redirect()->back()->with('error', 'Checklist Penyisiran tidak ditemukan.');
        }

        // Kelompokkan detail berdasarkan kategori itemnya
        $groupedDetails = $checklist->details->groupBy(function ($detail) {
            return $detail->item->category;
        });

        // Kirim data ke view
        return view('officer.checklist.receivedChecklistPenyisiran', [
            'checklist' => $checklist,
            'groupedDetails' => $groupedDetails,
        ]);
    }

    public function storeReceivedSignaturePenyisiran(Request $request, ChecklistPenyisiran $checklist)
    {
        $request->validate([
            'receivedSignature' => 'required|string',
        ]);

        // Pastikan user yang login adalah user yang ditunjuk sebagai penerima
        if (Auth::id() !== $checklist->received_id) {
            return redirect()->back()->with('error', 'Anda tidak memiliki otorisasi untuk menandatangani checklist ini.');
        }

        // Proses dan simpan tanda tangan
        $signature = $request->input('receivedSignature');
        if (preg_match('/^data:image\/(\w+);base64,/', $signature, $type)) {
            $signature = substr($signature, strpos($signature, ',') + 1);
        }

        $checklist->receivedSignature = $signature;
        $checklist->save();

        return redirect()->route('officer.receivedChecklistPenyisiran.show', $checklist->id)->with('success', 'Tanda tangan berhasil disimpan.');
    }


    public function showDetailPenyisiran(ChecklistPenyisiran $checklist)
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
        return view('supervisor.detailChecklistPenyisiran', compact('checklist', 'groupedItems'));
    }

    public function storeSignatureApproved(Request $request, ChecklistPenyisiran $checklist)
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

        if ($type === 'penyisiran') {
            $groupedItems = $checklist->details->groupBy(function ($detail) {
                return $detail->item->category;
            });
        }

        // // // 5. Bangun nama view
        $viewName = 'officer.checklist.detailChecklistPenyisiran' . ucfirst($type);

        // 6. Cek apakah view ada
        if (!view()->exists($viewName)) {
            Log::error('View not found after saving signature', ['viewName' => $viewName]);
            return redirect()->route('supervisor.checklist-penyisiran.list')->with('error', 'Template tidak ditemukan, namun data berhasil disimpan.');
        }

        // 7. Ganti return view() untuk menyertakan KEDUA variabel
        return view('supervisor.list-checklist-penyisiran', compact('checklist', 'groupedItems'))
            ->with('success', 'Tanda tangan berhasil disimpan dan checklist telah disetujui.');
    }
}
