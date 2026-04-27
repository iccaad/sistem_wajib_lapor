<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'action',
        'target_type',
        'target_id',
        'description',
        'metadata',
        'ip_address',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'target_id' => 'integer',
        ];
    }

    // -------------------------------------------------------
    // Relationships
    // -------------------------------------------------------

    /**
     * The admin who performed this action.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // -------------------------------------------------------
    // Helpers
    // -------------------------------------------------------

    /**
     * Get the target model instance (polymorphic-style manual resolve).
     * Returns null if target_type or target_id is missing, or if model not found.
     */
    public function getTarget(): ?Model
    {
        if (!$this->target_type || !$this->target_id) {
            return null;
        }

        $modelMap = [
            'participant' => Participant::class,
            'location' => Location::class,
            'warning' => Warning::class,
            'attendance_log' => AttendanceLog::class,
            'attendance_period' => AttendancePeriod::class,
            'user' => User::class,
        ];

        $modelClass = $modelMap[$this->target_type] ?? null;

        if (!$modelClass) {
            return null;
        }

        return $modelClass::find($this->target_id);
    }
}
