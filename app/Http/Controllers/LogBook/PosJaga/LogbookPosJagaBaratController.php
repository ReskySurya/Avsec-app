<?php

namespace App\Http\Controllers\LogBook\PosJaga;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LogbookPosJagaBaratController extends Controller
{
    public function index()
    {
        return view('logbook.posjaga.logbookPosBarat');
    }
}
