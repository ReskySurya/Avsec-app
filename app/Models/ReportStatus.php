<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReportStatus extends Model
{
    //
    protected $table = 'report_status';

    protected $primaryKey = 'statusID';
    protected $fillable = [
        'id',
        'name',
        'description',
        'isDefault',
        'label'
    ];
 
    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'id', 'id');
    }
}
