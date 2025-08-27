<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormPencatatanPI extends Model
{
    protected $table = 'form_pencatatan_pi';

    protected $fillable = [
        'date',
        'grup',
        'in_time',
        'out_time',
        'name_person',
        'agency',
        'jenis_PI',
        'in_quantity',
        'out_quantity',
        'summary',
    ];
}
