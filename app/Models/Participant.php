<?php

namespace App\Models;

use App\Services\PeriodService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Participant extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'assigned_admin_id',
        'full_name',
        'nik',
        'address',
        'phone',
        'violation_type_id',
        'case_notes',
        'supervision_start',
        'supervision_end',
        'quota_type',
        'quota_amount',
        'location_id',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'supervision_start' => 'date',
            'supervision_end' => 'date',
            'quota_amount' => 'integer',
        ];
    }

    // -------------------------------------------------------
    // Relationships
    // -------------------------------------------------------

    /**
     * The user account linked to this participant.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * The admin assigned to oversee this participant.
     */
    public function assignedAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_admin_id');
    }

    /**
     * Get the violation type.
     */
    public function violationType(): BelongsTo
    {
        return $this->belongsTo(ViolationType::class);
    }

    /**
     * Attendance periods (quota windows) for this participant.
     */
    public function attendancePeriods(): HasMany
    {
        return $this->hasMany(AttendancePeriod::class, 'participant_id');
    }

    /**
     * Attendance logs (check-in records) for this participant.
     */
    public function attendanceLogs(): HasMany
    {
        return $this->hasMany(AttendanceLog::class, 'participant_id');
    }

    /**
     * Failed attendance attempts by this participant.
     */
    public function attendanceAttempts(): HasMany
    {
        return $this->hasMany(AttendanceAttempt::class, 'participant_id');
    }

    /**
     * Compliance warnings issued to this participant.
     */
    public function warnings(): HasMany
    {
        return $this->hasMany(Warning::class, 'participant_id');
    }

    /**
     * The assigned reporting location for this participant.
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    // -------------------------------------------------------
    // Helpers
    // -------------------------------------------------------

    /**
     * Check if the participant's supervision period is currently active.
     * Both the status column AND the date window must be satisfied.
     */
    public function isActive(): bool
    {
        return $this->status === 'active'
            && today()->between($this->supervision_start, $this->supervision_end);
    }

    /**
     * Get remaining days of supervision.
     */
    public function getRemainingDays(): int
    {
        if (today()->isAfter($this->supervision_end)) {
            return 0;
        }

        return (int) now()->startOfDay()->diffInDays($this->supervision_end->startOfDay(), false);
    }

    /**
     * Check if the participant has already submitted attendance today.
     * Checks the attendance_date column in attendance_logs.
     */
    public function hasAbsentToday(): bool
    {
        return $this->attendanceLogs()
            ->where('attendance_date', today()->toDateString())
            ->exists();
    }

    /**
     * Get the currently active attendance period for this participant.
     * Delegates to PeriodService to keep logic centralised.
     */
    public function getCurrentPeriod(): ?AttendancePeriod
    {
        return (new PeriodService())->getCurrentPeriod($this);
    }
}
