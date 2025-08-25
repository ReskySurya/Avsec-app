<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChecklistKendaraanDetail extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'checklist_kendaraan_details';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'checklist_kendaraan_id',
        'checklist_item_id',
        'is_ok',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_ok' => 'boolean',
    ];

    /**
     * Get the parent checklist.
     */
    public function checklistKendaraan(): BelongsTo
    {
        return $this->belongsTo(ChecklistKendaraan::class, 'checklist_kendaraan_id', 'id');
    }

    /**
     * Get the checklist item.
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(ChecklistItem::class, 'checklist_item_id');
    }
}
