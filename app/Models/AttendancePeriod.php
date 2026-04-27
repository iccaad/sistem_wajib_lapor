<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AttendancePeriod extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'participant_id',
        'period_type',
        'period_start',
        'period_end',
        'target_count',
        'attended_count',
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
            'period_start' => 'date',
            'period_end' => 'date',
            'target_count' => 'integer',
            'attended_count' => 'integer',
        ];
    }

    // -------------------------------------------------------
    // Relationships
    // -------------------------------------------------------

    /**
     * The participant this period belongs to.
     */
    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'participant_id');
    }

    /**
     * Attendance logs recorded during this period.
     */
    public function attendanceLogs(): HasMany
    {
        return $this->hasMany(AttendanceLog::class, 'attendance_period_id');
    }

    /**
     * Warnings triggered by this attendance period.
     */
    public function warnings(): HasMany
    {
        return $this->hasMany(Warning::class, 'attendance_period_id');
    }

    // -------------------------------------------------------
    // Helpers
    // -------------------------------------------------------

    /**
     * Check if the quota for this period has been fulfilled.
     */
    public function isFulfilled(): bool
    {
        return $this->attended_count >= $this->target_count;
    }

    /**
     * Get the remaining attendance count needed to fulfill the quota.
     */
    public function getRemainingCount(): int
    {
        return max(0, $this->target_count - $this->attended_count);
    }

    /**
     * Get remaining calendar days in this period (from today to period_end).
     * Returns 0 if the period has already ended.
     */
    public function getRemainingDays(): int
    {
        if (today()->isAfter($this->period_end)) {
            return 0;
        }

        return (int) now()->startOfDay()->diffInDays($this->period_end->startOfDay(), false);
    }

    /**
     * Check if the period has ended (period_end is in the past).
     */
    public function hasEnded(): bool
    {
        return today()->isAfter($this->period_end);
    }

    /**
     * Check if this period is currently active (date-wise).
     */
    public function isCurrent(): bool
    {
        return $this->status === 'active'
            && today()->between($this->period_start, $this->period_end);
    }
}
