<?php

namespace App\Http\Controllers\LogBook;

use App\Http\Controllers\Controller;
use App\Models\Logbook;
use App\Models\Location;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class LogbookSweppingPIController extends Controller
{
    public function index()
    {
        // Ambil ID user yang sedang login
        $currentUserId = Auth::id();

        $logbooks = Logbook::with('locationArea')
            ->where('senderID', $currentUserId)
            ->where('status', 'draft')
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $locations = Location::where('name', 'Sweeping PI')->get();

        return view('logbook.logbookSweppingPI', [
            'logbooks' => $logbooks,
            'locations' => $locations,
        ]);
    }
}
