<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogbookChiefKemajuan extends Model
{
    protected $table = 'logbook_chief_kemajuan';

    protected $fillable = [
        'logbook_chief_id',
        'jml_personil',
        'jml_hadir',
        'materi',
        'keterangan',
    ];

    public function logbookChief()
    {
        return $this->belongsTo(LogbookChief::class, 'logbook_chief_id', 'logbookID');
    }
}
