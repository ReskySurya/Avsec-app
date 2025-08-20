<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChecklistKendaraan extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'checklist_kendaraan';

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
        'type',
        'shift',
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
    ];

    /**
     * Get the user who sent the checklist.
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get the user who received the checklist.
     */
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_id');
    }

    /**
     * Get the user who approved the checklist.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_id');
    }

    /**
     * Get the details for the checklist.
     */
    public function details(): HasMany
    {
        return $this->hasMany(ChecklistKendaraanDetail::class, 'checklist_kendaraan_id', 'id');
    }
}
