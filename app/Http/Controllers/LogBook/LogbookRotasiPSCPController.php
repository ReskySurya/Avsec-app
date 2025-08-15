<?php

namespace App\Http\Controllers\LogBook;

use App\Http\Controllers\Controller;
use App\Models\LogbookRotasiPSCP;
use App\Models\LogbookRotasiPSCPDetail;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

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
        $date = Carbon::today()->toDateString();

        // Cari atau buat logbook header
        $logbook = LogbookRotasiPSCP::firstOrCreate(
            ['date' => $date],
            ['id' => $this->createLogbookId(), 'status' => 'draft']
        );

        // Simpan/Update detail baris
        LogbookRotasiPSCPDetail::updateOrCreate(
            [
                'logbook_id' => $logbook->id,
                'id' => $request->row_id ?? null
            ],
            [
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
            ]
        );

        return response()->json(['success' => true]);
    }

    // Load draft saat halaman dibuka
    public function loadDraft()
    {
        $date = Carbon::today()->toDateString();
        $logbook = LogbookRotasiPSCP::where('date', $date)->first();

        if (!$logbook) {
            return response()->json([]);
        }

        $details = $logbook->details()->get();
        return response()->json($details);
    }

    // Generate ID unik
    private function createLogbookId()
    {
        $last = LogbookRotasiPSCP::orderBy('id', 'desc')->first();
        $number = $last ? intval(substr($last->id, 4)) + 1 : 1;
        return 'LRP-' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }
}
