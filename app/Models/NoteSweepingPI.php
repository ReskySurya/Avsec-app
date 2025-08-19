<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NoteSweepingPI extends Model
{
    use HasFactory;

    protected $table = 'notes_sweeping_pi';

    protected $fillable = [
        'sweepingpiID',
        'tanggal',
        'notes'
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    /**
     * Relationship to LogbookSweepingPI
     */
    public function logbookSweepingPI(): BelongsTo
    {
        return $this->belongsTo(LogbookSweepingPI::class, 'sweepingpiID', 'sweepingpiID');
    }

    /**
     * Scope untuk filter berdasarkan logbook
     */
    public function scopeByLogbook($query, $sweepingpiID)
    {
        return $query->where('sweepingpiID', $sweepingpiID);
    }

    /**
     * Scope untuk filter berdasarkan tanggal
     */
    public function scopeByDate($query, $tanggal)
    {
        return $query->where('tanggal', $tanggal);
    }

    /**
     * Scope untuk filter berdasarkan rentang tanggal
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal', [$startDate, $endDate]);
    }
}