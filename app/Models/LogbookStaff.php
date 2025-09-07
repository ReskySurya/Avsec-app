<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogbookStaff extends Model
{
    protected $table = 'logbook_staff';
    protected $fillable = [
        'logbookID',
        'logbook_chief_id',
        'staffID',
        'classification',
        'description',
    ];

    public function logbook(): BelongsTo
    {
        return $this->belongsTo(Logbook::class, 'logbookID', 'logbookID');
    }

    public function logbookChief(): BelongsTo
    {
        return $this->belongsTo(LogbookChief::class, 'logbook_chief_id', 'logbookID');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staffID', 'id'); // Foreign key: staffID, Local key: id
    }
}
