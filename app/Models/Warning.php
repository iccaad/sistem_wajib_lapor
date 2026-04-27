<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Warning extends Model
{
    use HasFactory;

    /**
     * Warning level constants.
     */
    public const LEVEL_1 = 'level_1';
    public const LEVEL_2 = 'level_2';
    public const LEVEL_3 = 'level_3';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'participant_id',
        'attendance_period_id',
        'level',
        'reason',
        'issued_at',
        'status',
        'resolved_at',
        'notes',
        'created_by',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'issued_at' => 'datetime',
            'resolved_at' => 'datetime',
        ];
    }

    // -------------------------------------------------------
    // Relationships
    // -------------------------------------------------------

    /**
     * The participant this warning was issued to.
     */
    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'participant_id');
    }

    /**
     * The attendance period that triggered this warning.
     */
    public function attendancePeriod(): BelongsTo
    {
        return $this->belongsTo(AttendancePeriod::class, 'attendance_period_id');
    }

    /**
     * The admin or system that issued this warning.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // -------------------------------------------------------
    // Helpers
    // -------------------------------------------------------

    /**
     * Check if this warning is still active (unresolved).
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if this warning has been resolved.
     */
    public function isResolved(): bool
    {
        return $this->resolved_at !== null;
    }

    /**
     * Get the numeric severity (1, 2, or 3) from the level string.
     */
    public function getSeverity(): int
    {
        return (int) str_replace('level_', '', $this->level);
    }
}
