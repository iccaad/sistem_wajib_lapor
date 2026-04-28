<?php

namespace Database\Seeders;

use App\Models\ViolationType;
use Illuminate\Database\Seeder;

class ViolationTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            [
                'name' => 'Balap liar',
                'description' => 'Aksi balap liar di jalanan umum yang mengganggu ketertiban berlalu lintas.',
            ],
            [
                'name' => 'Kerusuhan / ketertiban umum',
                'description' => 'Melakukan tindakan kerusuhan atau mengganggu ketertiban umum.',
            ],
            [
                'name' => 'Perkelahian',
                'description' => 'Terlibat dalam perkelahian atau tindak kekerasan ringan.',
            ],
            [
                'name' => 'Vandalisme',
                'description' => 'Pengrusakan fasilitas umum atau properti orang lain.',
            ],
            [
                'name' => 'Penyalahgunaan narkoba ringan',
                'description' => 'Tindak pidana ringan terkait narkoba yang direhabilitasi.',
            ],
        ];

        foreach ($types as $type) {
            ViolationType::firstOrCreate(['name' => $type['name']], $type);
        }
    }
}
