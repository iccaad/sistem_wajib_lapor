<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Participant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ActivityLogSeeder extends Seeder
{
    /**
     * Seed sample admin activity logs.
     *
     * Creates realistic audit trail entries showing typical admin actions:
     * - Created participants
     * - Created/updated locations
     * - Issued warnings
     */
    public function run(): void
    {
        $admin1 = User::where('email', 'budi.santoso@polrestabes-smg.test')->first();
        $admin2 = User::where('email', 'siti.rahayu@polrestabes-smg.test')->first();

        $participants = Participant::all();

        $logs = [];

        // Log participant creation by admins
        foreach ($participants as $participant) {
            $creator = $participant->assigned_admin_id === $admin1->id ? $admin1 : $admin2;

            $logs[] = [
                'user_id' => $creator->id,
                'action' => 'created_participant',
                'target_type' => 'participant',
                'target_id' => $participant->id,
                'description' => 'Mendaftarkan peserta baru: ' . $participant->full_name . ' (NIK: ' . $participant->nik . ')',
                'metadata' => json_encode([
                    'violation_type' => $participant->violation_type,
                    'supervision_period' => $participant->supervision_start . ' s/d ' . $participant->supervision_end,
                ]),
                'ip_address' => '192.168.1.' . mt_rand(10, 50),
                'created_at' => Carbon::parse($participant->supervision_start)->subDays(mt_rand(1, 3)),
                'updated_at' => Carbon::parse($participant->supervision_start)->subDays(mt_rand(1, 3)),
            ];
        }

        // Log location creation
        $logs[] = [
            'user_id' => $admin1->id,
            'action' => 'created_location',
            'target_type' => 'location',
            'target_id' => 1,
            'description' => 'Membuat lokasi baru: GOR Jatidiri Semarang',
            'metadata' => json_encode(['radius_meters' => 150]),
            'ip_address' => '192.168.1.15',
            'created_at' => Carbon::parse('2026-03-25 09:00:00'),
            'updated_at' => Carbon::parse('2026-03-25 09:00:00'),
        ];

        $logs[] = [
            'user_id' => $admin1->id,
            'action' => 'updated_location',
            'target_type' => 'location',
            'target_id' => 2,
            'description' => 'Memperbarui radius lokasi Lapangan Polrestabes dari 80m menjadi 100m',
            'metadata' => json_encode(['old_radius' => 80, 'new_radius' => 100]),
            'ip_address' => '192.168.1.15',
            'created_at' => Carbon::parse('2026-03-28 10:30:00'),
            'updated_at' => Carbon::parse('2026-03-28 10:30:00'),
        ];

        // Log warning issuance
        $logs[] = [
            'user_id' => $admin1->id,
            'action' => 'issued_warning',
            'target_type' => 'participant',
            'target_id' => Participant::where('nik', '3374011509020004')->value('id'),
            'description' => 'Menerbitkan peringatan level 1 untuk Fajar Nugroho karena ketidakhadiran',
            'metadata' => json_encode(['warning_level' => 'level_1']),
            'ip_address' => '192.168.1.22',
            'created_at' => Carbon::now()->subDays(5),
            'updated_at' => Carbon::now()->subDays(5),
        ];

        foreach ($logs as $log) {
            ActivityLog::create($log);
        }
    }
}
