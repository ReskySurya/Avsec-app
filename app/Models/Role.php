<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    // Relasi ke User
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    // Konstanta untuk role
    const SUPERADMIN = 'superadmin';
    const SUPERVISOR = 'supervisor';
    const OFFICER = 'officer';

    public static function getRoles(): array
    {
        return [
            self::SUPERADMIN,
            self::SUPERVISOR,
            self::OFFICER,
        ];
    }
}
