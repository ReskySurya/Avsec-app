<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormPencatatanPI extends Model
{
    protected $table = 'form_pencatatan_pi';

    protected $fillable = [
        'date',
        'grup',
        'in_time',
        'out_time',
        'name_person',
        'agency',
        'jenis_PI',
        'in_quantity',
        'out_quantity',
        'summary',
        'status',
        'sender_id',
        'senderSignature',
        'approved_id',
        'approvedSignature',
    ];

      /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'date',
    ];


    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
     public function scopeByDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }
}
