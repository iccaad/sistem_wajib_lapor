<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\User;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Seed official attendance check-in locations.
     *
     * Creates 4 real locations in the Semarang area that could serve
     * as official reporting points for a police department system.
     */
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();

        $locations = [
            [
                'name' => 'GOR Jatidiri Semarang',
                'address' => 'Jl. Jenderal Soedirman, Pleburan, Kec. Semarang Selatan, Kota Semarang',
                'latitude' => -6.9934000,
                'longitude' => 110.3912000,
                'radius_meters' => 150,
                'is_active' => true,
                'created_by' => $admin->id,
            ],
            [
                'name' => 'Lapangan Polrestabes Semarang',
                'address' => 'Jl. Sukun Raya No.1, Lamper Kidul, Kec. Semarang Selatan, Kota Semarang',
                'latitude' => -6.9826000,
                'longitude' => 110.4098000,
                'radius_meters' => 100,
                'is_active' => true,
                'created_by' => $admin->id,
            ],
            [
                'name' => 'Balai Pemuda Kota Semarang',
                'address' => 'Jl. Pemuda No.150, Sekayu, Kec. Semarang Tengah, Kota Semarang',
                'latitude' => -6.9838000,
                'longitude' => 110.4103000,
                'radius_meters' => 100,
                'is_active' => true,
                'created_by' => $admin->id,
            ],
            [
                'name' => 'Taman Indonesia Kaya',
                'address' => 'Jl. Menteri Supeno No.1, Mugassari, Kec. Semarang Selatan, Kota Semarang',
                'latitude' => -6.9845000,
                'longitude' => 110.4092000,
                'radius_meters' => 120,
                'is_active' => true,
                'created_by' => $admin->id,
            ],
        ];

        foreach ($locations as $location) {
            Location::updateOrCreate(
                ['name' => $location['name']],
                $location
            );
        }
    }
}
