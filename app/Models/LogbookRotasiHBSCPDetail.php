<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Casts\TimeOnlyCast;

class LogbookRotasiHBSCPDetail extends Model
{
    use HasFactory;

    protected $table = 'logbook_rotasi_hbscp_details';

    protected $fillable = [
        'logbook_id',
        'start',
        'end',
        'pengatur_flow',
        'operator_xray',
        'manual_bagasi_petugas',
        'reunited',
        'keterangan'
    ];

    protected $casts = [
        'start' => TimeOnlyCast::class,
        'end' => TimeOnlyCast::class,
    ];

    /**
     * Relasi ke logbook utama
     */
    public function logbook(): BelongsTo
    {
        return $this->belongsTo(LogbookRotasiHBSCP::class, 'logbook_id');
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
     * Relasi ke user untuk manual bagasi petugas
     */
    public function manualBagasiOfficer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manual_bagasi_petugas');
    }

    /**
     * Relasi ke user untuk reunited
     */
    public function reunitedOfficer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reunited');
    }

    /**
     * Cek apakah baris ini memiliki data
     */
    public function hasData(): bool
    {
        return !empty($this->start) ||
               !empty($this->end) ||
               !empty($this->pengatur_flow) ||
               !empty($this->operator_xray) ||
               !empty($this->manual_bagasi_petugas) ||
               !empty($this->reunited) ||
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
