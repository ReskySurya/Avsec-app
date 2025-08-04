<?php

namespace App\Http\Controllers\LogBook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Logbook;
use App\Models\Location;
use Illuminate\Support\Facades\Auth;

class LogbookPosJagaController extends Controller
{
     protected $allowedLocations = [
        'Pos Kedatangan',
        'Pos Barat',
        'Pos Timur',
        'HBSCP',
        'PSCP',
        'CCTV',
        'Patroli',
        'Walking Patrol'
    ];

    public function index($location)
    {
        if (!in_array($location, $this->allowedLocations)) {
            abort(404);
        }

        $locationModel = Location::where('name', $location)->first();

        if (!$locationModel) {
            abort(404, 'Location tidak ditemukan di database. Pastikan data lokasi sudah tersedia.');
        }

        $logbooks = Logbook::where('location_area_id', $locationModel->id)
            ->with('locationArea') // Eager load the location area
            ->orderBy('date', 'desc')
            ->paginate(10); // Paginate the results

        return view('logbook.posjaga.logbookPosJaga', [
            'location' => $location,
            'location_id' => $locationModel->id,
            'logbooks' => $logbooks,
        ]);
    }

    public function detail($location, $id){
         if (!in_array($location, $this->allowedLocations)) {
            abort(404);
        }

        $locationModel = Location::where('name', $location)->first();

        if (!$locationModel) {
            abort(404, 'Location tidak ditemukan di database. Pastikan data lokasi sudah tersedia.');
        }
        $logbooks = Logbook::where('location_area_id', $locationModel->id)
            ->where('logbookID', $id) // Ganti 'logbook_id' dengan nama kolom primary key yang benar jika bukan 'id'
            // ->with('location_area_id') // Eager load the location area
            ->first();

        if (!$logbooks) {
            abort(404, 'Logbook tidak ditemukan.');
        }

        return view('logbook.posjaga.detailPosJaga', [
            'location' => $location,
            'location_id' => $locationModel->id,
            'logbook' => $logbooks,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'location_area_id' => 'required|exists:locations,id',
            'grup' => 'required|string|max:255',
            'shift' => 'required|string|max:255',
        ]);

        try {
            Logbook::create([
            'date' => $request->date,
            'location_area_id' => $request->location_area_id,
            'grup' => $request->grup,
            'shift' => $request->shift,
            'senderID' => Auth::id(),
            ]);

            return redirect()->back()->with([
            'success' => 'Logbook berhasil dibuat.',
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membuat logbook. ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Logbook $logbook)
    {
        $request->validate([
            'date' => 'required|date',
            'grup' => 'required|string|max:255',
            'shift' => 'required|string|max:255',
        ]);

        try {
            $logbook->update([
                'date' => $request->date,
                'grup' => $request->grup,
                'shift' => $request->shift,
            ]);

            return redirect()->back()->with('success', 'Logbook berhasil di perbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui data logbook. ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Logbook $logbook)
    {
        try {
            $logbook->delete();
            return redirect()->back()->with([
            'success' => 'Logbook berhasil dihapus.',
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus data logbook. ' . $e->getMessage());
        }
    }
}
