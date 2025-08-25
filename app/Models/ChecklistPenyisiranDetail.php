<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChecklistPenyisiranDetail extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'checklist_penyisiran_detail';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'checklist_penyisiran_id',
        'checklist_item_id',
        'isfindings',
        'iscondition',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'isfindings' => 'boolean',
        'iscondition' => 'boolean',
    ];

    /**
     * Get the parent checklist.
     */
    public function checklistPenyisiran(): BelongsTo
    {
        return $this->belongsTo(ChecklistPenyisiran::class, 'checklist_penyisiran_id', 'id');
    }

    /**
     * Get the checklist item.
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(ChecklistItem::class, 'checklist_item_id');
    }

    /**
     * Accessor untuk mendapatkan status temuan dalam bentuk text
     */
    public function getTemuanTextAttribute()
    {
        if (is_null($this->isfindings)) {
            return '-';
        }
        return $this->isfindings ? 'Ya' : 'Tidak';
    }

    /**
     * Accessor untuk mendapatkan status kondisi dalam bentuk text
     */
    public function getKondisiTextAttribute()
    {
        if (is_null($this->iscondition)) {
            return '-';
        }
        return $this->iscondition ? 'Baik' : 'Rusak';
    }
}