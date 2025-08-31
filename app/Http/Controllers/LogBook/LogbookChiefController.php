<?php

namespace App\Http\Controllers\Logbook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogbookChiefController extends Controller
{
    public function index()
    {
        // Ambil ID user yang sedang login
        $currentUserId = Auth::id();

        

        return view('logbook.chief.logbookLaporanLeader');
    }
}
