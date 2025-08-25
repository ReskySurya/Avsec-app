<?php

namespace App\Http\Controllers\Checklist;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ManualBookController extends Controller
{
    public function index()
    {
        $currentUser = Auth::id();

        $options = [
            
        ];

        return view('checklist.manualbook.manualBook');
    }
}
