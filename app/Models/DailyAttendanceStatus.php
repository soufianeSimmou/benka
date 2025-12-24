<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyAttendanceStatus extends Model
{
    protected $table = 'daily_attendance_status';

    protected $fillable = [
        'date',
        'is_completed',
        'completed_by',
        'completed_at',
    ];

    protected $casts = [
        'date' => 'date',
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function completedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }
}
