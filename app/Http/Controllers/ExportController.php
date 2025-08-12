<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExportController extends Controller
{
    public function index(Request $request)
    {
        return view('superadmin.export.exportPdf', );
    }

    public function exportPdfDailyTest(Request $request)
    {

        return view('superadmin.export.exportPdfDailyTest' );
    }
    public function exportPdfLogbook(Request $request)
    {

        return view('superadmin.export.exportPdfLogbook' );
    }
}
