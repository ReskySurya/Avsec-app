<?php

namespace App\Http\Controllers\LogBook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Logbook;
use App\Models\Location;
use App\Models\LogbookDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
    public function update(Request $request, $location, $logbookID)
    {
        if (!in_array($location, $this->allowedLocations)) {
            abort(404);
        }

        $locationModel = Location::where('name', $location)->first();
        if (!$locationModel) {
            abort(404, 'Location tidak ditemukan di database.');
        }

        $logbook = Logbook::where('logbookID', $logbookID)
            ->where('location_area_id', $locationModel->id)
            ->first();

        if (!$logbook) {
            abort(404, 'Data logbook tidak ditemukan.');
        }

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
    public function detail($location, $id)
    {
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

        $uraianKegiatan = LogbookDetail::where('logbookID', $id)->get();
        if (!$logbooks) {
            abort(404, 'Logbook tidak ditemukan.');
        }

        return view('logbook.posjaga.detailPosJaga', [
            'location' => $location,
            'location_id' => $locationModel->id,
            'logbook' => $logbooks,
            'uraianKegiatan' => $uraianKegiatan,
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

    public function signatureSend(Request $request, $location, $logbookID)
    {
        // Validasi input
        $request->validate([
            'signature' => 'required|string',
            'receivedID' => 'required|exists:users,id',
            'approvedID' => 'required|exists:users,id',
        ], [
            'signature.required' => 'Tanda tangan wajib diberikan',
            'receivedID.required' => 'Officer penerima wajib dipilih',
            'receivedID.exists' => 'Officer penerima tidak valid',
            'approvedID.required' => 'Supervisor wajib dipilih',
            'approvedID.exists' => 'Supervisor tidak valid',
        ]);

        try {
            // Cari logbook berdasarkan primary key yang benar
            $logbook = Logbook::find($logbookID);

            if (!$logbook) {
                return redirect()->back()->with('error', 'Logbook tidak ditemukan');
            }

            // Update dengan menggunakan fill dan save untuk menghindari masalah timestamps
            $logbook->fill([
                'senderSignature' => $request->signature,
                'receivedID' => $request->receivedID,
                'approvedID' => $request->approvedID,
                'status' => 'submitted' // sesuai dengan enum yang ada di migration
            ]);

            $logbook->save();

            return redirect()->route('logbook.index', ['location' => $location])
                ->with('success', 'Logbook berhasil diserahkan dan menunggu konfirmasi dari Officer penerima.');
        } catch (\Exception $e) {
            Log::error('Signature Send Error: ' . $e->getMessage(), [
                'logbookID' => $logbookID,
                'signature_length' => strlen($request->signature ?? ''),
                'receivedID' => $request->receivedID,
                'approvedID' => $request->approvedID,
                'stack_trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan tanda tangan: ' . $e->getMessage())
                ->withInput();
        }
    }
}
