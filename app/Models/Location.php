<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'creationID',
    ];

    /**
     * Relasi: Location belongs to User (yang membuatnya)
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creationID');
    }

    /**
     * Relasi: Location many-to-many dengan Equipment
     */
    public function equipments(): BelongsToMany
    {
        return $this->belongsToMany(Equipment::class, 'equipment_location')
                    ->withPivot('description')
                    ->withTimestamps();
    }
}