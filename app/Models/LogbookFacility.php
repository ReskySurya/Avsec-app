<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogbookFacility extends Model
{
    protected $table = 'logbook_facility';
    protected $fillable = [
        'logbookID',
        'logbook_chief_id',
        'facility',
        'quantity',
        'description',
    ];

    public function logbook(): BelongsTo
    {
        return $this->belongsTo(Logbook::class, 'logbookID', 'logbookID');
    }

    public function logbookChief(): BelongsTo
    {
        return $this->belongsTo(LogbookChief::class, 'logbook_chief_id', 'logbookID');
    }
}
