<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogbookDetail extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'logbookID',
        'start_time',
        'end_time',
        'summary',
        'description',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    /**
     * Get the logbook that owns the logbook detail.
     */
    public function logbook(): BelongsTo
    {
        return $this->belongsTo(Logbook::class, 'logbookID', 'logbookID');
    }

    /**
     * Get the duration between start_time and end_time
     */
    public function getDurationAttribute(): string
    {
        if (!$this->start_time || !$this->end_time) {
            return '0 minutes';
        }

        $start = \Carbon\Carbon::parse($this->start_time);
        $end = \Carbon\Carbon::parse($this->end_time);

        $diff = $start->diff($end);

        if ($diff->h > 0) {
            return $diff->h . ' jam ' . $diff->i . ' menit';
        }

        return $diff->i . ' menit';
    }
}
