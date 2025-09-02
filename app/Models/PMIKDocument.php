<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PMIKDocument extends Model
{
    use HasFactory;

    protected $table = 'pmik_documents';

    protected $fillable = [
        'title',
        'original_name',
        'file_name',
        'file_path',
        'file_size',
        'mime_type',
        'folder_id',
        'uploaded_by'
    ];

    /**
     * Mendefinisikan relasi ke PMIKFolder.
     * Foreign key 'folder_id' ditentukan secara eksplisit.
     */
    public function folder()
    {
        // FIX: Tambahkan 'folder_id' sebagai argumen kedua
        return $this->belongsTo(PMIKFolder::class, 'folder_id');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getFileSizeHumanAttribute()
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }
}

