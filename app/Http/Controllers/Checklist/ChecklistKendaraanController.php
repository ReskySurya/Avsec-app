<?php

namespace App\Http\Controllers\Checklist;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ChecklistKendaraanController extends Controller
{
    public function indexChecklistKendaraan()
    {
        
        return view('checklist.checklistKendaraan', []);
    }
    
}
