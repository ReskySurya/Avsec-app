<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManualBookDetail extends Model
{
    protected $table = 'manualbook_details';

    protected $fillable = [
        'manualbook_id',
        'time',
        'name',
        'pax',
        'flight',
        'orang',
        'barang',
        'temuan',
        'keterangan',
    ];

    public function manualBook()
    {
        return $this->belongsTo(ManualBook::class, 'manualbook_id', 'id');
    }
}
