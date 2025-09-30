<?php

namespace App\Http\Controllers\Checklist;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\Logbook;
use App\Models\LogbookStaff;
use App\Models\ManualBook;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ManualBookController extends Controller
{
    public function index(request $request)
    {
        $currentUserId = Auth::id();
        $loggedInUserName = Auth::user()->name;

        // Langkah 1: Kumpulkan semua logbookID di mana user ini terdaftar "hadir" (Tidak berubah)
        $staffLogbookIds = LogbookStaff::where('staffID', $currentUserId)
            ->where('description', 'hadir')
            ->pluck('logbookID');

        // Inisialisasi query ManualBook
        $manualBooksQuery = ManualBook::query();

        $posjaga = $request->logbookID;

        if ($staffLogbookIds->isNotEmpty()) {
            // Langkah 2: Ambil properti dari logbook-logbook tersebut, TERMASUK relasi ke lokasi
            $relevantLogbooks = Logbook::with('locationArea:id,name') // Eager load nama lokasi
                ->whereIn('logbookID', $staffLogbookIds)
                ->get(['date', 'shift', 'location_area_id']); // Ambil kolom yang diperlukan

            // Langkah 3: Filter ManualBook berdasarkan kombinasi yang cocok
            $manualBooksQuery->with('creator', 'details')
                ->where('status', 'draft')
                ->where(function ($query) use ($relevantLogbooks) {
                    if ($relevantLogbooks->isEmpty()) {
                        $query->whereRaw('1 = 0');
                        return;
                    }

                    // Buat grup kondisi OR untuk setiap kombinasi yang ditemukan
                    foreach ($relevantLogbooks as $logbook) {
                        // Pastikan relasi locationArea ada untuk menghindari error
                        if ($logbook->locationArea) {
                            $query->orWhere(function ($subQuery) use ($logbook) {
                                $subQuery->where('date', $logbook->date)
                                    ->where('shift', 'like', $logbook->shift) // Ubah menjadi 'like'
                                    // Cocokkan `type` di ManualBook dengan `nama lokasi` dari Logbook
                                    ->where('type', strtolower($logbook->locationArea->name));
                            });
                        }
                    }
                });
        } else {
            // Jika user tidak terdaftar di logbook manapun, jangan tampilkan manual book sama sekali
            $manualBooksQuery->whereRaw('1 = 0');
        }

        $manualBooks = $manualBooksQuery->orderBy('date', 'desc')->latest()->paginate(10);

        $supervisors = User::whereHas('role', fn($q) => $q->where('name', Role::SUPERVISOR))->get();

        return view('checklist.manualbook.manualBook', compact(
            'manualBooks',
            'loggedInUserName',
            'posjaga',
            'supervisors'
        ));
    }

    /**
     * Menyimpan laporan baru (header dan detail awal) dengan status 'draft'.
     */
    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'shift' => 'required|in:pagi,siang',
            'type' => 'required|in:hbscp,pscp',
            'rows' => 'required|array|min:1',
            'rows.*.time' => 'required|date_format:H:i',
            'rows.*.name' => 'required|string|max:255',
        ]);

        // Cek apakah sudah ada draft untuk hari, shift, dan tipe yang sama oleh user ini
        $existingDraft = ManualBook::where('date', $request->date)
            ->where('shift', $request->shift)
            ->where('type', $request->type)
            ->where('created_by', Auth::id())
            ->where('status', 'draft')
            ->first();

        if ($existingDraft) {
            return redirect()->back()->withErrors(['general' => 'Laporan draft untuk tanggal, shift, dan pos jaga ini sudah ada. Silakan lanjutkan input dari halaman utama.'])->withInput();
        }

        // Logika Pembuatan ID Unik (contoh: MBH-2508-0001)
        $prefix = $request->type === 'hbscp' ? 'MBH' : 'MBP';
        $datePart = Carbon::parse($request->date)->format('ym');

        $lastRecord = ManualBook::where('id', 'like', $prefix . '-' . $datePart . '-%')
            ->orderBy('id', 'desc')
            ->first();
        if ($lastRecord) {
            $lastSequence = (int) substr($lastRecord->id, -4);
            $nextSequence = $lastSequence + 1;
        } else {
            $nextSequence = 1;
        }
        $newId = $prefix . '-' . $datePart . '-' . str_pad($nextSequence, 4, '0', STR_PAD_LEFT);

        // Buat record header
        $header = ManualBook::create([
            'id' => $newId,
            'created_by' => Auth::id(),
            'date' => $request->date,
            'shift' => $request->shift,
            'type' => $request->type,
            'status' => 'draft',
        ]);

        // Simpan baris-baris awal yang diinput
        foreach ($request->rows as $row) {
            if (!empty($row['time']) && !empty($row['name'])) {
                $header->details()->create([
                    'time' => $row['time'],
                    'name' => $row['name'],
                    'pax' => $row['pax'],
                    'flight' => $row['flight'],
                    'orang' => $row['orang'],
                    'barang' => $row['barang'],
                    'temuan' => $row['temuan'],
                    'keterangan' => $row['keterangan'],
                ]);
            }
        }

        return redirect()->back()
            ->with('success', 'Draft laporan baru berhasil dibuat.');
    }

    /**
     * Menambahkan baris detail baru ke laporan 'draft' yang sudah ada.
     */
    public function addDetails(Request $request, $id)
    {
        $request->validate([
            'rows' => 'required|array|min:1',
            'rows.*.time' => 'required|date_format:H:i',
            'rows.*.name' => 'required|string|max:255',
        ]);

        $header = ManualBook::findOrFail($id);

        // Pastikan hanya pembuatnya yang bisa menambah detail
        // if ($header->created_by !== Auth::id()) {
        //     abort(403, 'AKSES DITOLAK');
        // }

        // Pastikan laporan masih berstatus draft
        if ($header->status !== 'draft') {
            return redirect()->back()->withErrors(['general' => 'Laporan ini sudah selesai dan tidak bisa ditambahkan data lagi.']);
        }

        // Simpan baris-baris baru
        foreach ($request->rows as $row) {
            if (!empty($row['time']) && !empty($row['name'])) {
                $header->details()->create([
                    'time' => $row['time'],
                    'name' => $row['name'],
                    'pax' => $row['pax'],
                    'flight' => $row['flight'],
                    'orang' => $row['orang'],
                    'barang' => $row['barang'],
                    'temuan' => $row['temuan'],
                    'keterangan' => $row['keterangan'],
                ]);
            }
        }

        return redirect()->back()
            ->with('success', 'Data baru berhasil ditambahkan ke laporan ' . $header->id);
    }

    public function finish(Request $request, $id)
    {
        // 1. Validasi input dari modal (tidak berubah)
        $request->validate([
            'signature'  => 'required|string',
            'approvedID' => 'required|exists:users,id',
        ]);

        // Eager load details untuk pengecekan nanti
        $manualBook = ManualBook::with('details')->findOrFail($id);

        // Langkah 1: Dapatkan daftar ID personil yang WAJIB mengisi (status 'hadir')
        $requiredStaffIds = collect();
        $location = Location::where('name', 'LIKE', $manualBook->type)->first();

        if ($location) {
            $logbookPosJaga = Logbook::where('date', $manualBook->date)
                ->where('shift', $manualBook->shift)
                ->where('location_area_id', $location->id)
                ->first();

            if ($logbookPosJaga) {
                $requiredStaffIds = LogbookStaff::where('logbookID', $logbookPosJaga->logbookID)
                    ->where('description', 'hadir')
                    ->pluck('staffID');
            }
        }

        // Lakukan pengecekan hanya jika ada personil yang wajib mengisi
        if ($requiredStaffIds->isNotEmpty()) {
            // ++++ KODE BARU ++++
            // Langkah 2: Dapatkan daftar NAMA personil yang SUDAH mengisi di manual book ini
            $filledInStaffNames = $manualBook->details->pluck('name')->unique();

            // Langkah 3: Ubah daftar NAMA tersebut menjadi daftar ID
            $filledInStaffIds = User::whereIn('name', $filledInStaffNames)->pluck('id');

            // Langkah 4: Bandingkan kedua daftar ID tersebut
            $missingStaff = $requiredStaffIds->diff($filledInStaffIds);

            if ($missingStaff->isNotEmpty()) {
                return redirect()->back()->with('error', 'Gagal, masih ada personil hadir yang belum mengisi laporan Manual Book.');
            }
        }

        // Jika lolos pengecekan, lanjutkan proses submit
        $manualBook->status = 'submitted';
        $manualBook->senderSignature = $request->signature;
        $manualBook->approved_by = $request->approvedID; // Tambahkan waktu persetujuan

        $manualBook->save();

        return redirect()->back()
            ->with('success', 'Laporan ' . $manualBook->id . ' telah berhasil diselesaikan.');
    }

    public function show(ManualBook $manualBook)
    {
        $manualBook->load('details');

        return view('supervisor.detailManualBook', compact('manualBook'));
    }

    public function approveSignature(Request $request, ManualBook $manualBook)
    {
        // Validasi: pastikan data tanda tangan ada
        $request->validate([
            'approvedSignature' => 'required|string',
        ]);

        // Otorisasi: Pastikan user yang login adalah supervisor yang ditunjuk
        // Anda bisa menambahkan logika yang lebih kompleks di sini jika perlu
        if (Auth::id() !== $manualBook->approved_by) {
            return redirect()->back()->withErrors(['general' => 'Anda tidak memiliki otorisasi untuk menyetujui laporan ini.']);
        }

        // Simpan tanda tangan dan ubah status jika perlu
        $manualBook->approvedSignature = $request->approvedSignature;
        $manualBook->status = 'approved';

        $manualBook->save();

        return redirect()->back()->with('success', 'Tanda tangan persetujuan berhasil disimpan.');
    }
}
