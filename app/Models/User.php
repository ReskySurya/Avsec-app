<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'nip',
        'lisensi',
        'email',
        'password',
        'role_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relasi ke Role
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    // Helper methods untuk cek role
    public function isSuperAdmin(): bool
    {
        return $this->role->name === Role::SUPERADMIN;
    }

    public function isSupervisor(): bool
    {
        return $this->role->name === Role::SUPERVISOR;
    }

    public function isOfficer(): bool
    {
        return $this->role->name === Role::OFFICER;
    }

    public function hasRole(string $role): bool
    {
        return $this->role->name === $role;
    }

    // Di dalam class User, tambahkan:
    public function equipments(): HasMany
    {
        return $this->hasMany(Equipment::class, 'creationID');
    }

    public function locations(): HasMany
    {
        return $this->hasMany(Location::class, 'creationID');
    }   
}
