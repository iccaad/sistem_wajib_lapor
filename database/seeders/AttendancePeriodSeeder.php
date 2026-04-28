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
        $periodService = new \App\Services\PeriodService();

        foreach ($participants as $participant) {
            // Delete existing periods if any to avoid duplicates in case of re-seeding without fresh
            $participant->attendancePeriods()->delete();
            
            // Use the same service used for real users to ensure perfectly consistent date generation
            $periodService->generateAllPeriods($participant);
        }
    }
}
