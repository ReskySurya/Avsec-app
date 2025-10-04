<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

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

    public function locations(): HasManyThrough
    {
        return $this->hasManyThrough(
            Location::class,          // Model tujuan yang ingin kita akses
            EquipmentLocation::class, // Model perantara
            'equipment_id',           // Foreign key di tabel equipment_locations
            'id',                     // Foreign key di tabel locations
            'id',                     // Local key di tabel equipment
            'location_id'             // Local key di tabel equipment_locations
        );
    }
}
