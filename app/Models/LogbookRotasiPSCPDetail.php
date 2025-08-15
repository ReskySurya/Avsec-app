<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Casts\TimeOnlyCast;

class LogbookRotasiPSCPDetail extends Model
{
    use HasFactory;

    protected $table = 'logbook_rotasi_pscp_details';

    protected $fillable = [
        'logbook_id',
        'start',
        'end',
        'pemeriksaan_dokumen',
        'pengatur_flow',
        'operator_xray',
        'hhmd_petugas',
        'hhmd_random',
        'hhmd_unpredictable',
        'manual_kabin_petugas',
        'cek_random_barang',
        'barang_unpredictable',
        'keterangan'
    ];

    protected $casts = [
        'start' => TimeOnlyCast::class,
        'end' => TimeOnlyCast::class,
        'hhmd_random' => 'integer',
        'hhmd_unpredictable' => 'integer',
        'cek_random_barang' => 'integer',
        'barang_unpredictable' => 'integer',
    ];

    /**
     * Relasi ke logbook utama
     */
    public function logbook(): BelongsTo
    {
        return $this->belongsTo(LogbookRotasiPSCP::class, 'logbook_id');
    }

    /**
     * Relasi ke user untuk pemeriksaan dokumen
     */
    public function pemeriksaanDokumenOfficer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pemeriksaan_dokumen');
    }

    /**
     * Relasi ke user untuk pengatur flow
     */
    public function pengaturFlowOfficer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pengatur_flow');
    }

    /**
     * Relasi ke user untuk operator x-ray
     */
    public function operatorXrayOfficer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'operator_xray');
    }

    /**
     * Relasi ke user untuk HHMD petugas
     */
    public function hhmdOfficer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'hhmd_petugas');
    }

    /**
     * Relasi ke user untuk manual kabin petugas
     */
    public function manualKabinOfficer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manual_kabin_petugas');
    }

    /**
     * Cek apakah baris ini memiliki data
     */
    public function hasData(): bool
    {
        return !empty($this->start) ||
               !empty($this->end) ||
               !empty($this->pemeriksaan_dokumen) ||
               !empty($this->pengatur_flow) ||
               !empty($this->operator_xray) ||
               !empty($this->hhmd_petugas) ||
               !empty($this->hhmd_random) ||
               !empty($this->hhmd_unpredictable) ||
               !empty($this->manual_kabin_petugas) ||
               !empty($this->cek_random_barang) ||
               !empty($this->barang_unpredictable) ||
               !empty($this->keterangan);
    }

    /**
     * Format waktu untuk tampilan
     */
    public function getFormattedStartAttribute(): ?string
    {
        return $this->start ? $this->start->format('H:i') : null;
    }

    /**
     * Format waktu untuk tampilan
     */
    public function getFormattedEndAttribute(): ?string
    {
        return $this->end ? $this->end->format('H:i') : null;
    }
}

