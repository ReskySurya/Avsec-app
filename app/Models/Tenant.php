<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    //
    protected $table = 'tenants';

    protected $primaryKey = 'tenantID';

    public $incrementing = false;
    
    protected $fillable = [
        'tenantID',
        'tenant_name',
        'supervisorSignature',
    ];

    public static function generateTenantID(): string
    {
        do {
            $randomNumber = str_pad(random_int(1, 9999999), 7, '0', STR_PAD_LEFT);
            $tenantID = 'T-' . $randomNumber;
        } while (self::where('tenantID', $tenantID)->exists());

        return $tenantID;
    }

    public function details(): HasMany
    {
        return $this->hasMany(ProhibitedItem::class, 'tenantID', 'tenantID');
    }
}
