<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManualBook extends Model
{
    protected $table = 'manualbooks';
    protected $primaryKey = 'id';
    public $incrementing = false; // Karena primary key adalah string
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'type',
        'shift',
        'date',
        'status',
        'created_by',
        'approved_by',
        'senderSignature',
        'approvedSignature',
        'approved_at',
        'notes',
    ];

    public function details()
    {
        return $this->hasMany(ManualBookDetail::class, 'manualbook_id', 'id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
