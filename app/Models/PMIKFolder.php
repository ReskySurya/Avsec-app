<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PMIKFolder extends Model
{
    use HasFactory;

    protected $table = 'pmik_folders';

    protected $fillable = ['name', 'slug', 'description', 'created_by'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($folder) {
            $folder->slug = Str::slug($folder->name);
        });

        static::updating(function ($folder) {
            $folder->slug = Str::slug($folder->name);
        });
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Mendefinisikan relasi ke PMIKDocument.
     * Foreign key 'folder_id' ditentukan secara eksplisit.
     */
    public function documents()
    {
        // FIX: Tambahkan 'folder_id' sebagai argumen kedua
        return $this->hasMany(PMIKDocument::class, 'folder_id');
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}

