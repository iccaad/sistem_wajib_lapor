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
                'is_root_super_admin' => true,
                'is_active' => true,
            ],
        ];

        $polseks = [
            'Polsek Semarang Tengah' => 'semarangtengah@libas.id',
            'Polsek Semarang Utara' => 'semarangutara@libas.id',
            'Polsek Semarang Selatan' => 'semarangselatan@libas.id',
            'Polsek Semarang Barat' => 'semarangbarat@libas.id',
            'Polsek Semarang Timur' => 'semarangtimur@libas.id',
            'Polsek Gajahmungkur' => 'gajahmungkur@libas.id',
            'Polsek Candisari' => 'candisari@libas.id',
        ];

        foreach ($polseks as $name => $email) {
            $admins[] = [
                'name' => $name,
                'email' => $email,
                'password' => Hash::make('11223344'),
                'role' => 'admin',
                'is_root_super_admin' => false,
                'is_active' => true,
            ];
        }

        foreach ($admins as $admin) {
            User::updateOrCreate(
                ['email' => $admin['email']],
                $admin
            );
        }
    }
}
