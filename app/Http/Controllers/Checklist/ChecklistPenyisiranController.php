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
        // 1. Ambil semua item dari database berdasarkan type penyisiran
        $items = ChecklistItem::where('type', 'penyisiran')
            ->orderBy('id')
            ->get();

        // 2. Format data untuk checklist penyisiran - STRUKTUR YANG SESUAI DENGAN VIEW
        $penyisiranChecklist = $items->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name
            ];
        })->values()->all();

        // 3. Buat instance baru checklist
        $checklist = new ChecklistPenyisiran();

        // 4. Ambil data user untuk dropdown
        $officers = User::whereHas('role', fn($q) => $q->where('name', Role::OFFICER))
            ->where('id', Auth::id())
            ->first();

        $supervisors = User::whereHas('role', fn($q) => $q->where('name', Role::SUPERVISOR))
            ->get();

        // 5. Kirim data yang sudah diformat ke view
        return view('checklist.checklistPenyisiran', compact(
            'penyisiranChecklist',
            'officers',
            'supervisors',
            'checklist'
        ));
    }

    public function storeChecklistPenyisiran(Request $request)
    {
        Log::info('Request data:', $request->all());

        // 1. Validasi input dengan field names yang sesuai
        $validated = $request->validate([
            'date' => 'required|date',
            'jam' => 'required|string',
            'grup' => 'required|in:A,B,C',
            'sender_id' => 'required|exists:users,id',
            'signature' => 'required|string',
            'received_id' => 'required|exists:users,id',  // Fixed field name
            'approved_id' => 'required|exists:users,id',   // Fixed field name
            'items' => 'required|array',
            'items.*.temuan_ya' => 'nullable|in:1',
            'items.*.temuan_tidak' => 'nullable|in:0',
            'items.*.kondisi_baik' => 'nullable|in:1',
            'items.*.kondisi_rusak' => 'nullable|in:1',
            'items.*.notes' => 'nullable|string|max:1000',
        ]);

        Log::info('Validated data:', $validated);

        // 2. Custom validation
        $customValidator = Validator::make($request->all(), [], []);
        $customValidator->after(function ($validator) use ($request) {
            if (!$request->has('items') || empty($request->items)) {
                $validator->errors()->add('items', 'Tidak ada data checklist yang dikirim');
                return;
            }

            foreach ($request->items as $itemId => $item) {
                // Check temuan (findings)
                $hasTemuan = (isset($item['temuan_ya']) && $item['temuan_ya'] == '1') ||
                    (isset($item['temuan_tidak']) && $item['temuan_tidak'] == '0');

                // Check kondisi (condition)
                $hasKondisi = (isset($item['kondisi_baik']) && $item['kondisi_baik'] == '1') ||
                    (isset($item['kondisi_rusak']) && $item['kondisi_rusak'] == '1');

                // Validation rules based on item number
                // Items 1, 2, 3 must have temuan (findings)
                if (in_array($itemId, ['1', '2', '3']) && !$hasTemuan) {
                    $validator->errors()->add(
                        "items.{$itemId}.temuan",
                        "Item nomor {$itemId} harus memiliki pilihan temuan (YA/TIDAK)"
                    );
                }

                // Item 4 must have kondisi (condition)
                if ($itemId == '4' && !$hasKondisi) {
                    $validator->errors()->add(
                        "items.{$itemId}.kondisi",
                        "Item nomor {$itemId} harus memiliki pilihan kondisi (BAIK/RUSAK)"
                    );
                }
            }
        });

        if ($customValidator->fails()) {
            return redirect()->back()->withErrors($customValidator)->withInput();
        }

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

            // 5. Create main checklist record
            $checklist = ChecklistPenyisiran::create([
                'id' => $id,
                'date' => Carbon::parse($validated['date'])->format('Y-m-d'),
                'type' => 'penyisiran',
                'time' => $validated['jam'],
                'grup' => $validated['grup'],
                'status' => 'submitted',
                'sender_id' => $validated['sender_id'],
                'senderSignature' => $signature,
                'received_id' => $validated['received_id'],    // Fixed field name
                'approved_id' => $validated['approved_id'],     // Fixed field name
            ]);

            Log::info('Checklist created:', $checklist->toArray());

            // 6. Save checklist item details
            foreach ($validated['items'] as $itemId => $result) {
                Log::info("Processing item {$itemId}:", $result);

                // Determine findings value
                $isfindings = null;
                if (isset($result['temuan_ya']) && $result['temuan_ya'] == '1') {
                    $isfindings = true; // Ya
                } elseif (isset($result['temuan_tidak']) && $result['temuan_tidak'] == '0') {
                    $isfindings = false; // Tidak
                }

                // Determine condition value
                $iscondition = null;
                if (isset($result['kondisi_baik']) && $result['kondisi_baik'] == '1') {
                    $iscondition = true; // Baik
                } elseif (isset($result['kondisi_rusak']) && $result['kondisi_rusak'] == '1') {
                    $iscondition = false; // Rusak
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

                // Verify checklist item exists
                if (!DB::table('checklist_items')->where('id', $itemId)->exists()) {
                    throw new \Exception("Checklist item dengan ID {$itemId} tidak ditemukan di database");
                }

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

    // // Method untuk update (PUT)
    // public function update(Request $request, $id)
    // {
    //     // 1. Cari checklist yang akan diupdate
    //     $checklist = ChecklistPenyisiran::findOrFail($id);

    //     // 2. Validasi yang sama seperti store
    //     $validated = $request->validate([
    //         'date' => 'required|date',
    //         'jam' => 'required|string',
    //         'grup' => 'required|in:A,B,C',
    //         'sender_id' => 'required|exists:users,id',
    //         'items' => 'required|array',
    //         'items.*.temuan_ya' => 'nullable|boolean',
    //         'items.*.temuan_tidak' => 'nullable|boolean',
    //         'items.*.kondisi_baik' => 'nullable|boolean',
    //         'items.*.kondisi_rusak' => 'nullable|boolean',
    //         'items.*.notes' => 'nullable|string|max:1000',
    //     ]);

    //     DB::beginTransaction();

    //     try {

    //         // 4. Update record utama
    //         $checklist->update([
    //             'date' => $validated['date'],
    //             'jam' => $validated['jam'],
    //             'status' => 'submitted',
    //             'grup' => $validated['grup'],
    //             'sender_id' => $validated['sender_id'],
    //         ]);

    //         // 5. Hapus detail lama dan buat yang baru
    //         $checklist->details()->delete();

    //         foreach ($validated['items'] as $itemKey => $result) {
    //             $temuanYa = isset($result['temuan_ya']) && $result['temuan_ya'] ? true : false;
    //             $temuanTidak = isset($result['temuan_tidak']) && $result['temuan_tidak'] ? false : null;
    //             $kondisiBaik = isset($result['kondisi_baik']) && $result['kondisi_baik'] ? true : false;
    //             $kondisiRusak = isset($result['kondisi_rusak']) && $result['kondisi_rusak'] ? true : false;

    //             ChecklistPenyisiranDetail::create([
    //                 'penyisiran_checklist_id' => $checklist->id,
    //                 'item_key' => $itemKey,
    //                 'temuan_ya' => $temuanYa,
    //                 'temuan_tidak' => $temuanTidak,
    //                 'kondisi_baik' => $kondisiBaik,
    //                 'kondisi_rusak' => $kondisiRusak,
    //                 'notes' => $result['notes'] ?? null,
    //             ]);
    //         }

    //         DB::commit();

    //         return redirect()
    //             ->route('penyisiran.checklist.index')
    //             ->with('success', 'Checklist penyisiran berhasil diupdate.');
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         Log::error('Error updating penyisiran checklist: ' . $e->getMessage());

    //         return redirect()
    //             ->back()
    //             ->with('error', 'Gagal mengupdate checklist: ' . $e->getMessage())
    //             ->withInput();
    //     }
    // }
}
