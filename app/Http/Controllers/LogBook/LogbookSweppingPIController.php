<?php

namespace App\Http\Controllers\LogBook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LogbookSweppingPIController extends Controller
{
    public function index()
    {
        return view('logbook.logbookSweppingPI');
    }
}
