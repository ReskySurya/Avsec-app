<?php

namespace App\Http\Controllers\LogBook;

use App\Http\Controllers\Controller;
use App\Models\LogbookRotasiPSCP;
use App\Models\LogbookRotasiPSCPDetail;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class LogbookRotasiPSCPController extends Controller
{
    public function index()
    {
        $officers = User::whereHas('role', function ($q) {
            $q->where('name', Role::OFFICER);
        })
            ->select('id', 'name', 'nip')
            ->get()
            ->map(function ($officer) {
                $officer->display_name = substr($officer->nip, -4) . ' - ' . $officer->name;
                return $officer;
            });

        return view('logbook.logbookRotasiPscp', compact('officers'));
    }

    public function autosave(Request $request)
    {
        try {
            // Validasi input
            $validator = Validator::make($request->all(), [
                'start' => 'nullable|date_format:H:i',
                'end' => 'nullable|date_format:H:i',
                'pemeriksaan_dokumen' => 'nullable|exists:users,id',
                'pengatur_flow' => 'nullable|exists:users,id',
                'operator_xray' => 'nullable|exists:users,id',
                'hhmd_petugas' => 'nullable|exists:users,id',
                'hhmd_random' => 'nullable|integer|min:0',
                'hhmd_unpredictable' => 'nullable|integer|min:0',
                'manual_kabin_petugas' => 'nullable|exists:users,id',
                'cek_random_barang' => 'nullable|integer|min:0',
                'barang_unpredictable' => 'nullable|integer|min:0',
                'keterangan' => 'nullable|string|max:255',
                'row_id' => 'nullable|exists:logbook_rotasi_pscp_details,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $date = Carbon::today()->toDateString();

            // Cari atau buat logbook header
            $logbook = LogbookRotasiPSCP::firstOrCreate(
                [
                    'date' => $date,
                    'status' => 'draft'
                ],
                [
                    'id' => $this->createLogbookId(),
                    'created_by' => Auth::id()
                ]
            );

            // Prepare data untuk disimpan
            $dataToSave = [
                'logbook_id' => $logbook->id,
                'start' => $request->start,
                'end' => $request->end,
                'pemeriksaan_dokumen' => $request->pemeriksaan_dokumen,
                'pengatur_flow' => $request->pengatur_flow,
                'operator_xray' => $request->operator_xray,
                'hhmd_petugas' => $request->hhmd_petugas,
                'hhmd_random' => $request->hhmd_random,
                'hhmd_unpredictable' => $request->hhmd_unpredictable,
                'manual_kabin_petugas' => $request->manual_kabin_petugas,
                'cek_random_barang' => $request->cek_random_barang,
                'barang_unpredictable' => $request->barang_unpredictable,
                'keterangan' => $request->keterangan,
                'updated_at' => now()
            ];

            // Jika row_id ada, update existing record
            if ($request->row_id) {
                $detail = LogbookRotasiPSCPDetail::where('id', $request->row_id)
                    ->where('logbook_id', $logbook->id)
                    ->first();

                if ($detail) {
                    $detail->update($dataToSave);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Detail not found'
                    ], 404);
                }
            } else {
                // Buat record baru
                $dataToSave['created_at'] = now();
                $detail = LogbookRotasiPSCPDetail::create($dataToSave);
            }

            Log::info('Autosave successful', [
                'logbook_id' => $logbook->id,
                'detail_id' => $detail->id,
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'id' => $detail->id,
                'message' => 'Data berhasil disimpan'
            ]);
        } catch (\Exception $e) {
            Log::error('Autosave failed', [
                'error' => $e->getMessage(),
                'request' => $request->all(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data'
            ], 500);
        }
    }

    public function loadDraft()
    {
        try {
            $date = Carbon::today()->toDateString();
            $logbook = LogbookRotasiPSCP::where('date', $date)
                ->where('status', 'draft')
                ->with(['details' => function ($query) {
                    $query->orderBy('id', 'asc');
                }])
                ->first();

            Log::info('Executed SQL Query for loadDraft:', DB::getQueryLog());

            if (!$logbook || !$logbook->details || $logbook->details->isEmpty()) {
                return response()->json([]);
            }

            // Transform data untuk frontend
            $details = $logbook->details->map(function ($detail) {
                return [
                    'id' => $detail->id,
                    'start' => $detail->start,
                    'end' => $detail->end,
                    'pemeriksaan_dokumen' => $detail->pemeriksaan_dokumen,
                    'pengatur_flow' => $detail->pengatur_flow,
                    'operator_xray' => $detail->operator_xray,
                    'hhmd_petugas' => $detail->hhmd_petugas,
                    'hhmd_random' => $detail->hhmd_random,
                    'hhmd_unpredictable' => $detail->hhmd_unpredictable,
                    'manual_kabin_petugas' => $detail->manual_kabin_petugas,
                    'cek_random_barang' => $detail->cek_random_barang,
                    'barang_unpredictable' => $detail->barang_unpredictable,
                    'keterangan' => $detail->keterangan,
                ];
            });

            Log::info('Load draft successful', [
                'logbook_id' => $logbook->id,
                'details_count' => $details->count(),
                'user_id' => Auth::id()
            ]);

            return response()->json($details);
        } catch (\Exception $e) {
            Log::error('Load draft failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memuat data'
            ], 500);
        }
    }

    // Generate ID unik
    private function createLogbookId()
    {
        $last = LogbookRotasiPSCP::orderBy('id', 'desc')->first();
        $number = $last ? intval(substr($last->id, 4)) + 1 : 1;
        return 'LRP-' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    public function submit(Request $request) // Ubah parameter menjadi Request
    {
        $date = Carbon::today()->toDateString();
        $userId = Auth::id();

        // 1. Cari logbook yang statusnya masih 'draft' untuk hari ini
        $logbook = LogbookRotasiPSCP::with('details')
            ->where('date', $date)
            ->where('status', 'draft')
            ->first();

        // 2. Jika tidak ada logbook draft, berarti tidak ada yang bisa disubmit
        if (!$logbook) {
            return back()->with('error', 'Tidak ada logbook aktif yang bisa disubmit.');
        }

        // 3. Cek apakah ada detail logbook yang sudah diisi
        if ($logbook->details->isEmpty()) {
            return back()->with('error', 'Logbook masih kosong dan tidak bisa disubmit.');
        }

        // 4. Lakukan pengecekan apakah user yang login tercatat di salah satu detail
        $isAllowed = $logbook->details->contains(function ($detail) use ($userId) {
            return in_array($userId, [
                $detail->pemeriksaan_dokumen,
                $detail->pengatur_flow,
                $detail->operator_xray,
                $detail->hhmd_petugas,
                $detail->manual_kabin_petugas
            ]);
        });

        if (!$isAllowed) {
            return back()->with('error', 'Anda tidak terdaftar dalam logbook ini dan tidak memiliki hak untuk submit.');
        }

        // 5. Update status menjadi submitted
        $logbook->status = 'submitted';
        $logbook->approved_at = now();
        $logbook->approved_by = $userId;
        $logbook->save();

        return redirect()->route('logbookRotasiPSCP.index') // Asumsi nama route untuk view adalah 'logbook.rotasi-pscp.index'
            ->with('success', 'Logbook Rotasi PSCP berhasil disubmit.');
    }
}
