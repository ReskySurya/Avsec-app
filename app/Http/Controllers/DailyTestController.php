<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DailyTestController extends Controller
{
    /**
     * Menampilkan halaman HHMD daily test
     *
     * @return \Illuminate\View\View
     */

    public function xrayBagasiLayout()
    {
        return view('daily-test.xrayBagasiLayout');
    }

    public function xrayCabinLayout()
    {
        return view('daily-test.xrayCabinLayout');
    }
}
