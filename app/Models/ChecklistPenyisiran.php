<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChecklistPenyisiran extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'checklist_penyisiran';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The data type of the primary key.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'date',
        'time',
        'type',
        'grup',
        'status',
        'sender_id',
        'received_id',
        'approved_id',
        'senderSignature',
        'receivedSignature',
        'approvedSignature',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'date',
        'time' => 'datetime:H:i',
    ];

    /**
     * Get the details for the checklist.
     */
    public function details(): HasMany
    {
        return $this->hasMany(ChecklistPenyisiranDetail::class, 'checklist_penyisiran_id', 'id');
    }

    /**
     * Get the sender (user who created the checklist).
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get the receiver (user who received the checklist).
     */
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_id');
    }

    /**
     * Get the approver (user who approved the checklist).
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_id');
    }

    /**
     * Scope untuk filter berdasarkan grup
     */
    public function scopeGrup($query, $grup)
    {
        return $query->where('grup', $grup);
    }

    /**
     * Scope untuk filter berdasarkan status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope untuk filter berdasarkan tanggal
     */
    public function scopeByDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }
}