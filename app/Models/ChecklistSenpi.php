<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ChecklistSenpi extends Model
{
    use HasUuids;

    /**
     * The table associated with the model.
     */
    protected $table = 'checklist_senpi';

    /**
     * The primary key associated with the table.
     */
    protected $primaryKey = 'id';

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
        'date',
        'name',
        'agency',
        'flightNumber',
        'destination',
        'typeSenpi',
        'quantitySenpi',
        'quantityMagazine',
        'quantityBullet',
        'licenseNumber',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'date' => 'date',
        'quantitySenpi' => 'integer',
        'quantityMagazine' => 'integer',
        'quantityBullet' => 'integer',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        // Add any sensitive attributes here if needed
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'date' => 'date',
            'quantitySenpi' => 'integer',
            'quantityMagazine' => 'integer',
            'quantityBullet' => 'integer',
        ];
    }

    // Accessor untuk format tanggal yang lebih friendly
    public function getFormattedDateAttribute()
    {
        return $this->date->format('d/m/Y');
    }

    // Mutator untuk memastikan flight number dalam format uppercase
    public function setFlightNumberAttribute($value)
    {
        $this->attributes['flightNumber'] = $value ? strtoupper($value) : null;
    }

    // Scope untuk filter berdasarkan tanggal
    public function scopeByDate($query, $date)
    {
        return $query->where('date', $date);
    }

    // Scope untuk filter berdasarkan agency
    public function scopeByAgency($query, $agency)
    {
        return $query->where('agency', $agency);
    }

    // Scope untuk filter berdasarkan tipe senjata
    public function scopeByTypeSenpi($query, $type)
    {
        return $query->where('typeSenpi', $type);
    }

    // Method untuk mendapatkan total senjata dan amunisi
    public function getTotalWeaponsAttribute()
    {
        return $this->quantitySenpi + $this->quantityMagazine + $this->quantityBullet;
    }
}