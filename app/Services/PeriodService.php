<?php

namespace App\Services;

use App\Models\AttendancePeriod;
use App\Models\Participant;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class PeriodService
{
    /**
     * Generate all attendance periods for a newly registered participant.
     *
     * Periods start on supervision_start and end on supervision_end.
     * Duration: weekly = 7 days, monthly = 30 days.
     */
    public function generateAllPeriods(Participant $participant): void
    {
        $currentStart = Carbon::parse($participant->supervision_start)->startOfDay();
        $supervisionEnd = Carbon::parse($participant->supervision_end)->startOfDay();

        while ($currentStart->lte($supervisionEnd)) {
            $periodEnd = $this->calculatePeriodEnd($currentStart, $participant->quota_type);

            // Cap period_end at supervision_end
            if ($periodEnd->gt($supervisionEnd)) {
                $periodEnd = $supervisionEnd->copy();
            }
            
            // Determine status based on dates
            $status = 'active';
            if ($periodEnd->lt(today())) {
                $status = 'completed';
            }

            AttendancePeriod::create([
                'participant_id'  => $participant->id,
                'period_type'     => $participant->quota_type,
                'period_start'    => $currentStart->toDateString(),
                'period_end'      => $periodEnd->toDateString(),
                'target_count'    => $participant->quota_amount,
                'attended_count'  => 0,
                'status'          => $status,
            ]);

            $currentStart = $periodEnd->copy()->addDay()->startOfDay();
        }
    }

    /**
     * Sync periods after a participant is updated by admin.
     * Updates target_count for all active periods so the dashboard reflects the new quota.
     * Also handles generating any missing future periods if supervision_end was extended.
     */
    public function syncPeriodsForUpdate(Participant $participant): void
    {
        $supervisionEnd = Carbon::parse($participant->supervision_end)->startOfDay();

        // 1. Update targets and period_type for all active periods.
        // We update period_type here because the admin might have changed it (e.g. weekly to monthly).
        $participant->attendancePeriods()
            ->where('status', 'active')
            ->update([
                'target_count' => $participant->quota_amount,
                'period_type'  => $participant->quota_type,
            ]);

        // 2. Adjust date boundaries for the first active period and remove subsequent ones,
        // so they can be regenerated with the correct boundaries based on the new period_type.
        $activePeriods = $participant->attendancePeriods()
            ->where('status', 'active')
            ->orderBy('period_start', 'asc')
            ->get();

        if ($activePeriods->isNotEmpty()) {
            $firstActive = $activePeriods->first();
            
            $newEnd = $this->calculatePeriodEnd(Carbon::parse($firstActive->period_start), $participant->quota_type);
            
            if ($newEnd->gt($supervisionEnd)) {
                $newEnd = $supervisionEnd->copy();
            }

            $firstActive->update([
                'period_end' => $newEnd->toDateString(),
                'status'     => $newEnd->lt(today()) ? 'completed' : 'active',
            ]);

            // Delete subsequent active periods (if they have 0 attendances) so they can be accurately regenerated
            $participant->attendancePeriods()
                ->where('status', 'active')
                ->where('id', '!=', $firstActive->id)
                ->where('attended_count', 0)
                ->delete();
        }

        // 3. If supervision_end was reduced, delete periods completely beyond the new end
        $participant->attendancePeriods()
            ->where('status', 'active')
            ->where('period_start', '>', $supervisionEnd)
            ->where('attended_count', 0)
            ->delete();

        // 4. Generate missing periods up to supervision_end
        $latestPeriod = $participant->attendancePeriods()->orderBy('period_end', 'desc')->first();
        
        $currentStart = $latestPeriod 
            ? Carbon::parse($latestPeriod->period_end)->addDay()->startOfDay() 
            : Carbon::parse($participant->supervision_start)->startOfDay();

        while ($currentStart->lte($supervisionEnd)) {
            $periodEnd = $this->calculatePeriodEnd($currentStart, $participant->quota_type);

            if ($periodEnd->gt($supervisionEnd)) {
                $periodEnd = $supervisionEnd->copy();
            }

            AttendancePeriod::create([
                'participant_id'  => $participant->id,
                'period_type'     => $participant->quota_type,
                'period_start'    => $currentStart->toDateString(),
                'period_end'      => $periodEnd->toDateString(),
                'target_count'    => $participant->quota_amount,
                'attended_count'  => 0,
                'status'          => $periodEnd->lt(today()) ? 'completed' : 'active',
            ]);

            $currentStart = $periodEnd->copy()->addDay()->startOfDay();
        }
    }

    /**
     * Get the currently active attendance period for a participant.
     *
     * A period is "current" if today falls within its date range and
     * its status is 'active'.
     */
    public function getCurrentPeriod(Participant $participant): ?AttendancePeriod
    {
        return $participant->attendancePeriods()
            ->where('status', 'active')
            ->where('period_start', '<=', today())
            ->where('period_end', '>=', today())
            ->first();
    }

    /**
     * Scheduler entry point — updates the status of old periods.
     *
     * Should be called daily at midnight (00:05 WIB).
     *
     * @return int  Number of periods updated.
     */
    public function updateExpiredPeriodsStatus(): int
    {
        $yesterday = today()->subDay()->toDateString();

        // Find all periods that ended yesterday and are still marked 'active'
        $endedPeriods = AttendancePeriod::where('period_end', $yesterday)
            ->where('status', 'active')
            ->get();

        $updated = 0;

        foreach ($endedPeriods as $period) {
            // Mark old period as completed
            $period->update(['status' => 'completed']);
            $updated++;
        }

        Log::info("PeriodService::updateExpiredPeriodsStatus — {$updated} period(s) marked as completed.");

        return $updated;
    }

    // -------------------------------------------------------
    // Private Helpers
    // -------------------------------------------------------

    /**
     * Calculate the natural end date for a period based on type.
     *
     * weekly  → start + 6 days  (7-day window)
     * monthly → start + 29 days (30-day window)
     */
    private function calculatePeriodEnd(Carbon $start, string $quotaType): Carbon
    {
        return match ($quotaType) {
            'monthly' => $start->copy()->addDays(29),
            default   => $start->copy()->addDays(6), // 'weekly'
        };
    }
}
