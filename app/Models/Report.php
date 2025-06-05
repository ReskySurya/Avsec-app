<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected $table = 'report';

    /**
     * The primary key associated with the table.
     */
    protected $primaryKey = 'reportID';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'reportID',
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
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'isFullFilled' => 'boolean',
    ];

    /**
     * The attributes that should be mutated to dates.
     */
    protected $dates = [
        'testDate',
        'reviewed_at',
        'created_at',
        'updated_at',
        'deleted_at',
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
     * Relationship: Report belongs to Equipment Location
     */
    public function equipmentLocation(): BelongsTo
    {
        return $this->belongsTo(EquipmentLocation::class, 'equipmentLocationID');
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
     * Mutator: Set test date
     */
    // public function setTestDateAttribute($value)
    // {
    //     $this->attributes['testDate'] = $value ? Carbon::parse($value) : null;
    // }

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
            'reviewedBy'
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
     * Get reports summary
     */
    // public static function getSummary(): array
    // {
    //     return [
    //         'total' => static::count(),
    //         'pending' => static::pending()->count(),
    //         'approved' => static::approved()->count(),
    //         'today' => static::whereDate('created_at', today())->count(),
    //         'this_month' => static::whereMonth('created_at', now()->month)->count(),
    //     ];
    // }
}