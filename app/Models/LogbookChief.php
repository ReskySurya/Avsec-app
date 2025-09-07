<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogbookChief extends Model
{
    protected $table = 'logbook_chief';

    protected $primaryKey = 'logbookID';

    public $incrementing = false;

    protected $fillable = [
        'logbookID',
        'date',
        'grup',
        'shift',
        'created_by',
        'approved_by',
        'senderSignature',
        'approvedSignature',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public static function generateLogbookID(): string
    {
        $datePart = date('dmy');
        $latestLogbook = self::where('logbookID', 'like', "LTL-$datePart-%")
            ->orderBy('logbookID', 'desc')
            ->first();

        if ($latestLogbook) {
            $lastNumber = (int)substr($latestLogbook->logbookID, -2);
            $newNumber = str_pad($lastNumber + 1, 2, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '01';
        }

        return "LTL-$datePart-$newNumber";
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function details()
    {
        return $this->hasMany(LogbookDetail::class, 'logbook_chief_id', 'logbookID');
    }

    public function facility() 
    {
        return $this->hasMany(LogbookFacility::class, 'logbook_chief_id', 'logbookID');
    }
    public function personil()
    {
        return $this->hasMany(LogbookStaff::class, 'logbook_chief_id', 'logbookID');
    }
    public function kemajuan()
    {
        return $this->hasMany(LogbookChiefKemajuan::class, 'logbook_chief_id', 'logbookID');
    }
}
