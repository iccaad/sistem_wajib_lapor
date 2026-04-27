<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Seed admin user accounts.
     *
     * Creates 2 admin accounts representing typical police station officers:
     * - AKBP Budi Santoso (Kapolres / station chief)
     * - AKP Siti Rahayu (Kasat Binmas / community policing unit head)
     */
    public function run(): void
    {
        $admins = [
            [
                'name' => 'AKBP Budi Santoso',
                'email' => 'budi.santoso@polrestabes-smg.test',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'is_active' => true,
            ],
            [
                'name' => 'AKP Siti Rahayu',
                'email' => 'siti.rahayu@polrestabes-smg.test',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'is_active' => true,
            ],
        ];

        foreach ($admins as $admin) {
            User::updateOrCreate(
                ['email' => $admin['email']],
                $admin
            );
        }
    }
}
