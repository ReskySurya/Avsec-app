<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogbookFacility extends Model
{
    protected $table = 'logbook_facility';
    protected $fillable = [
        'logbookID',
        'facility',
        'quantity',
        'description',
    ];

    public function logbook(): BelongsTo
    {
        return $this->belongsTo(Logbook::class, 'logbookID', 'logbookID');
    }
}
