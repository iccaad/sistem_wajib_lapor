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
                'name' => 'PCC Polrestabes Semarang',
                'email' => 'pccpolrestabessemarang@gmail.com',
                'password' => Hash::make('presisi110'),
                'role' => 'admin',
                'is_active' => true,
            ],
            [
                'name' => 'AKBP Budi Santoso',
                'email' => 'budi.santoso@gmail.com',
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
