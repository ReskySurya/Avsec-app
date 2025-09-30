<?php

namespace App\Http\Controllers\LogBook;

use App\Http\Controllers\Controller;
use App\Models\Logbook;
use App\Models\LogbookRotasi;
use App\Models\LogbookRotasiDetail;
use App\Models\LogbookStaff;
use App\Models\Location;
use App\Models\OfficerRotasiAssignments;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LogbookRotasiController extends Controller
{

    public function index(Request $request)
    {
        // 1. Ambil tipe dari query URL, default-nya 'pscp'
        $typeForm = $request->query('type', 'pscp');

        // 2. Siapkan opsi untuk dropdown (tidak berubah)
        $pscpOptions = [
            'pemeriksaan_dokumen' => 'Pemeriksaan Dokumen',
            'pengatur_flow' => 'Pengatur Flow',
            'operator_xray' => 'Operator X-Ray',
            'hhmd_petugas' => 'Pemeriksaan Orang Manual / HHMD',
            'manual_kabin_petugas' => 'Pemeriksa Manual Kabin',
        ];
        $hbscpOptions = [
            'pengatur_flow' => 'Pengatur Flow',
            'operator_xray' => 'Operator X-Ray',
            'manual_bagasi_petugas' => 'Pemeriksa Manual Bagasi',
            'reunited' => 'Reunited',
        ];

        // 3. Peta relasi (tidak berubah)
        $roleRelationMap = [
            'pemeriksaan_dokumen'   => 'pemeriksaanDokumen',
            'pengatur_flow'         => 'pengaturFlow',
            'operator_xray'         => 'operatorXray',
            'hhmd_petugas'          => 'hhmdPetugas',
            'manual_kabin_petugas'  => 'manualKabinPetugas',
            'manual_bagasi_petugas' => 'manualBagasiPetugas',
            'reunited'              => 'reunitedPetugas',
        ];

        // 4. Ambil SATU logbook DRAFT TERAKHIR berdasarkan tipe
        $logbook = LogbookRotasi::where('type', $typeForm)
            ->where('status', 'draft')
            ->latest('id')
            ->first();

        $posjaga = $request->logbookID;
        // 5. Inisialisasi variabel
        $details = [];
        $officerLog = [];
        $personil = collect(); // Inisialisasi sebagai collection kosong

        // =================================================================
        // BAGIAN YANG DIPERBAIKI: Mengambil Personil dari Pos Jaga
        // =================================================================

        // Langkah A: Tentukan Tanggal Target. Default-nya hari ini.
        $targetDate = Carbon::today()->toDateString();

        // Langkah B: Jika ada draft logbook yang ditemukan, gunakan tanggal dari logbook itu.
        if ($logbook) {
            $targetDate = $logbook->date;
        }

        // Langkah C: Cari personil menggunakan Tanggal Target yang sudah benar.
        $location = Location::where('name', 'LIKE', $typeForm)->first();
        if ($location) {
            $logbookPosJaga = Logbook::where('date', $targetDate) // <-- Menggunakan $targetDate
                ->where('location_area_id', $location->id)
                ->first();

            if ($logbookPosJaga) {
                // Kita ambil personil yang 'hadir' pada tanggal tersebut
                $personil = LogbookStaff::with('user')
                    ->where('logbookID', $logbookPosJaga->logbookID)
                    ->where('description', 'hadir')
                    ->get();
            }
        }

        // 6. Jika logbook dengan status draft ditemukan, ambil detailnya
        if ($logbook) {
            $details = LogbookRotasiDetail::with(array_values($roleRelationMap))
                ->where('logbook_id', $logbook->id)
                ->latest('id')
                ->get();
        }

        // 7. Proses data dan kelompokkan berdasarkan officer (Tidak ada perubahan di sini)
        foreach ($details as $detail) {
            $startTime = $detail->officerAssignment?->start_time ? Carbon::parse($detail->officerAssignment->start_time)->format('H:i') : '-';
            $endTime = $detail->officerAssignment?->end_time ? Carbon::parse($detail->officerAssignment->end_time)->format('H:i') : '-';
            $keterangan = $detail->keterangan ?? '';

            foreach ($roleRelationMap as $roleKey => $relationName) {
                if ($officer = $detail->{$relationName}) {
                    $officerId = $officer->id;

                    if (!isset($officerLog[$officerId])) {
                        $officerLog[$officerId] = [
                            'officer_name' => $officer->display_name,
                            'roles' => [],
                            'keterangan' => []
                        ];
                    }

                    $slotData = [
                        'start' => $startTime,
                        'end'   => $endTime,
                    ];

                    if ($roleKey === 'hhmd_petugas') {
                        $slotData['hhmd_random'] = $detail->hhmd_random;
                        $slotData['hhmd_unpredictable'] = $detail->hhmd_unpredictable;
                    }
                    if ($roleKey === 'manual_kabin_petugas') {
                        $slotData['cek_random_barang'] = $detail->cek_random_barang;
                        $slotData['barang_unpredictable'] = $detail->barang_unpredictable;
                    }

                    $officerLog[$officerId]['roles'][$roleKey][] = $slotData;
                    if (!empty($keterangan)) {
                        $officerLog[$officerId]['keterangan'][] = $keterangan;
                    }
                }
            }
        }

        $supervisors = User::whereHas('role', function ($query) {
            $query->where('name', Role::SUPERVISOR);
        })->get();

        // 8. Kirim semua data yang dibutuhkan ke view
        return view('logbook.rotasi.logbookRotasi', compact(
            'officerLog',
            'typeForm',
            'posjaga',
            'logbook',
            'pscpOptions',
            'hbscpOptions',
            'supervisors',
            'personil' // <-- Data personil yang benar sekarang dikirim ke view
        ));
    }

    public function store(Request $request)
    {
        // 1. Validasi Input
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'type' => 'required|in:PSCP,HBSCP',
            'tempat_jaga' => 'required|string',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'personil_id' => 'required|exists:users,id',
            'hhmd_random' => 'nullable|integer|min:0',
            'hhmd_unpredictable' => 'nullable|integer|min:0',
            'cek_random_barang' => 'nullable|integer|min:0',
            'barang_unpredictable' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $userId = $request->input('personil_id');
            $logbookType = strtolower($request->input('type'));
            $logbookDate = $request->input('date');
            $tempatJaga = $request->input('tempat_jaga');

            // 1. Cari logbook terakhir untuk hari dan tipe ini
            $latestLogbook = LogbookRotasi::where('date', $logbookDate)
                ->where('type', $logbookType)
                ->orderBy('id', 'desc') // Urutkan dari ID terbesar/terbaru
                ->first();

            $logbook = null;

            // 2. Cek kondisinya
            if ($latestLogbook && $latestLogbook->status === 'draft') {
                // Jika logbook terakhir ada DAN statusnya 'draft', kita pakai logbook ini.
                $logbook = $latestLogbook;
            } else {
                // Jika tidak ada logbook ATAU logbook terakhir statusnya bukan 'draft',
                // maka kita buat logbook yang benar-benar baru.
                $logbook = LogbookRotasi::create([
                    'id'         => $this->generateLogbookId($logbookType), // Fungsi ini akan membuat nomor urut baru
                    'date'       => $logbookDate,
                    'type'       => $logbookType,
                    'status'     => 'draft',
                    'created_by' => Auth::id(),
                ]);
            }

            // LANGKAH B: Membuat Jadwal Petugas
            $assignment = OfficerRotasiAssignments::create([
                'officer_id' => $userId,
                'date' => $logbookDate,
                'start_time' => $request->input('start_time'),
                'end_time' => $request->input('end_time'),
                'location_type' => $logbookType,
            ]);

            // LANGKAH C: Menyiapkan dan Menyimpan Detail Logbook
            $detailData = [
                'logbook_id' => $logbook->id,
                'officer_assignment_id' => $assignment->id,
            ];

            // Tetapkan ID officer ke kolom peran yang dipilih
            $detailData[$tempatJaga] = $userId;

            // Jika tipenya PSCP, periksa dan tambahkan data counter
            if ($logbookType === 'pscp') {
                if ($tempatJaga === 'hhmd_petugas') {
                    $detailData['hhmd_random'] = $request->input('hhmd_random', 0);
                    $detailData['hhmd_unpredictable'] = $request->input('hhmd_unpredictable', 0);
                }

                if ($tempatJaga === 'manual_kabin_petugas') {
                    $detailData['cek_random_barang'] = $request->input('cek_random_barang', 0);
                    $detailData['barang_unpredictable'] = $request->input('barang_unpredictable', 0);
                }
            }

            // Simpan data detail yang sudah lengkap
            LogbookRotasiDetail::create($detailData);

            DB::commit();

            return redirect()->back()
                ->with('success', 'Entri logbook berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Helper function untuk membuat ID unik (Contoh: LRP-240825-001)
     */
    private function generateLogbookId($type)
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

    /**
     * Menampilkan halaman detail untuk sebuah logbook rotasi.
     * Fungsi ini menangani PSCP dan HBSCP, lalu mengarahkan ke view yang sesuai.
     *
     * @param  \App\Models\LogbookRotasi $logbook
     * @return \Illuminate\View\View
     */
    public function show(LogbookRotasi $logbook)
    {
        // Menggunakan Route Model Binding untuk mengambil data secara otomatis
        // Eager load semua relasi yang mungkin dibutuhkan
        $logbook->load(
            'details.officerAssignment',
            'details.pengaturFlow',
            'details.operatorXray',
            'details.manualBagasiPetugas',
            'details.reunitedPetugas',
            'details.pemeriksaanDokumen',
            'details.hhmdPetugas',
            'details.manualKabinPetugas',
            'creator',
            'approver'
        );

        // Siapkan array untuk menampung data yang sudah diproses per officer
        $officerLog = [];

        // Definisikan peran untuk HBSCP
        $roleRelationMap = [
            'pemeriksaan_dokumen' => 'pemeriksaanDokumen',
            'pengatur_flow' => 'pengaturFlow',
            'operator_xray' => 'operatorXray',
            'hhmd_petugas' => 'hhmdPetugas',
            'manual_kabin_petugas' => 'manualKabinPetugas',
            'manual_bagasi_petugas' => 'manualBagasiPetugas',
            'reunited' => 'reunitedPetugas',
        ];

        // Looping setiap detail entri dan kelompokkan berdasarkan officer
        foreach ($logbook->details as $detail) {
            $startTime = $detail->officerAssignment?->start_time ? Carbon::parse($detail->officerAssignment->start_time)->format('H:i') : '-';
            $endTime = $detail->officerAssignment?->end_time ? Carbon::parse($detail->officerAssignment->end_time)->format('H:i') : '-';
            $keterangan = $detail->keterangan ?? '';

            foreach ($roleRelationMap as $roleKey => $relationName) {
                if ($officer = $detail->{$relationName}) {
                    $officerId = $officer->id;

                    if (!isset($officerLog[$officerId])) {
                        $officerLog[$officerId] = [
                            'officer_name' => $officer->display_name,
                            'roles' => [],
                            'keterangan' => []
                        ];
                    }

                    $slotData = [
                        'start' => $startTime,
                        'end' => $endTime,
                    ];

                    // Jika perannya adalah hhmd_petugas, ambil data counternya dari $detail
                    if ($roleKey === 'hhmd_petugas') {
                        $slotData['hhmd_random'] = $detail->hhmd_random;
                        $slotData['hhmd_unpredictable'] = $detail->hhmd_unpredictable;
                    }

                    // Jika perannya adalah manual_kabin_petugas, ambil data counternya dari $detail
                    if ($roleKey === 'manual_kabin_petugas') {
                        $slotData['cek_random_barang'] = $detail->cek_random_barang;
                        $slotData['barang_unpredictable'] = $detail->barang_unpredictable;
                    }

                    // Masukkan data slot yang sudah lengkap (dengan atau tanpa counter)
                    $officerLog[$officerId]['roles'][$roleKey][] = $slotData;

                    if (!empty($keterangan)) {
                        $officerLog[$officerId]['keterangan'][] = $keterangan;
                    }
                }
            }
        }

        if ($logbook->type === 'hbscp') {
            return view('supervisor.detailLogbookRotasiHBSCP', compact('logbook', 'officerLog'));
        } else {
            return view('supervisor.detailLogbookRotasiPSCP', compact('logbook', 'officerLog'));
        }
    }

    public function submitForm(Request $request, $id, $posjaga)
    {
        // 1. Validasi input dari modal
        $validator = Validator::make($request->all(), [
            'signature'    => 'required|string',
            'approvedID'   => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', 'Semua field pada modal konfirmasi wajib diisi.');
        }

        $logbook = LogbookRotasi::findOrFail($id);

        if ($logbook->status !== 'draft') {
            return redirect()->route('logbookRotasi.index')->with('error', 'Gagal, logbook ini sudah tidak dalam status draft.');
        }

        // Langkah 1: Dapatkan daftar ID personil yang WAJIB mengisi (status 'hadir')
        $requiredStaffIds = collect();
        $location = Location::where('name', 'LIKE', $logbook->type)->first();

        if ($location) {
            $logbookPosJaga = Logbook::where('date', $logbook->date)
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
            // Langkah 2: Dapatkan daftar ID personil yang SUDAH mengisi di logbook rotasi ini
            $roleColumns = [
                'pemeriksaan_dokumen',
                'pengatur_flow',
                'operator_xray',
                'hhmd_petugas',
                'manual_kabin_petugas',
                'manual_bagasi_petugas',
                'reunited'
            ];
            $filledInStaffIds = collect();

            foreach ($logbook->details as $detail) {
                foreach ($roleColumns as $column) {
                    if (!empty($detail->{$column})) {
                        $filledInStaffIds->push($detail->{$column});
                    }
                }
            }
            $uniqueFilledInStaffIds = $filledInStaffIds->unique();

            // Langkah 3: Bandingkan kedua daftar tersebut
            $missingStaff = $requiredStaffIds->diff($uniqueFilledInStaffIds);

            if ($missingStaff->isNotEmpty()) {
                return redirect()->back()->with('error', 'Gagal, masih ada petugas hadir yang belum dimasukkan ke dalam jadwal rotasi.');
            }
        }

        // 2. Simpan data baru dari modal
        $logbook->status = 'submitted';
        $logbook->submittedSignature = $request->input('signature');
        $logbook->submitted_by = Auth::id();
        $logbook->approved_by = $request->input('approvedID');

        $logbook->save();

        return redirect()->route('logbook.posjaga.list', $posjaga)->with('success', 'Logbook berhasil diselesaikan dan dikirim.');
    }

    public function approvedForm(Request $request, $id)
    {
        // 1. Validasi input dari modal
        $validator = Validator::make($request->all(), [
            'signature' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Tanda tangan wajib diisi.'
            ], 422);
        }

        $logbook = LogbookRotasi::findOrFail($id);

        // Periksa status - seharusnya submitted, bukan draft
        if ($logbook->status !== 'submitted') {
            return response()->json([
                'success' => false,
                'message' => 'Logbook ini tidak dapat disetujui karena statusnya bukan submitted.'
            ], 400);
        }

        // 2. Update logbook
        $logbook->status = 'approved';
        $logbook->approvedSignature = $request->input('signature');
        $logbook->save();

        return redirect()->back()->with('success', 'Logbook berhasil disetujui.');
    }
}
