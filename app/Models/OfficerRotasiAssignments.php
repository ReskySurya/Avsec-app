<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfficerRotasiAssignments extends Model
{
    use HasFactory;

    protected $table = 'officer_rotasi_assignments';

    protected $fillable = [
        'officer_id',
        'date',
        'start_time',
        'end_time',
        'shift_type',
        'location_type',
    ];

    // Relasi ke User (officer)
    public function officer()
    {
        return $this->belongsTo(User::class, 'officer_id');
    }

    // Relasi ke LogbookRotasiDetail
    public function details()
    {
        return $this->hasMany(LogbookRotasiDetail::class, 'officer_assignment_id');
    }
}
