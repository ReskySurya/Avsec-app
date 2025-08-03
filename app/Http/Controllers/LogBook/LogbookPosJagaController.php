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

        // Contoh: Bisa ambil data berdasarkan lokasi jika perlu
        $data = []; // Ambil data dari model sesuai lokasi, kalau butuh

        return view('logbook.posjaga.logbookPosJaga', [
            'location' => $location,
            'data' => $data,
        ]);
    }

    public function detail($id)
    {
        // Logika untuk menampilkan detail logbook berdasarkan ID
        // Misalnya, ambil data dari model LogbookPosJaga

        $logbook = []; // Ambil data dari model sesuai ID, kalau butuh

        return view('logbook.posjaga.detailPosJaga', [
            'logbook' => $logbook,
        ]);
    }
}
