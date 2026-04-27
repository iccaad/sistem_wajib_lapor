<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     *
     * Execution order respects foreign key dependencies:
     * 1. Admin users (no FK deps)
     * 2. Participant users + profiles (FK → users)
     * 3. Locations (FK → users)
     * 4. Attendance periods (FK → participants)
     * 5. Attendance logs (FK → participants, periods, locations)
     * 6. Warnings (FK → participants, periods, users)
     * 7. Activity logs (FK → users)
     */
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            ParticipantUserSeeder::class,
            LocationSeeder::class,
            AttendancePeriodSeeder::class,
            AttendanceLogSeeder::class,
            WarningSeeder::class,
            ActivityLogSeeder::class,
        ]);
    }
}
