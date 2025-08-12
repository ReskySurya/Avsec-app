<?php

namespace App\Http\Controllers\LogBook;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\LogbookFacility;
use Illuminate\Http\Request;
use App\Models\Logbook;
use App\Models\Location;
use App\Models\LogbookDetail;
use App\Models\LogbookStaff;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

\Carbon\Carbon::setLocale('id');

use Illuminate\Support\Facades\Log;



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
        $currentUserId = Auth::id();

        $logbooks = Logbook::with('locationArea')
            ->where('senderID', $currentUserId)
            // ->whereNull('senderSignature') 
            ->where('status', 'draft') // Hanya tampilkan logbook dengan status draft
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $locations = Location::whereIn('name', $this->allowedLocations)
            ->orderBy('name', 'asc')
            ->get();

        return view('logbook.posjaga.logbookPosJaga', [
            'logbooks' => $logbooks,
            'locations' => $locations,
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

    // Uraian Kegiatan
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
        $personil = LogbookStaff::with('user') // Tambahkan eager loading
            ->where('logbookID', $id)
            ->get();
        $facility = LogbookFacility::with('equipments') // Eager load facility relation
            ->where('logbookID', $id)->get();

        // Tambahkan data equipments untuk dropdown form
        $equipments = Equipment::orderBy('name', 'asc')->get();

        return view('logbook.posjaga.detailPosJaga', [
            'logbook' => $logbook,
            'uraianKegiatan' => $uraianKegiatan,
            'personil' => $personil,
            'facility' => $facility,
            'equipments' => $equipments,
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
            return redirect()->back()->with('success', 'Uraian kegiatan berhasil diperbarui');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', 'Terjadi kesalahan validasi data');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Handle record not found
            return redirect()->back()->with('error', 'Data yang ingin diperbarui tidak ditemukan');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui data. Silakan coba lagi.');
        }
    }

    // personil
    public function storeStaff(Request $request)
    {
        $request->validate([
            'logbookID'   => 'required|exists:logbooks,logbookID',
            'staffID'     => 'required|exists:users,id',
            'classification' => 'nullable|string|max:255',
            'description'  => 'required|in:hadir,izin,sakit,cuti',
        ]);

        try {
            $user = User::find($request->staffID);
            LogbookStaff::create([
                'logbookID' => $request->logbookID,
                'staffID' => $request->staffID,
                'classification' => $user->lisensi,
                'description' => $request->description,
            ]);

            return redirect()->back()->with('success', 'Personil berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menambahkan personil. Silakan coba lagi.')
                ->withInput();
        }
    }

    public function updateStaff(Request $request, $id)
    {
        try {
            // Validasi input
            $validatedData = $request->validate([
                'logbookID'   => 'required|exists:logbooks,logbookID',
                'staffID'     => 'required|exists:users,id',
                'classification' => 'nullable|string|max:255',
                'description'  => 'required|in:hadir,izin,sakit,cuti',
            ]);

            // Cari record berdasarkan ID
            $logbookStaff = LogbookStaff::findOrFail($id);

            // Ambil lisensi dari user yang dipilih (konsisten dengan store)
            $user = User::find($validatedData['staffID']);

            // Update data
            $updateData = [
                'logbookID' => $validatedData['logbookID'],
                'staffID' => $validatedData['staffID'],
                'classification' => $user ? $user->lisensi : $validatedData['classification'], // Konsisten dengan store
                'description' => $validatedData['description'],
            ];

            $logbookStaff->update($updateData);

            return redirect()->back()->with('success', 'Personil berhasil diperbarui');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', 'Terjadi kesalahan validasi data');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Data yang ingin diperbarui tidak ditemukan');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    public function deleteStaff($id)
    {
        try {
            // Cari detail berdasarkan ID
            $logbookDetail = LogbookStaff::findOrFail($id);
            // Hapus data
            $logbookDetail->delete();
            // Redirect kembali ke halaman detail dengan pesan sukses
            return redirect()->back()->with([
                'success' => 'Personil berhasil dihapus.',
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            return redirect()
                ->back()
                ->with('error', 'Data tidak ditemukan!');
        }
    }

    // fasilitas
    public function storeFacility(Request $request)
    {
        $request->validate([
            'logbookID'   => 'required|exists:logbooks,logbookID',
            'facilityID'  => 'required|exists:equipment,id',
            'quantity'  => 'required|integer|min:1',
            'description'   => 'required|string|max:1000',
        ]);

        try {
            // $facility = \App\Models\Equipment::find($request->facilityID);
            LogbookFacility::create([
                'logbookID' => $request->logbookID,
                'facilityID'  => $request->facilityID,
                'quantity'  => $request->quantity,
                'description'   => $request->description,
            ]);

            return redirect()->back()->with('success', 'Fasilitas berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menambahkan personil. Silakan coba lagi.')
                ->withInput();
        }
    }
    public function updateFacility(Request $request, $id)
    {
        try {
            // Validasi input - sesuai dengan struktur database Anda
            $validatedData = $request->validate([
                'logbookID'   => 'required|exists:logbooks,logbookID',
                'facilityID'  => 'required|exists:equipment,id', // Ubah dari facility ke facilityID
                'quantity'    => 'required|integer|min:1',
                'description' => 'required|string|max:1000',
            ]);

            // Cari record berdasarkan ID
            $logbookFacility = LogbookFacility::findOrFail($id);

            // Update data sesuai dengan struktur database
            $logbookFacility->update([
                'logbookID'   => $validatedData['logbookID'],
                'facilityID'  => $validatedData['facilityID'],
                'quantity'    => $validatedData['quantity'],
                'description' => $validatedData['description'],
            ]);

            return redirect()->back()->with('success', 'Fasilitas berhasil diperbarui');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', 'Terjadi kesalahan validasi data');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Data yang ingin diperbarui tidak ditemukan');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function deleteFacility($id)
    {
        try {
            // Cari detail berdasarkan ID
            $logbookDetail = LogbookFacility::findOrFail($id);
            // Hapus data
            $logbookDetail->delete();
            // Redirect kembali ke halaman detail dengan pesan sukses
            return redirect()->back()->with([
                'success' => 'Fasilitas berhasil dihapus.',
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            return redirect()
                ->back()
                ->with('error', 'Data tidak ditemukan!');
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

            // Ambil data base64 dari request
            $signature = $request->signature;

            // Hapus prefix data URL jika ada, untuk menyimpan base64 murni
            if (preg_match('/^data:image\/(\w+);base64,/', $signature, $type)) {
                $signature = substr($signature, strpos($signature, ',') + 1);
            }

            // Update dengan menggunakan fill dan save untuk menghindari masalah timestamps
            $logbook->fill([
                'senderSignature' => $signature,
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

    public function showReceivedLogbook($location, $logbookID)
    {
        try {
            $logbook = Logbook::with(['locationArea', 'senderBy', 'receiverBy', 'approverBy'])
                ->where('logbookID', $logbookID)
                ->firstOrFail();

            $logbookDetails = LogbookDetail::where('logbookID', $logbookID)->get();
            $personil = LogbookStaff::with('user')
                ->where('logbookID', $logbookID)
                ->get();
            $facility = LogbookFacility::with('equipments')
                ->where('logbookID', $logbookID)->get();

            return view('officer.receivedLogbook', [
                'logbook' => $logbook,
                'personil' => $personil,
                'facility' => $facility,
                'logbookDetails' => $logbookDetails
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Logbook tidak ditemukan');
        }
    }

    public function supervisorReviewLogbook($logbookID)
    {
        try {
            $logbook = Logbook::with(['locationArea', 'senderBy', 'receiverBy', 'approverBy'])
                ->where('logbookID', $logbookID)
                ->firstOrFail();

            $logbookDetails = LogbookDetail::where('logbookID', $logbookID)->get();

            $personil = LogbookStaff::with('user')
                ->where('logbookID', $logbookID)
                ->get();
            $facility = LogbookFacility::with('equipments')
                ->where('logbookID', $logbookID)->get();

            return view('supervisor.logbookReview', [
                'logbook' => $logbook,
                'logbookDetails' => $logbookDetails,
                'personil' => $personil,
                'facility' => $facility
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Logbook tidak ditemukan');
        }
    }

    public function signatureReceive(Request $request, $location, $logbookID)
    {
        Log::info('Signature Receive Request:', [
            'logbookID' => $logbookID,
            'location' => $location,
            'has_signature' => $request->has('signature'),
            'signature_length' => strlen($request->signature ?? '')
        ]);

        // Validasi input
        $request->validate([
            'signature' => 'required|string',
        ], [
            'signature.required' => 'Tanda tangan wajib diberikan',
        ]);

        try {
            // Cari logbook berdasarkan primary key yang benar
            $logbook = Logbook::find($logbookID);

            if (!$logbook) {
                return redirect()->back()->with('error', 'Logbook tidak ditemukan');
            }

            // Ambil data base64 dari request
            $signature = $request->signature;

            // Hapus prefix data URL jika ada, untuk menyimpan base64 murni
            if (preg_match('/^data:image\/(\w+);base64,/', $signature, $type)) {
                $signature = substr($signature, strpos($signature, ',') + 1);
            }

            // Update dengan menggunakan fill dan save untuk menghindari masalah timestamps
            Log::info('Before saving signature:', [
                'logbookID' => $logbookID,
                'signature_length' => strlen($signature),
                'current_status' => $logbook->status
            ]);

            $logbook->fill([
                'receivedSignature' => $signature,
            ]);

            $logbook->save();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Logbook berhasil diterima.',
                ]);
            }

            return redirect()->route('logbook.index', ['location' => $location])
                ->with('success', 'Logbook berhasil diterima.');
        } catch (\Exception $e) {
            Log::error('Signature Receive Error: ' . $e->getMessage(), [
                'logbookID' => $logbookID,
                'signature_length' => strlen($request->signature ?? ''),
                'stack_trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan tanda tangan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function signatureApprove(Request $request, $logbookID)
    {
        Log::info('Signature Receive Request:', [
            'logbookID' => $logbookID,
            'has_signature' => $request->has('signature'),
            'signature_length' => strlen($request->signature ?? '')
        ]);

        // Validasi input
        $request->validate([
            'signature' => 'required|string',
        ], [
            'signature.required' => 'Tanda tangan wajib diberikan',
        ]);

        try {
            // Cari logbook berdasarkan primary key yang benar
            $logbook = Logbook::find($logbookID);

            if (!$logbook) {
                return redirect()->back()->with('error', 'Logbook tidak ditemukan');
            }

            // Ambil data base64 dari request
            $signature = $request->signature;

            // Hapus prefix data URL jika ada, untuk menyimpan base64 murni
            if (preg_match('/^data:image\/(\w+);base64,/', $signature, $type)) {
                $signature = substr($signature, strpos($signature, ',') + 1);
            }

            // Update dengan menggunakan fill dan save untuk menghindari masalah timestamps
            Log::info('Before saving signature:', [
                'logbookID' => $logbookID,
                'signature_length' => strlen($signature),
                'current_status' => $logbook->status
            ]);

            $logbook->fill([
                'approvedSignature' => $signature,
                'status' => 'approved' // sesuai dengan enum yang ada di migration
            ]);

            $logbook->save();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Logbook berhasil diterima.',
                ]);
            }

            return redirect()->route('supervisor.logbook-form', ['logbookID' => $logbookID])
                ->with('success', 'Logbook berhasil diterima.');
        } catch (\Exception $e) {
            Log::error('Signature Receive Error: ' . $e->getMessage(), [
                'logbookID' => $logbookID,
                'signature_length' => strlen($request->signature ?? ''),
                'stack_trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan tanda tangan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function rejectLogbook(Request $request, $logbookID)
    {
        try {
            // Validasi input untuk alasan reject (opsional)
            $request->validate([
                'rejected_reason' => 'nullable|string|max:1000'
            ]);

            $logbook = Logbook::where('logbookID', $logbookID)->firstOrFail();

            $logbook->update([
                'status' => 'draft',
                'rejected_reason' => $request->rejected_reason,
            ]);

            return redirect()->route('supervisor.showDataLogbook')
                ->with('success', 'Logbook berhasil ditolak dan dikembalikan ke status draft. Alasan penolakan: ' . ($request->rejected_reason ?? 'Tidak ada alasan'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Logbook tidak ditemukan');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat melakukan reject logbook: ' . $e->getMessage());
        }
    }
}
