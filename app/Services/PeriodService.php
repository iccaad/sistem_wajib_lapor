<?php

namespace App\Services;

use App\Models\AttendancePeriod;
use App\Models\Participant;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class PeriodService
{
    /**
     * Generate the first attendance period for a newly registered participant.
     *
     * Period starts on supervision_start.
     * Duration: weekly = 7 days, monthly = 30 days.
     * Capped at supervision_end if it falls before the natural period end.
     */
    public function generateFirstPeriod(Participant $participant): AttendancePeriod
    {
        $periodStart = Carbon::parse($participant->supervision_start)->startOfDay();
        $supervisionEnd = Carbon::parse($participant->supervision_end)->startOfDay();

        $periodEnd = $this->calculatePeriodEnd($periodStart, $participant->quota_type);

        // Cap period_end at supervision_end
        if ($periodEnd->gt($supervisionEnd)) {
            $periodEnd = $supervisionEnd->copy();
        }

        return AttendancePeriod::create([
            'participant_id'  => $participant->id,
            'period_type'     => $participant->quota_type,
            'period_start'    => $periodStart->toDateString(),
            'period_end'      => $periodEnd->toDateString(),
            'target_count'    => $participant->quota_amount,
            'attended_count'  => 0,
            'status'          => 'active',
        ]);
    }

    /**
     * Generate the next attendance period after the given current period.
     *
     * Returns null if the next period would start after supervision_end.
     */
    public function generateNextPeriod(AttendancePeriod $currentPeriod): ?AttendancePeriod
    {
        $participant    = $currentPeriod->participant;
        $supervisionEnd = Carbon::parse($participant->supervision_end)->startOfDay();

        $nextStart = Carbon::parse($currentPeriod->period_end)->addDay()->startOfDay();

        if ($nextStart->gt($supervisionEnd)) {
            return null; // Supervision is over — no more periods
        }

        $nextEnd = $this->calculatePeriodEnd($nextStart, $participant->quota_type);

        if ($nextEnd->gt($supervisionEnd)) {
            $nextEnd = $supervisionEnd->copy();
        }

        return AttendancePeriod::create([
            'participant_id'  => $participant->id,
            'period_type'     => $participant->quota_type,
            'period_start'    => $nextStart->toDateString(),
            'period_end'      => $nextEnd->toDateString(),
            'target_count'    => $participant->quota_amount,
            'attended_count'  => 0,
            'status'          => 'active',
        ]);
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
     * Scheduler entry point — generates the next period for every participant
     * whose most recent active period ended yesterday.
     *
     * Should be called daily at midnight (00:05 WIB).
     *
     * @return int  Number of new periods generated.
     */
    public function generatePeriodsForAllActive(): int
    {
        $yesterday = today()->subDay()->toDateString();

        // Find all periods that ended yesterday and are still marked 'active'
        $endedPeriods = AttendancePeriod::where('period_end', $yesterday)
            ->where('status', 'active')
            ->with('participant')
            ->get();

        $generated = 0;

        foreach ($endedPeriods as $period) {
            // Mark old period as completed
            $period->update(['status' => 'completed']);

            $newPeriod = $this->generateNextPeriod($period);

            if ($newPeriod) {
                $generated++;
                Log::info("PeriodService: Generated new period #{$newPeriod->id} for participant #{$period->participant_id}");
            }
        }

        Log::info("PeriodService::generatePeriodsForAllActive — {$generated} period(s) generated.");

        return $generated;
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
