<?php

namespace App\Http\Controllers\LogBook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LogbookPosJagaController extends Controller
{
     protected $allowedLocations = [
        'Kedatangan',
        'Barat',
        'Timur',
        'HBSCP',
        'PSCP',
        'CCTV',
        'Patroli',
        'Walking Patrol'
        
        // tambahkan jika ada lagi
    ];

    public function index($location)
    {
        if (!in_array($location, $this->allowedLocations)) {
            abort(404);
        }


        return view('logbook.posjaga.logbookPosJaga', [
            'location' => $location
        ]);
    }

    public function detail($location, $id)
    {
        // Logika untuk menampilkan detail logbook berdasarkan ID
        // Misalnya, ambil data dari model LogbookPosJaga
        if (!in_array($location, $this->allowedLocations)) {
            abort(404);
        }

        $logbook = [
            // Contoh data, ganti dengan query ke model LogbookPosJaga
            'id' => $id,
            'tanggal' => '2023-10-01',
            'area' => $location,
            'group' => 'A',
            'dinas_shift' => 'Pagi',
            // 'keterangan' => 'Contoh keterangan logbook',
        ]; // Ambil data dari model sesuai ID, kalau butuh

        return view('logbook.posjaga.detailPosJaga', [
            'logbook' => $logbook,
            'location' => $location 
        ]);
    }
}
