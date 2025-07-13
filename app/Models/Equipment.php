<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Equipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'creationID',
    ];

    /**
     * Relasi: Equipment belongs to User (yang membuatnya)
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creationID');
    }

 
    /**
     * Relasi: Equipment has many EquipmentLocation (untuk akses langsung ke pivot)
     */
    public function equipmentLocations(): HasMany
    {
        return $this->hasMany(EquipmentLocation::class);
    }
}