<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogbookRotasi extends Model
{
    use HasFactory;

    protected $table = 'logbook_rotasi';
    protected $primaryKey = 'id';
    public $incrementing = false; // karena pakai string custom (LRH-00001, dsb)
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'type',
        'date',
        'status',
        'created_by',
        'approved_by',
        'submitted_by',
        'submittedSignature',
        'approvedSignature',
        'notes',
    ];

    // Relasi ke User (pembuat)
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    // Relasi ke User (yang menyetujui)
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Relasi ke Detail Logbook
    public function details()
    {
        return $this->hasMany(LogbookRotasiDetail::class, 'logbook_id');
    }
}
