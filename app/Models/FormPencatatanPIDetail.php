<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormPencatatanPIDetail extends Model
{
    use HasFactory;

    protected $table = 'form_pencatatan_pi_details';

    protected $fillable = [
        'form_pencatatan_pi_id',
        'jenis_pi',
        'in_quantity',
        'out_quantity',
    ];

    public function formPencatatanPI(): BelongsTo
    {
        return $this->belongsTo(FormPencatatanPI::class, 'form_pencatatan_pi_id');
    }
}
