<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogbookRotasiPSCPDetail extends Model
{
    protected $table = 'detail_logbook_rotasipscp';

    protected $fillable = [
        'logbook_id', 'start', 'end',
        'pemeriksaan_dokumen', 'pengatur_flow', 'operator_xray',
        'hhmd_petugas', 'hhmd_random', 'hhmd_unpredictable',
        'manual_kabin_petugas', 'cek_random_barang', 'barang_unpredictable',
        'keterangan'
    ];

    public function logbook()
    {
        return $this->belongsTo(LogbookRotasiPSCP::class, 'logbook_id');
    }
}
