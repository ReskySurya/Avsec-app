<?php

namespace App\Http\Controllers\Checklist;

use App\Http\Controllers\Controller;
use App\Models\ManualBook;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ManualBookController extends Controller
{
    /**
     * Menampilkan halaman utama dengan daftar semua laporan manual book.
     */
    public function index()
    {
        $manualBooks = ManualBook::with('creator','details')->latest()->paginate(10);
        $loggedInUserName = Auth::user()->name;

        return view('checklist.manualbook.manualBook', compact('manualBooks', 'loggedInUserName'));
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

        return redirect()->route('checklist.manualbook.index')
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
        if ($header->created_by !== Auth::id()) {
            abort(403, 'AKSES DITOLAK');
        }

        // Pastikan laporan masih berstatus draft
        if ($header->status !== 'draft') {
            return redirect()->route('checklist.manualbook.index')->withErrors(['general' => 'Laporan ini sudah selesai dan tidak bisa ditambahkan data lagi.']);
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

        return redirect()->route('checklist.manualbook.index')
            ->with('success', 'Data baru berhasil ditambahkan ke laporan ' . $header->id);
    }

    /**
     * Mengubah status laporan dari 'draft' menjadi 'submitted'.
     */
    public function finish($id)
    {
        $manualBook = ManualBook::findOrFail($id);

        // Pastikan hanya pembuatnya yang bisa menyelesaikan
        if ($manualBook->created_by !== Auth::id()) {
            abort(403, 'AKSES DITOLAK');
        }

        $manualBook->status = 'submitted';
        $manualBook->save();

        return redirect()->route('checklist.manualbook.index')
            ->with('success', 'Laporan ' . $manualBook->id . ' telah berhasil diselesaikan.');
    }
}
