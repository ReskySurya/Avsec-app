<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExportController extends Controller
{
    public function index(Request $request)
    {
        return view('superadmin.export.exportPdf', );
    }
}
