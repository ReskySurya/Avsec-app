<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Equipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'description',
        'creationID',
    ];

    // Relasi: Equipment belongs to User (yang membuatnya)
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creationID');
    }

    /**
     * Relasi: Equipment many-to-many dengan Location
     */
    public function locations(): BelongsToMany
    {
        return $this->belongsToMany(Location::class, 'equipment_locations')
                    ->withPivot('description')
                    ->withTimestamps();
    }
}
