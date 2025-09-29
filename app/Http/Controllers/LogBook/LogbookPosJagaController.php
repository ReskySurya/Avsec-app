<?php

namespace App\Http\Controllers\LogBook;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use App\Models\Equipment;
use App\Models\LogbookFacility;
use App\Models\Logbook;
use App\Models\Location;
use App\Models\LogbookRotasi;
use App\Models\ManualBook;
use App\Models\LogbookDetail;
use App\Models\LogbookStaff;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;

Carbon::setLocale('id');

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
        'Walking Patrol',
        'Pos Keberangkatan'
    ];

    public function index()
    {
        // 1. Ambil ID user yang sedang login
        $currentUserId = Auth::id();

        // 2. Cari semua logbookID di mana user ini terdaftar sebagai personil "hadir"
        $staffLogbookIds = LogbookStaff::where('staffID', $currentUserId)
            ->where('description', 'hadir')
            ->pluck('logbookID');

        // 3. Ambil semua logbook draft yang relevan dengan user
        $logbooks = Logbook::with('locationArea')
            ->where('status', 'draft') // Hanya tampilkan logbook dengan status draft
            ->where(function ($query) use ($currentUserId, $staffLogbookIds) {
                // Kondisi 1: Logbook dibuat oleh user ini
                $query->where('senderID', $currentUserId)
                    // ATAU
                    // Kondisi 2: User ini terdaftar sebagai personil "hadir" di logbook tersebut
                    ->orWhereIn('logbookID', $staffLogbookIds);
            })
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // 4. Ambil data lokasi untuk modal (tidak berubah)
        $locations = Location::whereIn('name', $this->allowedLocations)
            ->orderBy('name', 'asc')
            ->get();

        // 5. Kirim data ke view
        return view('logbook.posjaga.logbookPosJaga', [
            'logbooks' => $logbooks,
            'locations' => $locations,
        ]);
    }

    public function store(Request $request)
    {
        // 1. Validasi Input Utama
        $request->validate([
            'date' => 'required|date',
            'location_area_id' => 'required|exists:locations,id',
            'grup' => 'required|string|max:255',
            'shift' => 'required|in:pagi,malam',
        ]);

        // 2. Cek Duplikat Logbook Pos Jaga
        $existingLogbook = Logbook::where('date', $request->date)
            ->where('location_area_id', $request->location_area_id)
            ->where('shift', $request->shift)
            ->first();

        if ($existingLogbook) {
            $locationName = Location::find($existingLogbook->location_area_id)->name ?? 'area yang dipilih';
            $formattedDate = Carbon::parse($existingLogbook->date)->isoFormat('D MMMM YYYY');
            $errorMessage = "Logbook untuk {$locationName} pada tanggal {$formattedDate} shift {$existingLogbook->shift} sudah ada.";
            return redirect()->back()->with('duplicate_error', $errorMessage)->withInput();
        }

        // Mulai Database Transaction
        DB::beginTransaction();
        try {
            // 3. Buat Logbook Pos Jaga Utama
            $logbookPosJaga = Logbook::create([
                'date' => $request->date,
                'location_area_id' => $request->location_area_id,
                'grup' => $request->grup,
                'shift' => $request->shift,
                'senderID' => Auth::id(),
            ]);

            // 4. Cek Lokasi dan Buat Draft Form Turunan
            $location = Location::find($request->location_area_id);
            $successMessage = 'Logbook Pos Jaga berhasil dibuat.';

            if ($location && in_array($location->name, ['PSCP', 'HBSCP'])) {
                // Konversi nama lokasi menjadi 'type' yang konsisten (lowercase)
                $logbookType = strtolower($location->name);

                // Buat draft Logbook Rotasi
                $this->_createLogbookRotasiDraft($request, $logbookType);

                // Buat draft Manual Book
                $this->_createManualBookDraft($request, $logbookType);

                $successMessage .= ' Draft untuk Logbook Rotasi juga telah dibuat.';
            }

            // Jika semua berhasil, commit transaction
            DB::commit();

            return redirect()->route('logbook.index')->with('success', $successMessage);
        } catch (\Exception $e) {
            // Jika ada error, rollback semua proses
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Fungsi helper untuk membuat draft Logbook Rotasi.
     * Hanya akan membuat jika belum ada draft untuk tanggal dan tipe yang sama.
     */
    private function _createLogbookRotasiDraft(Request $request, string $logbookType)
    {
        // Cek apakah sudah ada logbook rotasi (draft atau submitted) untuk tanggal & tipe ini
        $exists = LogbookRotasi::where('date', $request->date)
            ->where('type', $logbookType)
            ->exists();

        if (!$exists) {
            LogbookRotasi::create([
                'id'         => $this->generateLogbookRotasiId($logbookType), // Panggil fungsi generator ID Anda
                'date'       => $request->date,
                'type'       => $logbookType,
                'status'     => 'draft',
                'created_by' => Auth::id(),
            ]);
        }
    }

    /**
     * Fungsi helper untuk membuat draft Manual Book.
     * Hanya akan membuat jika belum ada draft untuk tanggal, shift, dan tipe yang sama.
     */
    private function _createManualBookDraft(Request $request, string $logbookType)
    {
        // Cek apakah sudah ada draft manual book untuk kombinasi ini
        $exists = ManualBook::where('date', $request->date)
            ->where('shift', $request->shift)
            ->where('type', $logbookType)
            ->exists();

        if (!$exists) {
            ManualBook::create([
                'id'         => $this->generateManualBookId($logbookType, $request->date), // Panggil fungsi generator ID Anda
                'created_by' => Auth::id(),
                'date'       => $request->date,
                'shift'      => $request->shift,
                'type'       => $logbookType,
                'status'     => 'draft',
            ]);
        }
    }

    private function generateLogbookRotasiId($type)
    {
        $prefix = ($type == 'pscp') ? 'LRP' : 'LRH';
        $date = now()->format('ymd'); // Format: TahunBulanTanggal

        $lastLogbook = LogbookRotasi::where('id', 'LIKE', $prefix . '-' . $date . '-%')
            ->orderBy('id', 'desc')
            ->first();

        $newNumber = 1;
        if ($lastLogbook) {
            $parts = explode('-', $lastLogbook->id);
            $lastNumber = (int)end($parts);
            $newNumber = $lastNumber + 1;
        }

        return $prefix . '-' . $date . '-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    private function generateManualBookId(string $type, string $date): string
    {
        // Logika dari controller ManualBook Anda
        $prefix = ($type === 'hbscp') ? 'MBH' : 'MBP';
        $datePart = Carbon::parse($date)->format('ym');

        $lastRecord = ManualBook::where('id', 'like', $prefix . '-' . $datePart . '-%')
            ->orderBy('id', 'desc')
            ->first();

        $nextSequence = $lastRecord ? ((int) substr($lastRecord->id, -4)) + 1 : 1;
        return $prefix . '-' . $datePart . '-' . str_pad($nextSequence, 4, '0', STR_PAD_LEFT);
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
        $facility = LogbookFacility::where('logbookID', $id)->get();

        // Dapatkan daftar staff yang sudah terdaftar
        $existingStaffIds = $personil->pluck('staffID')->toArray();

        // Dapatkan officer yang tersedia (belum ditambahkan)
        $availableOfficers = User::whereHas('role', function ($query) {
            $query->where('name', Role::OFFICER);
        })
            ->whereNotIn('id', $existingStaffIds)
            ->orderBy('name', 'asc')
            ->get();

        // Dapatkan semua officer untuk modal edit
        $allOfficers = User::whereHas('role', function ($query) {
            $query->where('name', Role::OFFICER);
        })
            ->orderBy('name', 'asc')
            ->get();

        // Tambahkan data equipments untuk dropdown form
        $equipments = Equipment::orderBy('name', 'asc')->get();

        return view('logbook.posjaga.detailPosJaga', [
            'logbook' => $logbook,
            'uraianKegiatan' => $uraianKegiatan,
            'personil' => $personil,
            'facility' => $facility,
            'equipments' => $equipments,
            'availableOfficers' => $availableOfficers, // Officer yang tersedia untuk ditambahkan
            'allOfficers' => $allOfficers, // Semua officer untuk modal edit
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
            'facility'  => 'required|string|max:255',
            'quantity'  => 'required|integer|min:1',
            'description'   => 'required|string|max:1000',
        ]);

        try {
            LogbookFacility::create([
                'logbookID' => $request->logbookID,
                'facility'  => $request->facility,
                'quantity'  => $request->quantity,
                'description'   => $request->description,
            ]);

            return redirect()->back()->with('success', 'Fasilitas berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menambahkan Fasilitas. Silakan coba lagi.')
                ->withInput();
        }
    }
    public function updateFacility(Request $request, $id)
    {
        try {
            // Validasi input - sesuai dengan struktur database Anda
            $validatedData = $request->validate([
                'logbookID'   => 'required|exists:logbooks,logbookID',
                'facility'  => 'required|string|max:255',
                'quantity'    => 'required|integer|min:1',
                'description' => 'required|string|max:1000',
            ]);

            // Cari record berdasarkan ID
            $logbookFacility = LogbookFacility::findOrFail($id);

            // Update data sesuai dengan struktur database
            $logbookFacility->update([
                'logbookID'   => $validatedData['logbookID'],
                'facility'  => $validatedData['facility'],
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

            return redirect()->route('officer.logbook.detail', ['logbookID' => $logbookID])
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
            $facility = LogbookFacility::where('logbookID', $logbookID)->get();

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
            $facility = LogbookFacility::where('logbookID', $logbookID)->get();

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

    public function officerReviewLogbook($logbookID)
    {
        try {
            $logbook = Logbook::with(['locationArea', 'senderBy', 'receiverBy', 'approverBy'])
                ->where('logbookID', $logbookID)
                ->firstOrFail();

            $logbookDetails = LogbookDetail::where('logbookID', $logbookID)->get();

            $personil = LogbookStaff::with('user')
                ->where('logbookID', $logbookID)
                ->get();
            $facility = LogbookFacility::where('logbookID', $logbookID)->get();

            return view('logbook.posjaga.logbookReviewPosJaga', [
                'logbook' => $logbook,
                'logbookDetails' => $logbookDetails,
                'personil' => $personil,
                'facility' => $facility
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Logbook tidak ditemukan');
        }
    }

    public function showListPosJaga($id)
    {
        $logbook = Logbook::with('locationArea')->findOrFail($id);

        // Cek apakah ada form turunan yang dibuat sebagai draft
        $logbookRotasi = null;
        $manualBook = null;

        if (in_array($logbook->locationArea->name, ['PSCP', 'HBSCP'])) {
            $logbookType = strtolower($logbook->locationArea->name);

            // Query untuk LogbookRotasi
            $logbookRotasi = LogbookRotasi::where('date', $logbook->date)
                ->where('type', $logbookType)
                ->where('status', 'draft')
                ->first();

            // Query untuk ManualBook
            $manualBook = ManualBook::where('date', $logbook->date)
                ->where('shift', $logbook->shift)
                ->where('type', $logbookType)
                ->where('status', 'draft')
                ->first();
        }

        return view('logbook.posjaga.listPosJaga', [
            'logbook' => $logbook,
            'logbookRotasi' => $logbookRotasi,
            'manualBook' => $manualBook,
        ]);
    }
}
