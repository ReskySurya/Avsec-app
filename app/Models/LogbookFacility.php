<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogbookFacility extends Model
{
    protected $table = 'logbook_facility';
    protected $fillable = [
        'logbookID',
        'facilityID',
        'quantity',
        'description', 
    ];

    public function logbook(): BelongsTo
    {
        return $this->belongsTo(Logbook::class, 'logbookID', 'logbookID');
    }

    public function equipments(): BelongsTo
    {
        return $this->belongsTo(Equipment::class, 'facilityID', 'id'); // Foreign key: staffID, Local key: id
    }
}
