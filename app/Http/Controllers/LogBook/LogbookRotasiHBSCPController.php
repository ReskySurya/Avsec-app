<?php

namespace App\Http\Controllers\LogBook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LogbookRotasiHBSCP;
use App\Models\LogbookRotasiHBSCPDetail;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class LogbookRotasiHBSCPController extends Controller
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

        return view('logbook.logbookRotasiHbscp', compact('officers'));
    }

    public function autosave(Request $request)
    {
        try {
            // Validasi input
            $validator = Validator::make($request->all(), [
                'start' => 'nullable|date_format:H:i',
                'end' => 'nullable|date_format:H:i',
                'pengatur_flow' => 'nullable|exists:users,id',
                'operator_xray' => 'nullable|exists:users,id',
                'manual_bagasi_petugas' => 'nullable|exists:users,id',
                'reunited' => 'nullable|exists:users,id',
                'keterangan' => 'nullable|string|max:255',
                'row_id' => 'nullable|exists:logbook_rotasi_hbscp_details,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $date = Carbon::today()->toDateString();

            // Cari atau buat logbook header
            $logbook = LogbookRotasiHBSCP::firstOrCreate(
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
                'pengatur_flow' => $request->pengatur_flow,
                'operator_xray' => $request->operator_xray,
                'manual_bagasi_petugas' => $request->manual_bagasi_petugas,
                'reunited' => $request->reunited,
                'keterangan' => $request->keterangan,
                'updated_at' => now()
            ];

            // Jika row_id ada, update existing record
            if ($request->row_id) {
                $detail = LogbookRotasiHBSCPDetail::where('id', $request->row_id)
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
                $detail = LogbookRotasiHBSCPDetail::create($dataToSave);
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
            $logbook = LogbookRotasiHBSCP::where('date', $date)
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
                    'pengatur_flow' => $detail->pengatur_flow,
                    'operator_xray' => $detail->operator_xray,
                    'manual_bagasi_petugas' => $detail->manual_bagasi_petugas,
                    'reunited' => $detail->reunited,
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
        $last = LogbookRotasiHBSCP::orderBy('id', 'desc')->first();
        $number = $last ? intval(substr($last->id, 4)) + 1 : 1;
        return 'LRH-' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    public function submit(Request $request) // Ubah parameter menjadi Request
    {
        $date = Carbon::today()->toDateString();
        $userId = Auth::id();

        // 1. Cari logbook yang statusnya masih 'draft' untuk hari ini
        $logbook = LogbookRotasiHBSCP::with('details')
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
                $detail->pengatur_flow,
                $detail->operator_xray,
                $detail->manual_bagasi_petugas,
                $detail->reunited,
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

        return redirect()->route('logbookRotasiHBSCP.index')
            ->with('success', 'Logbook Rotasi HBSCP berhasil disubmit.');
    }

    public function showDetailLogbook($logbookId)
    {
        // Ambil logbook utama beserta semua detail + officer terkait
        $logbook = LogbookRotasiHBSCP::with([
            'details.pengaturFlowOfficer',
            'details.operatorXrayOfficer',
            'details.manualBagasiOfficer',
            'details.reunitedOfficer',
        ])->findOrFail($logbookId);

        return view('supervisor.detailLogbookRotasiHBSCP', compact('logbook'));
    }
}
