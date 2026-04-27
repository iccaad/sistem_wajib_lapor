<?php

namespace Database\Seeders;

use App\Models\AttendancePeriod;
use App\Models\Participant;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AttendancePeriodSeeder extends Seeder
{
    /**
     * Seed attendance periods for all active participants.
     *
     * Creates realistic quota windows based on each participant's
     * quota_type (weekly/monthly) and supervision dates.
     * Generates periods covering the current operational window.
     */
    public function run(): void
    {
        $participants = Participant::where('status', 'active')->get();

        foreach ($participants as $participant) {
            if ($participant->quota_type === 'weekly') {
                $this->seedWeeklyPeriods($participant);
            } else {
                $this->seedMonthlyPeriods($participant);
            }
        }
    }

    /**
     * Create weekly attendance periods for a participant.
     */
    private function seedWeeklyPeriods(Participant $participant): void
    {
        // Generate periods from supervision_start, week by week
        $start = Carbon::parse($participant->supervision_start)->startOfWeek(Carbon::MONDAY);
        $supervisionEnd = Carbon::parse($participant->supervision_end);

        // Generate up to 8 weeks of periods (enough for demo)
        $weeksGenerated = 0;
        $current = $start->copy();

        while ($current->lte($supervisionEnd) && $weeksGenerated < 8) {
            $periodEnd = $current->copy()->endOfWeek(Carbon::SUNDAY);

            // Don't go past supervision end
            if ($periodEnd->gt($supervisionEnd)) {
                $periodEnd = $supervisionEnd->copy();
            }

            // Determine status based on dates
            $status = 'active';
            if ($periodEnd->lt(today())) {
                $status = 'completed';
            }

            AttendancePeriod::updateOrCreate(
                [
                    'participant_id' => $participant->id,
                    'period_start' => $current->toDateString(),
                    'period_end' => $periodEnd->toDateString(),
                ],
                [
                    'period_type' => 'weekly',
                    'target_count' => $participant->quota_amount,
                    'attended_count' => 0,  // Will be updated by AttendanceLogSeeder
                    'status' => $status,
                ]
            );

            $current->addWeek();
            $weeksGenerated++;
        }
    }

    /**
     * Create monthly attendance periods for a participant.
     */
    private function seedMonthlyPeriods(Participant $participant): void
    {
        $start = Carbon::parse($participant->supervision_start)->startOfMonth();
        $supervisionEnd = Carbon::parse($participant->supervision_end);

        // Generate up to 4 months of periods
        $monthsGenerated = 0;
        $current = $start->copy();

        while ($current->lte($supervisionEnd) && $monthsGenerated < 4) {
            $periodEnd = $current->copy()->endOfMonth();

            if ($periodEnd->gt($supervisionEnd)) {
                $periodEnd = $supervisionEnd->copy();
            }

            $status = 'active';
            if ($periodEnd->lt(today())) {
                $status = 'completed';
            }

            AttendancePeriod::updateOrCreate(
                [
                    'participant_id' => $participant->id,
                    'period_start' => $current->toDateString(),
                    'period_end' => $periodEnd->toDateString(),
                ],
                [
                    'period_type' => 'monthly',
                    'target_count' => $participant->quota_amount,
                    'attended_count' => 0,
                    'status' => $status,
                ]
            );

            $current->addMonth()->startOfMonth();
            $monthsGenerated++;
        }
    }
}
