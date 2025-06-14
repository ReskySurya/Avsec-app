<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Report extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected $table = 'reports';

    /**
     * The primary key associated with the table.
     */
    protected $primaryKey = 'reportID';

    /**
     * Indicates if the primary key is auto-incrementing.
     */
    public $incrementing = false;

    /**
     * The data type of the primary key.
     */
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'testDate',
        'equipmentLocationID',
        'deviceInfo',
        'certificateInfo',
        'isFullFilled',
        'result',
        'note',
        'statusID',
        'submittedByID',
        'submitterSignature',
        'approvedByID',
        'approverSignature',
        'approvalNote',
        'reviewedByID',
        'reviewed_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'submitterSignature',
        'approverSignature',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'testDate' => 'datetime',
        'reviewed_at' => 'datetime',
        'isFullFilled' => 'boolean',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate reportID when creating
        static::creating(function ($report) {
            if (empty($report->reportID)) {
                $report->reportID = static::generateReportID();
            }
        });
    }

    /**
     * Generate unique report ID
     */
    public static function generateReportID(): string
    {
        $prefix = 'R-';
        $date = now()->format('ymd'); // 6 digit date (YYMMDD)

        // Get the last report for today to determine sequence
        $lastReport = static::whereDate('created_at', today())
                           ->where('reportID', 'like', $prefix . $date . '%')
                           ->orderBy('reportID', 'desc')
                           ->first();

        if ($lastReport) {
            // Extract the sequence number from the last report ID
            $lastSequence = (int)substr($lastReport->reportID, -1);
            $sequence = $lastSequence + 1;
        } else {
            $sequence = 1;
        }

        return $prefix . $date . $sequence;
    }

    /**
     * Get equipment through pivot table
     */
    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class, 'equipmentLocationID', 'id')
                    ->join('equipment_locations', 'equipment.id', '=', 'equipment_locations.equipment_id')
                    ->where('equipment_locations.id', $this->equipmentLocationID);
    }

    /**
     * Get location through pivot table
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'equipmentLocationID', 'id')
                    ->join('equipment_locations', 'locations.id', '=', 'equipment_locations.location_id')
                    ->where('equipment_locations.id', $this->equipmentLocationID);
    }

    /**
     * Alternative: Raw relationship to equipment location data
     */
    public function equipmentLocationData()
    {
        return $this->hasOne('App\Models\Equipment', 'id', 'equipmentLocationID')
                    ->select('equipment_locations.id', 'equipment.name as equipment_name', 'equipment.id as equipment_id', 'locations.name as location_name', 'locations.id as location_id')
                    ->join('equipment_locations', 'equipment.id', '=', 'equipment_locations.equipment_id')
                    ->join('locations', 'equipment_locations.location_id', '=', 'locations.id')
                    ->where('equipment_locations.id', $this->equipmentLocationID);
    }

    /**
     * Relationship: Report belongs to Status
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(ReportStatus::class, 'statusID');
    }

    /**
     * Relationship: Report submitted by User
     */
    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submittedByID');
    }

    /**
     * Relationship: Report approved by User
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approvedByID');
    }

    /**
     * Relationship: Report reviewed by User
     */
    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewedByID');
    }

    /**
     * Relationship: Report has many Report Details
     */
    public function reportDetails(): HasMany
    {
        return $this->hasMany(ReportDetail::class, 'reportID', 'reportID');
    }

    /**
     * Scope: Filter by status
     */
    public function scopeByStatus($query, $statusId)
    {
        return $query->where('statusID', $statusId);
    }

    /**
     * Scope: Filter by date range
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('testDate', [$startDate, $endDate]);
    }

    /**
     * Scope: Filter by submitter
     */
    public function scopeBySubmitter($query, $userId)
    {
        return $query->where('submittedByID', $userId);
    }

    /**
     * Scope: Get pending reports
     */
    public function scopePending($query)
    {
        return $query->whereHas('status', function($q) {
            $q->where('name', 'pending');
        });
    }

    /**
     * Scope: Get approved reports
     */
    public function scopeApproved($query)
    {
        return $query->whereHas('status', function($q) {
            $q->where('name', 'approved');
        });
    }

    /**
     * Scope: Get rejected reports
     */
    public function scopeRejected($query)
    {
        return $query->whereHas('status', function($q) {
            $q->where('name', 'rejected');
        });
    }

    /**
     * Accessor: Get formatted test date
     */
    public function getFormattedTestDateAttribute(): string
    {
        return $this->testDate ? $this->testDate->format('d/m/Y') : '';
    }

    /**
     * Accessor: Check if report is approved
     */
    public function getIsApprovedAttribute(): bool
    {
        return !is_null($this->approvedByID) && !is_null($this->approverSignature);
    }

    /**
     * Accessor: Check if report is reviewed
     */
    public function getIsReviewedAttribute(): bool
    {
        return !is_null($this->reviewedByID) && !is_null($this->reviewed_at);
    }

    /**
     * Accessor: Get status name
     */
    public function getStatusNameAttribute(): string
    {
        return $this->status ? $this->status->name : 'Unknown';
    }

    /**
     * Accessor: Get submitter name
     */
    public function getSubmitterNameAttribute(): string
    {
        return $this->submittedBy ? $this->submittedBy->name : 'Unknown';
    }

    /**
     * Method: Mark as approved
     */
    public function markAsApproved(int $approvedById, string $signature, string $note = null): bool
    {
        return $this->update([
            'approvedByID' => $approvedById,
            'approverSignature' => $signature,
            'approvalNote' => $note,
        ]);
    }

    /**
     * Method: Mark as reviewed
     */
    public function markAsReviewed(int $reviewedById): bool
    {
        return $this->update([
            'reviewedByID' => $reviewedById,
            'reviewed_at' => now(),
        ]);
    }

    /**
     * Method: Get full report data with relationships
     */
    public function getFullReportData(): array
    {
        return $this->load([
            'equipmentLocation',
            'status',
            'submittedBy',
            'approvedBy',
            'reviewedBy',
            'reportDetails'
        ])->toArray();
    }

    /**
     * Search scope
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('reportID', 'like', "%{$search}%")
              ->orWhere('deviceInfo', 'like', "%{$search}%")
              ->orWhere('note', 'like', "%{$search}%")
              ->orWhereHas('submittedBy', function($subQ) use ($search) {
                  $subQ->where('name', 'like', "%{$search}%");
              });
        });
    }

    /**
     * Method: Check if report is rejected
     */
    public function isRejected(): bool
    {
        return $this->status && $this->status->name === 'rejected';
    }
}
