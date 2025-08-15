<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProhibitedItem extends Model
{
    protected $table = 'prohibited_items';

    protected $fillable = [
        'tenantID',
        'items_name',
        'quantity',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenantID', 'tenantID');
    }
}
