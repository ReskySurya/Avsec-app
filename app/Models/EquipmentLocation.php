<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EquipmentLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'equipment_id',
        'location_id',
        'merk_type',
        'certificateInfo',
        'description',
    ];

    /**
     * Relasi: EquipmentLocation belongs to Equipment
     */
    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    /**
     * Relasi: EquipmentLocation belongs to Location
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id', 'id');
    }

        public function getEquipmentNameAttribute()
    {
        return $this->equipment ? $this->equipment->name : null;
    }

    /**
     * Get location name
     */
    public function getLocationNameAttribute()
    {
        return $this->location ? $this->location->name : null;
    }
}
