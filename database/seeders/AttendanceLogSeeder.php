<?php

namespace Database\Seeders;

use App\Models\AttendanceLog;
use App\Models\AttendancePeriod;
use App\Models\Location;
use App\Models\Participant;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AttendanceLogSeeder extends Seeder
{
    /**
     * Seed attendance logs with mixed compliance scenarios.
     *
     * Creates a realistic mix:
     * - Andi Pratama: compliant (attended most periods)
     * - Rizky Maulana: partial compliance (some missed)
     * - Dewi Safitri: good compliance
     * - Fajar Nugroho: poor compliance (mostly absent)
     * - Yoga Aditya: moderate compliance
     */
    public function run(): void
    {
        $locations = Location::where('is_active', true)->get();

        if ($locations->isEmpty()) {
            return;
        }

        $participants = Participant::with('attendancePeriods')->get();

        foreach ($participants as $participant) {
            $this->seedForParticipant($participant, $locations);
        }
    }

    /**
     * Create attendance logs for a specific participant.
     */
    private function seedForParticipant(Participant $participant, $locations): void
    {
        // Define compliance level per participant (by NIK for consistency)
        $complianceMap = [
            '3374012505030001' => 0.85,  // Andi - high compliance
            '3374011208040002' => 0.50,  // Rizky - partial
            '3374024703050003' => 0.90,  // Dewi - very good
            '3374011509020004' => 0.20,  // Fajar - poor
            '3374012201060005' => 0.60,  // Yoga - moderate
        ];

        $compliance = $complianceMap[$participant->nik] ?? 0.50;

        foreach ($participant->attendancePeriods as $period) {
            // Only create logs for completed periods or the current period
            if ($period->status !== 'completed' && !$period->isCurrent()) {
                continue;
            }

            $this->seedPeriodLogs($participant, $period, $locations, $compliance);
        }
    }

    /**
     * Create attendance logs within a specific period.
     */
    private function seedPeriodLogs(
        Participant $participant,
        AttendancePeriod $period,
        $locations,
        float $compliance
    ): void {
        $targetCount = $period->target_count;
        $attendedCount = 0;

        // Calculate how many days we should create logs for
        $logsToCreate = (int) round($targetCount * $compliance);
        $logsToCreate = min($logsToCreate, $targetCount);

        // Generate log dates spread across the period
        $periodStart = Carbon::parse($period->period_start);
        $periodEnd = Carbon::parse($period->period_end);

        // Don't create logs for future dates
        $effectiveEnd = $periodEnd->lt(today()) ? $periodEnd : today()->subDay();

        if ($effectiveEnd->lt($periodStart)) {
            return;
        }

        $totalDays = $periodStart->diffInDays($effectiveEnd) + 1;

        if ($totalDays <= 0 || $logsToCreate <= 0) {
            return;
        }

        // Spread logs evenly across the period
        $interval = max(1, (int) floor($totalDays / $logsToCreate));

        for ($i = 0; $i < $logsToCreate; $i++) {
            $logDate = $periodStart->copy()->addDays($i * $interval);

            // Don't go past effective end
            if ($logDate->gt($effectiveEnd)) {
                break;
            }

            // Don't create future logs
            if ($logDate->gte(today())) {
                break;
            }

            // Pick a random location
            $location = $locations->random();

            // Simulate GPS coordinates near the location (within radius)
            $latOffset = (mt_rand(-50, 50) / 1000000);
            $lngOffset = (mt_rand(-50, 50) / 1000000);

            $lat = (float) $location->latitude + $latOffset;
            $lng = (float) $location->longitude + $lngOffset;

            // Calculate a realistic distance (within radius)
            $distance = mt_rand(5, (int) ($location->radius_meters * 0.8));

            // Check if this date already has a log (unique constraint)
            $exists = AttendanceLog::where('participant_id', $participant->id)
                ->where('attendance_date', $logDate->toDateString())
                ->exists();

            if ($exists) {
                continue;
            }

            AttendanceLog::create([
                'participant_id' => $participant->id,
                'attendance_period_id' => $period->id,
                'location_id' => $location->id,
                'attendance_date' => $logDate->toDateString(),
                'attendance_time' => sprintf('%02d:%02d:00', mt_rand(8, 15), mt_rand(0, 59)),
                'latitude' => round($lat, 7),
                'longitude' => round($lng, 7),
                'distance_meters' => $distance,
                'photo_path' => null,  // No actual photos in seed data
                'notes' => null,
                'status' => 'valid',
            ]);

            $attendedCount++;
        }

        // Update the period's attended_count
        $period->update(['attended_count' => $attendedCount]);
    }
}
