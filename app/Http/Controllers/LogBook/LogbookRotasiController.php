<?php

namespace App\Http\Controllers\LogBook;

use App\Http\Controllers\Controller;
use App\Models\LogbookRotasi;
use App\Models\LogbookRotasiDetail;
use App\Models\OfficerRotasiAssignments;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LogbookRotasiController extends Controller
{
    /**
     * Tampilkan semua logbook
     */
    public function index(Request $request)
    {
        $currentUser = Auth::id();

        // Opsi untuk dropdown PSCP
        $pscpOptions = [
            'pemeriksaan_dokumen' => 'Pemeriksaan Dokumen',
            'pengatur_flow' => 'Pengatur Flow',
            'operator_xray' => 'Operator X-Ray',
            'hhmd_petugas' => 'Pemeriksaan Orang Manual / HHMD',
            'manual_kabin_petugas' => 'Pemeriksa Manual Kabin',
        ];

        // Opsi untuk dropdown HBSCP
        $hbscpOptions = [
            'pengatur_flow' => 'Pengatur Flow',
            'operator_xray' => 'Operator X-Ray',
            'manual_bagasi_petugas' => 'Pemeriksa Manual Bagasi',
            'reunited' => 'Reunited',
        ];

        $typeForm = $request->query('type', 'PSCP');
        $logbook = LogbookRotasi::where('type', $typeForm)->latest()->get();

        return view('logbook.rotasi.logbookRotasi', compact(
            'logbook',
            'typeForm',
            'pscpOptions',
            'hbscpOptions'
        ));
    }

    public function store(Request $request)
    {

        // 2. Validasi Input
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'type' => 'required|in:PSCP,HBSCP',
            'tempat_jaga' => 'required|string',
            
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',

            // Validasi kondisional untuk input number PSCP (Sudah Benar)
            'hhmd_random' => 'required_if:tempat_jaga,hhmd_petugas|nullable|integer|min:0',
            'hhmd_unpredictable' => 'required_if:tempat_jaga,hhmd_petugas|nullable|integer|min:0',
            'cek_random_barang' => 'required_if:tempat_jaga,manual_kabin_petugas|nullable|integer|min:0',
            'barang_unpredictable' => 'required_if:tempat_jaga,manual_kabin_petugas|nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // 3. Gunakan Transaksi Database
        try {
            DB::beginTransaction();

            $userId = Auth::id();
            $logbookType = strtolower($request->input('type'));
            $logbookDate = $request->input('date');

            // === LANGKAH A: Membuat record di tabel `logbook_rotasi` ===
            $logbook = LogbookRotasi::firstOrCreate(
                [
                    // Kunci pencarian: Cari logbook berdasarkan tanggal DAN tipe
                    'date' => $logbookDate,
                    'type' => $logbookType,
                ],
                [
                    // Data ini hanya akan digunakan JIKA logbook baru dibuat
                    'id' => $this->generateLogbookId($logbookType),
                    'status' => 'draft',
                    'created_by' => $userId,
                ]
            );

            // === LANGKAH B: Membuat record di tabel `officer_rotasi_assignments` ===
            $assignment = OfficerRotasiAssignments::create([
                'officer_id' => $userId,
                'date' => $logbookDate,
                'start_time' => $request->input('start_time'), // Akan null jika tidak ada
                'end_time' => $request->input('end_time'),     // Akan null jika tidak ada
                'location_type' => $logbookType,
            ]);

            // === LANGKAH C: Membuat record di tabel `logbook_rotasi_details` ===
            $detailData = [
                'logbook_id' => $logbook->id,
                'officer_assignment_id' => $assignment->id,
            ];

            // Trik Dinamis: Gunakan 'tempat_jaga' sebagai nama kolom, dan isi dengan ID user
            $detailData[$request->input('tempat_jaga')] = $userId;

            // Tambahkan data counter jika ada
            if ($request->filled('hhmd_random')) {
                $detailData['hhmd_random'] = $request->input('hhmd_random');
                $detailData['hhmd_unpredictable'] = $request->input('hhmd_unpredictable');
            }

            if ($request->filled('cek_random_barang')) {
                $detailData['cek_random_barang'] = $request->input(key: 'cek_random_barang');
                $detailData['barang_unpredictable'] = $request->input('barang_unpredictable');
            }

            LogbookRotasiDetail::create($detailData);

            // Jika semua berhasil, commit transaksi
            DB::commit();
        } catch (\Exception $e) {
            // Jika terjadi error, batalkan semua query
            DB::rollBack();
            // Redirect kembali dengan pesan error
            return redirect()->back()->with('error', 'Gagal menyimpan data logbook: ' . $e->getMessage())->withInput();
        }

        // 4. Redirect dengan pesan sukses
        return redirect()->route('logbookRotasi.index')->with('success', 'Entry logbook berhasil ditambahkan.');
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
}
