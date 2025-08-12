<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;
use App\Models\Logbook;

class ExportController extends Controller
{
    protected $allowedLocationLogbook = [
        'Pos Kedatangan',
        'Pos Barat',
        'Pos Timur',
        'HBSCP',
        'PSCP',
        'CCTV',
        'Patroli',
        'Walking Patrol'
    ];

    public function index(Request $request)
    {
        return view('superadmin.export.exportPdf');
    }

    public function exportPdfDailyTest(Request $request)
    {
        return view('superadmin.export.exportPdfDailyTest');
    }

    public function exportPdfLogbook(Request $request)
    {
        // Ambil data lokasi dari model Location
        $locations = Location::whereIn('name', $this->allowedLocationLogbook)
            ->orderBy('name')
            ->get();

        // Ambil data logbook untuk tabel (contoh dengan filter sederhana)
        $logbooks = Logbook::with(['locationArea', 'senderBy', 'receiverBy', 'approverBy'])
            ->orderBy('date', 'desc')
            ->take(10) // Batasi untuk contoh
            ->get();

        return view('superadmin.export.exportPdfLogbook', [
            'locations' => $locations,
            'logbooks' => $logbooks
        ]);
    }

    public function filterLogbook(Request $request)
    {
        $query = Logbook::with(['locationArea'])
            ->orderBy('date', 'desc');

        if ($request->form_type) {
            // Tambahkan filter berdasarkan form_type jika diperlukan
        }

        if ($request->location) {
            $query->whereHas('locationArea', function ($q) use ($request) {
                $q->where('name', $request->location);
            });
        }

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('date', [
                $request->start_date,
                $request->end_date
            ]);
        }

        $logbooks = $query->get();

        return response()->json([
            'logbooks' => $logbooks
        ]);
    }
}
