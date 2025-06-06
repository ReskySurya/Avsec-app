<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReportStatus extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'report_statuses';

    /**
     * The primary key associated with the table.
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'description',
        'isDefault',
        'label',
        'color'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'isDefault' => 'boolean',
    ];

    /**
     * Get all available status names
     */
    public static function getStatusNames(): array
    {
        return ['pending', 'approved', 'rejected'];
    }

    /**
     * Get status by name
     */
    public static function getByName(string $name): ?self
    {
        return static::where('name', $name)->first();
    }

    /**
     * Get default status
     */
    public static function getDefault(): ?self
    {
        return static::where('isDefault', true)->first();
    }

    /**
     * Scope: Get active statuses
     */
    public function scopeActive($query)
    {
        return $query->whereNotNull('name');
    }

    /**
     * Scope: Get by name
     */
    public function scopeByName($query, string $name)
    {
        return $query->where('name', $name);
    }

    /**
     * Relationship: Status has many Reports
     */
    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'statusID', 'id');
    }

    /**
     * Accessor: Get status with label
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->label ?: ucfirst($this->name);
    }

    /**
     * Accessor: Check if this is pending status
     */
    public function getIsPendingAttribute(): bool
    {
        return $this->name === 'pending';
    }

    /**
     * Accessor: Check if this is approved status
     */
    public function getIsApprovedAttribute(): bool
    {
        return $this->name === 'approved';
    }

    /**
     * Accessor: Check if this is rejected status
     */
    public function getIsRejectedAttribute(): bool
    {
        return $this->name === 'rejected';
    }

    /**
     * Get status color for UI
     */
    public function getStatusColorAttribute(): string
    {
        if ($this->color) {
            return $this->color;
        }

        return match($this->name) {
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Bootstrap method - create default statuses
     */
    public static function createDefaults(): void
    {
        $statuses = [
            [
                'name' => 'pending',
                'label' => 'Pending',
                'description' => 'Report is waiting for review',
                'isDefault' => true,
                'color' => 'warning'
            ],
            [
                'name' => 'approved',
                'label' => 'Approved',
                'description' => 'Report has been approved',
                'isDefault' => false,
                'color' => 'success'
            ],
            [
                'name' => 'rejected',
                'label' => 'Rejected',
                'description' => 'Report has been rejected',
                'isDefault' => false,
                'color' => 'danger'
            ]
        ];

        foreach ($statuses as $status) {
            static::firstOrCreate(
                ['name' => $status['name']], 
                $status
            );
        }
    }

    /**
     * Get reports count for this status
     */
    public function getReportsCountAttribute(): int
    {
        return $this->reports()->count();
    }
}