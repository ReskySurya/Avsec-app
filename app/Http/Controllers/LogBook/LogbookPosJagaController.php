<?php

namespace App\Http\Controllers\LogBook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Logbook;
use App\Models\Location;
use App\Models\LogbookDetail;
use Illuminate\Support\Facades\Auth;

\Carbon\Carbon::setLocale('id');



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

    public function index()
    {
        // Ambil ID user yang sedang login
        $currentUserId =  Auth::id();

        $logbooks = Logbook::with('locationArea') // Eager load the location area
            ->where('senderID', $currentUserId) // Filter berdasarkan user yang login
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc') // Secondary ordering jika diperlukan
            ->paginate(10); // Paginate the results

        // Ambil semua locations untuk dropdown
        $locations = Location::whereIn('name', $this->allowedLocations)
            ->orderBy('name', 'asc')
            ->get();

        return view('logbook.posjaga.logbookPosJaga', [
            'logbooks' => $logbooks,
            'locations' => $locations, // Pass locations to view
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
                'success' => 'Logbook berhasil di buat.',
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membuat logbook. ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $logbookID)
    {
        // Validasi apakah user yang login adalah pemilik logbook
        $logbook = Logbook::where('logbookID', $logbookID)
            ->where('senderID', Auth::id()) // Tambah validasi ownership
            ->first();

        if (!$logbook) {
            abort(404, 'Data logbook tidak ditemukan atau Anda tidak memiliki akses untuk mengedit logbook ini.');
        }

        $request->validate([
            'date' => 'required|date',
            'location_area_id' => 'required|exists:locations,id', // Tambah validasi location_area_id
            'grup' => 'required|string|max:255',
            'shift' => 'required|string|max:255',
        ]);

        try {
            $logbook->update([
                'date' => $request->date,
                'location_area_id' => $request->location_area_id, // Update location_area_id
                'grup' => $request->grup,
                'shift' => $request->shift,
            ]);
            return redirect()->back()->with([
                'success' => 'Logbook berhasil diupdate.',
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengupdate logbook. ' . $e->getMessage());
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
                'success' => 'Logbook entry created successfully.',
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete logbook entry. ' . $e->getMessage());
        }
    }


    public function storeDetail(Request $request)
    {
        $request->validate([
            'logbookID'   => 'required|exists:logbooks,logbookID',
            'start_time'  => 'required|date_format:H:i',
            'end_time'  => 'required|date_format:H:i',
            'summary'     => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        try {
            LogbookDetail::create([
                'logbookID' => $request->logbookID,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'summary' => $request->summary,
                'description' => $request->description,
            ]);

            return redirect()->back()->with('success', 'Uraian kegiatan berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menambahkan uraian kegiatan. Silakan coba lagi.')
                ->withInput(); // Kembalikan input yang sudah diisi
        }
    }
    public function detail($id)
    {
        $logbook = Logbook::with('locationArea') // Eager load relasi location
            ->where('logbookID', $id)
            ->first();

        if (!$logbook) {
            abort(404, 'Logbook tidak ditemukan.');
        }

        $uraianKegiatan = LogbookDetail::where('logbookID', $id)->get();

        return view('logbook.posjaga.detailPosJaga', [
            'logbook' => $logbook,
            'uraianKegiatan' => $uraianKegiatan,
            'location' => $logbook->locationArea->name, // Ambil nama location dari relasi
            'location_id' => $logbook->location_area_id,
        ]);
    }


    public function deleteDetail($id)
    {
        try {
            // Cari detail berdasarkan ID
            $logbookDetail = LogbookDetail::findOrFail($id);
            // Hapus data
            $logbookDetail->delete();
            // Redirect kembali ke halaman detail dengan pesan sukses
            return redirect()->back()->with([
                'success' => 'Detail Logbook berhasil dihapus.',
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            return redirect()
                ->back()
                ->with('error', 'Data tidak ditemukan!');
        }
    }

    public function updateDetail(Request $request, $id)
    {
        try {
            // Validasi input
            $validatedData = $request->validate([
                'start_time' => 'required|string',
                'end_time' => 'nullable|string',
                'summary' => 'required|string|max:255',
                'description' => 'required|string|max:1000',
            ], [
                'start_time.required' => 'Waktu mulai wajib diisi',
                'summary.required' => 'Uraian kegiatan wajib diisi',
                'description.required' => 'Keterangan wajib diisi',
                'summary.max' => 'Uraian kegiatan maksimal 255 karakter',
                'description.max' => 'Keterangan maksimal 1000 karakter',
            ]);

            // Cari record berdasarkan ID
            $logbookDetail = LogbookDetail::findOrFail($id); // Sesuaikan dengan model Anda
            // Update data
            $logbookDetail->update([
                'start_time' => $validatedData['start_time'],
                'end_time' => $validatedData['end_time'],
                'summary' => $validatedData['summary'],
                'description' => $validatedData['description'],
                'updated_at' => now(),
            ]);

            // Redirect kembali dengan pesan sukses
            return redirect()->back()->with('success', 'Uraian kegiatan berhasil diupdate');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', 'Terjadi kesalahan validasi data');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Handle record not found
            return redirect()->back()->with('error', 'Data yang ingin diupdate tidak ditemukan');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengupdate data. Silakan coba lagi.');
        }
    }
}
