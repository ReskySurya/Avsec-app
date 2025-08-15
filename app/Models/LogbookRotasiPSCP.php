<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogbookRotasiPSCP extends Model
{
    protected $table = 'logbook_rotasipscp';
    public $incrementing = false; // Karena primary key bukan auto increment
    protected $keyType = 'string';

    protected $fillable = [
        'id', 'date', 'status'
    ];

    public function details()
    {
        return $this->hasMany(LogbookRotasiPSCPDetail::class, 'logbook_id');
    }
}
