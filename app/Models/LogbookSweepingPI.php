<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LogbookSweepingPI extends Model
{
    use HasFactory;

    protected $table = 'logbook_sweeping_pi';
    protected $primaryKey = 'sweepingpiID';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'tenantID',
        'bulan',
        'tahun',
        'notes',
    ];

    protected $casts = [
        'bulan' => 'integer',
        'tahun' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();
        
        // Auto-generate sweepingpiID when creating
        static::creating(function ($sweepingPI) {
            if (empty($sweepingPI->sweepingpiID)) {
                $sweepingPI->sweepingpiID = static::generateSweepingpiID();
            }
        });
    }

    public static function generateSweepingpiID(): string
    {
        $prefix = 'SPI-';
        $date = now()->format('ymd'); // 6 digit date (YYMMDD)
        
        // Get the last sweepingpi for today to determine sequence
        $lastSweepingPI = static::whereDate('created_at', today())
            ->where('sweepingpiID', 'like', $prefix . $date . '%')
            ->orderBy('sweepingpiID', 'desc')
            ->first();

        if ($lastSweepingPI) {
            // Extract the sequence number from the last sweepingpi ID
            $lastSequence = (int)substr($lastSweepingPI->sweepingpiID, -1);
            $sequence = $lastSequence + 1;
        } else {
            $sequence = 1;
        }

        return $prefix . $date . $sequence;
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenantID', 'tenantID');
    }

    public function sweepingPIDetails(): HasMany
    {
        return $this->hasMany(LogbookSweepingPIDetail::class, 'sweepingpiID', 'sweepingpiID');
    }

    /**
     * Get completion statistics for this logbook
     */
    public function getCompletionStats()
    {
        $details = $this->sweepingPIDetails;
        $daysInMonth = \Carbon\Carbon::createFromDate($this->tahun, $this->bulan, 1)->daysInMonth;
        
        $totalChecked = 0;
        $totalItems = $details->count();
        $totalCells = $totalItems * $daysInMonth;

        foreach ($details as $detail) {
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $field = 'tanggal_' . $day;
                if ($detail->$field) {
                    $totalChecked++;
                }
            }
        }

        return [
            'completion_rate' => $totalCells > 0 ? round(($totalChecked / $totalCells) * 100) : 0,
            'total_checked' => $totalChecked,
            'total_pending' => $totalCells - $totalChecked,
            'total_items' => $totalItems,
            'days_in_month' => $daysInMonth,
        ];
    }
}