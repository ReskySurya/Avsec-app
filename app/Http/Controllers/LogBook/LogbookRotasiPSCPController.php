<?php

namespace App\Http\Controllers\LogBook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LogbookRotasiPSCPController extends Controller
{
    public function index()
    {
        return view('logbook.logbookRotasiPscp');
    }
}
