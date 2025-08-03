<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Logbook extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     */
    protected $primaryKey = 'logbookID';

    /**
     * The "type" of the primary key ID.
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'logbookID',
        'date',
        'location_area_id',
        'grup',
        'shift',
        'senderID',
        'receivedID',
        'approvedID',
        'senderSignature',
        'receivedSignature',
        'approvedSignature',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'date' => 'date',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->logbookID)) {
                $model->logbookID = self::generateLogbookID();
            }
        });
    }

    /**
     * Generate unique logbook ID with format L-xxxxxxx
     */
    public static function generateLogbookID(): string
    {
        do {
            $randomNumber = str_pad(random_int(1, 9999999), 7, '0', STR_PAD_LEFT);
            $logbookID = 'L-' . $randomNumber;
        } while (self::where('logbookID', $logbookID)->exists());

        return $logbookID;
    }

    /**
     * Get the location area that owns the logbook.
     */
    public function locationArea(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_area_id');
    }

    /**
     * Get the sender user that owns the logbook.
     */
    public function senderBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'senderID');
    }

    /**
     * Get the receiver user that owns the logbook.
     */
    public function receiverBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receivedID');
    }

    /**
     * Get the approver user that owns the logbook.
     */
    public function approverBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approvedID');
    }

    /**
     * Get the logbook details for the logbook.
     */
    public function details(): HasMany
    {
        return $this->hasMany(LogbookDetail::class, 'logbookID', 'logbookID');
    }
}
