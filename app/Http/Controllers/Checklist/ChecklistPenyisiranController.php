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
        return view('checklist.checklistPenyisiran', compact(
            'penyisiranChecklist',
            'currentOfficer',
            'allOfficers',
            'supervisors',
            'checklist'
        ));
    }

    public function storeChecklistPenyisiran(Request $request)
    {
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
}
