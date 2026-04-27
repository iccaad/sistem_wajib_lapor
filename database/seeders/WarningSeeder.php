<?php

namespace Database\Seeders;

use App\Models\AttendancePeriod;
use App\Models\Participant;
use App\Models\User;
use App\Models\Warning;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class WarningSeeder extends Seeder
{
    /**
     * Seed sample compliance warnings.
     *
     * Creates realistic warnings for participants with poor attendance:
     * - Fajar (poor compliance) → level_1 + level_2
     * - Rizky (partial compliance) → level_1
     */
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();

        // Fajar Nugroho - poor compliance, gets level 1 and 2
        $fajar = Participant::where('nik', '3374011509020004')->first();
        $fajarPeriod = AttendancePeriod::where('participant_id', $fajar->id)
            ->where('status', 'completed')
            ->first();

        if ($fajarPeriod) {
            Warning::updateOrCreate(
                [
                    'participant_id' => $fajar->id,
                    'attendance_period_id' => $fajarPeriod->id,
                    'level' => Warning::LEVEL_1,
                ],
                [
                    'reason' => 'Tidak memenuhi kuota kehadiran periode ' . $fajarPeriod->period_start->format('d/m/Y') . ' - ' . $fajarPeriod->period_end->format('d/m/Y') . '. Kehadiran: ' . $fajarPeriod->attended_count . '/' . $fajarPeriod->target_count . '.',
                    'issued_at' => Carbon::parse($fajarPeriod->period_end)->addDay(),
                    'status' => 'active',
                    'notes' => null,
                    'created_by' => $admin->id,
                ]
            );

            Warning::updateOrCreate(
                [
                    'participant_id' => $fajar->id,
                    'attendance_period_id' => $fajarPeriod->id,
                    'level' => Warning::LEVEL_2,
                ],
                [
                    'reason' => 'Peringatan level 1 tidak diindahkan. Peserta masih belum menunjukkan itikad baik untuk memenuhi kewajiban lapor.',
                    'issued_at' => Carbon::parse($fajarPeriod->period_end)->addDays(3),
                    'status' => 'active',
                    'notes' => 'Sudah dihubungi via telepon, tidak ada respon.',
                    'created_by' => $admin->id,
                ]
            );
        }

        // Rizky Maulana - partial compliance, gets level 1 only
        $rizky = Participant::where('nik', '3374011208040002')->first();
        $rizkyPeriod = AttendancePeriod::where('participant_id', $rizky->id)
            ->where('status', 'completed')
            ->first();

        if ($rizkyPeriod) {
            Warning::updateOrCreate(
                [
                    'participant_id' => $rizky->id,
                    'attendance_period_id' => $rizkyPeriod->id,
                    'level' => Warning::LEVEL_1,
                ],
                [
                    'reason' => 'Kehadiran tidak mencukupi kuota periode ' . $rizkyPeriod->period_start->format('d/m/Y') . ' - ' . $rizkyPeriod->period_end->format('d/m/Y') . '. Kehadiran: ' . $rizkyPeriod->attended_count . '/' . $rizkyPeriod->target_count . '.',
                    'issued_at' => Carbon::parse($rizkyPeriod->period_end)->addDay(),
                    'status' => 'active',
                    'notes' => null,
                    'created_by' => $admin->id,
                ]
            );
        }
    }
}
