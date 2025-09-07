<?php

namespace App\Http\Controllers\Logbook;

use App\Http\Controllers\Controller;
use App\Models\LogbookChief;
use App\Models\LogbookChiefKemajuan;
use App\Models\LogbookFacility;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\LogbookDetail;
use App\Models\LogbookStaff;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class LogbookChiefController extends Controller
{
    public function index()
    {
        $currentUserId = Auth::id();

        $chiefLogbooks = LogbookChief::with('createdBy')
            ->where('created_by', $currentUserId)
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('logbook.chief.logbookLaporanLeader', [
            'chiefLogbooks' => $chiefLogbooks,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'grup' => 'required|string|max:10',
            'shift' => 'required|string|max:20',
        ]);

        try {
            LogbookChief::create([
                'logbookID' => LogbookChief::generateLogbookID(),
                'date' => $request->date,
                'grup' => $request->grup,
                'shift' => $request->shift,
                'created_by' => Auth::id(), // Aman, diambil dari user yang login
            ]);

            return redirect()->back()->with('success', 'Logbook created successfully.');
        } catch (\Exception $e) {
            Log::error('Error creating logbook: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal membuat logbook. Silakan coba lagi.');
        }
    }

    public function destory(LogbookChief $logbook)
    {
        try {
            $logbook->delete();
            return redirect()->back()->with('success', 'Logbook deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting logbook: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus logbook. Silakan coba lagi.');
        }
    }

    public function detail($id)
    {
        $logbook = LogbookChief::with('createdBy')
            ->where('logbookID', $id)
            ->first();

        if (!$logbook) {
            abort(404, 'Logbook not found.');
        }

        $kemajuanPersonil = LogbookChiefKemajuan::where('logbook_chief_id', $id)->get();
        $personil = LogbookStaff::with('user')
            ->where('logbook_chief_id', $id)
            ->get();
        $facility = LogbookFacility::where('logbook_chief_id', $id)->get();
        $uraianKegiatan = LogbookDetail::where('logbook_chief_id', $id)->get();

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


        return view('logbook.chief.detailLaporanLeader', [
            'logbook' => $logbook,
            'kemajuanPersonil' => $kemajuanPersonil,
            'personil' => $personil,
            'facility' => $facility,
            'uraianKegiatan' => $uraianKegiatan,
            'availableOfficers' => $availableOfficers,
            'allOfficers' => $allOfficers,
        ]);
    }

    public function storeKemajuan(Request $request)
    {
        // Ambil logbook_id dari hidden input
        $logbookId = $request->input('logbook_chief_id');

        // Validasi bahwa logbook_id ada
        if (!$logbookId) {
            return redirect()->back()->with('error', 'Logbook ID tidak ditemukan.')->withInput();
        }

        // Validasi input
        $validated = $request->validate([
            'jml_personil' => 'required|integer|min:0',
            'jml_hadir'    => 'required|integer|min:0|max:' . $request->jml_personil,
            'jml_kekuatan' => 'required|integer|min:0',
            'materi'       => 'required|string|max:1000',
            'keterangan'   => 'required|string|max:1000',
        ]);

        try {
            // Pastikan logbook ada
            $logbook = LogbookChief::findOrFail($logbookId);

            // Pastikan user memiliki akses
            if ($logbook->created_by !== Auth::id()) {
                abort(403, 'Unauthorized access.');
            }

            // Simpan data kemajuan
            LogbookChiefKemajuan::create([
                'logbook_chief_id' => $logbookId,
                'jml_personil'     => $validated['jml_personil'],
                'jml_hadir'        => $validated['jml_hadir'],
                'jml_kekuatan'     => $validated['jml_kekuatan'],
                'materi'           => $validated['materi'],
                'keterangan'       => $validated['keterangan'],
            ]);

            return redirect()->route('logbook.chief.detail', ['id' => $logbook->logbookID])
                ->with('success', 'Kemajuan personil berhasil ditambahkan.');
        } catch (\Exception $e) {
            Log::error('Error adding kemajuan personil: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'logbook_id' => $logbookId,
                'input' => $request->all(),
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.');
        }
    }

    public function storePersonil(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'logbook_chief_id' => 'required|exists:logbook_chief,logbookID',
            'staffID' => 'required|exists:users,id',
            'description' => 'required|in:hadir,izin,sakit,cuti',
        ]);

        try {
            // Pastikan logbook ada dan user memiliki akses
            $logbook = LogbookChief::findOrFail($request->logbook_chief_id);

            if ($logbook->created_by !== Auth::id()) {
                abort(403, 'Unauthorized access.');
            }

            // Cek apakah personil sudah ada di logbook ini
            $existingPersonil = LogbookStaff::where('logbook_chief_id', $request->logbook_chief_id)
                ->where('staffID', $request->staffID)
                ->first();

            if ($existingPersonil) {
                return redirect()->back()
                    ->with('error', 'Personil sudah terdaftar dalam logbook ini.')
                    ->withInput();
            }

            // Ambil data user
            $user = User::findOrFail($request->staffID);

            // Simpan data personil
            LogbookStaff::create([
                'logbook_chief_id' => $validated['logbook_chief_id'],
                'staffID' => $validated['staffID'],
                'classification' => $user->lisensi ?? null, // Gunakan null coalescing
                'description' => $validated['description'],
            ]);

            return redirect()->back()->with('success', 'Personil berhasil ditambahkan!');
        } catch (\Exception $e) {
            Log::error('Error adding personil: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'logbook_id' => $request->logbook_chief_id,
                'staff_id' => $request->staffID,
                'input' => $request->all(),
            ]);

            return redirect()->back()
                ->with('error', 'Gagal menambahkan personil. Silakan coba lagi.')
                ->withInput();
        }
    }

    public function storeFacility(Request $request)
    {
        $request->validate([
            'logbook_chief_id'   => 'required|exists:logbook_chief,logbookID',
            'facility'  => 'required|string|max:255',
            'quantity'  => 'required|integer|min:1',
            'description'   => 'required|string|max:1000',
        ]);

        try {
            LogbookFacility::create([
                'logbook_chief_id' => $request->logbook_chief_id,
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

    public function storeUraian(Request $request)
    {
        $request->validate([
            'logbook_chief_id'   => 'required|exists:logbook_chief,logbookID',
            'start_time'  => 'required|date_format:H:i',
            'end_time'  => 'required|date_format:H:i',
            'summary'     => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        try {
            LogbookDetail::create([
                'logbook_chief_id' => $request->logbook_chief_id,
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

    public function signatureSend(Request $request, $logbookID)
    {
        // Validasi input
        $request->validate([
            'signature' => 'required|string',
            'approved_by' => 'required|exists:users,id',
        ], [
            'signature.required' => 'Tanda tangan wajib diberikan',
            'approved_by.required' => 'Supervisor wajib dipilih',
            'approved_by.exists' => 'Supervisor tidak valid',
        ]);

        try {
            // Cari logbook berdasarkan primary key yang benar
            $logbook = LogbookChief::find($logbookID);

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
                'approved_by' => $request->approved_by,
            ]);

            $logbook->save();

            return redirect()->route('logbook.chief.index')
                ->with('success', 'Logbook berhasil diserahkan dan menunggu konfirmasi dari Officer penerima.');
        } catch (\Exception $e) {
            Log::error('Signature Send Error: ' . $e->getMessage(), [
                'logbookID' => $logbookID,
                'signature_length' => strlen($request->signature ?? ''),
                'approved_by' => $request->approved_by,
                'stack_trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan tanda tangan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function reviewLogbook($logbookID)
    {
        try {
            $logbook = LogbookChief::with(['createdBy', 'approvedBy'])
                ->where('logbookID', $logbookID)
                ->firstOrFail();

            $kemajuanPersonil = LogbookChiefKemajuan::where('logbook_chief_id', $logbookID)->get();

            $logbookDetails = LogbookDetail::where('logbook_chief_id', $logbookID)->get();

            $personil = LogbookStaff::with('user')
                ->where('logbook_chief_id', $logbookID)
                ->get();
            $facility = LogbookFacility::where('logbook_chief_id', $logbookID)->get();

            return view('logbook.chief.logbookReviewLaporanLeader', [
                'logbook' => $logbook,
                'kemajuanPersonil' => $kemajuanPersonil,
                'logbookDetails' => $logbookDetails,
                'personil' => $personil,
                'facility' => $facility
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Logbook tidak ditemukan');
        }
    }
}
