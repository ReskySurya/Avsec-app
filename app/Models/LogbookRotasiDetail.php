<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogbookRotasiDetail extends Model
{
    use HasFactory;

    protected $table = 'logbook_rotasi_details';

    protected $fillable = [
        'logbook_id',
        'officer_assignment_id',
        'pengatur_flow',
        'operator_xray',
        'manual_bagasi_petugas',
        'reunited',
        'pemeriksaan_dokumen',
        'hhmd_petugas',
        'manual_kabin_petugas',
        'hhmd_random',
        'hhmd_unpredictable',
        'cek_random_barang',
        'barang_unpredictable',
        'keterangan',
    ];

    // Relasi ke Logbook utama
    public function logbook()
    {
        return $this->belongsTo(LogbookRotasi::class, 'logbook_id');
    }

    // Relasi ke Assignment
    public function officerAssignment()
    {
        return $this->belongsTo(OfficerRotasiAssignments::class, 'officer_assignment_id');
    }

    // Relasi ke masing-masing User (officer shift)
    public function pengaturFlow()
    {
        return $this->belongsTo(User::class, 'pengatur_flow');
    }

    public function operatorXray()
    {
        return $this->belongsTo(User::class, 'operator_xray');
    }

    public function manualBagasiPetugas()
    {
        return $this->belongsTo(User::class, 'manual_bagasi_petugas');
    }

    public function reunitedPetugas()
    {
        return $this->belongsTo(User::class, 'reunited');
    }

    public function pemeriksaanDokumen()
    {
        return $this->belongsTo(User::class, 'pemeriksaan_dokumen');
    }

    public function hhmdPetugas()
    {
        return $this->belongsTo(User::class, 'hhmd_petugas');
    }

    public function manualKabinPetugas()
    {
        return $this->belongsTo(User::class, 'manual_kabin_petugas');
    }
}
