<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\Participant;
use App\Models\User;
use Illuminate\Database\Seeder;

class ParticipantUserSeeder extends Seeder
{
    /**
     * Seed participant (peserta) user accounts and their profile data.
     *
     * Creates 5 participants with:
     * - Realistic Indonesian names
     * - Valid Semarang-area NIK (prefix 3374)
     * - Varied violation types
     * - Mixed quota configurations (weekly/monthly)
     * - Assigned to admin accounts
     * - Assigned reporting locations matching quota_amount
     */
    public function run(): void
    {
        // Fetch admin users to assign as supervisors
        $admin1 = User::where('email', 'budi.santoso@polrestabes-smg.test')->first();
        $admin2 = User::where('email', 'siti.rahayu@polrestabes-smg.test')->first();

        // Fetch location IDs for assignment
        $locGor    = Location::where('name', 'GOR Jatidiri Semarang')->first();
        $locPolres = Location::where('name', 'Lapangan Polrestabes Semarang')->first();
        $locBalai  = Location::where('name', 'Balai Pemuda Kota Semarang')->first();
        $locTaman  = Location::where('name', 'Taman Indonesia Kaya')->first();

        $vtBalap = \App\Models\ViolationType::where('name', 'Balap liar')->first();
        $vtRusuh = \App\Models\ViolationType::where('name', 'Kerusuhan / ketertiban umum')->first();
        $vtKelahi = \App\Models\ViolationType::where('name', 'Perkelahian')->first();
        $vtVandal = \App\Models\ViolationType::where('name', 'Vandalisme')->first();
        $vtNarkoba = \App\Models\ViolationType::where('name', 'Penyalahgunaan narkoba ringan')->first();

        $participants = [
            [
                'user' => [
                    'name' => 'Andi Pratama',
                    'nik' => '3374012505030001',
                    'role' => 'peserta',
                    'is_active' => true,
                    'email' => null,
                    'password' => null,
                ],
                'profile' => [
                    'assigned_admin_id' => $admin1->id,
                    'full_name' => 'Andi Pratama',
                    'nik' => '3374012505030001',
                    'address' => 'Jl. Pandanaran No. 45, Semarang Tengah',
                    'phone' => '081234567801',
                    'violation_type_id' => $vtBalap?->id,
                    'case_notes' => 'Tertangkap saat razia balap liar di Jl. Siliwangi. Kendaraan Honda Beat, nopol H-1234-AB. Pelanggaran pertama.',
                    'supervision_start' => '2026-04-01',
                    'supervision_end' => '2026-06-30',
                    'quota_type' => 'weekly',
                    'quota_amount' => 1,
                    'status' => 'active',
                    'location_id' => $locPolres?->id,
                ],
            ],
            [
                'user' => [
                    'name' => 'Rizky Maulana',
                    'nik' => '3374011208040002',
                    'role' => 'peserta',
                    'is_active' => true,
                    'email' => null,
                    'password' => null,
                ],
                'profile' => [
                    'assigned_admin_id' => $admin1->id,
                    'full_name' => 'Rizky Maulana',
                    'nik' => '3374011208040002',
                    'address' => 'Jl. Karanganyar Gunung RT 05/RW 02, Candisari',
                    'phone' => '082345678902',
                    'violation_type_id' => $vtRusuh?->id,
                    'case_notes' => 'Terlibat tawuran antar kelompok di daerah Karanganyar Gunung. Tidak ada korban luka berat. Wajib pembinaan rutin.',
                    'supervision_start' => '2026-04-01',
                    'supervision_end' => '2026-07-31',
                    'quota_type' => 'weekly',
                    'quota_amount' => 2,
                    'status' => 'active',
                    'location_id' => $locPolres?->id,
                ],
            ],
            [
                'user' => [
                    'name' => 'Dewi Safitri',
                    'nik' => '3374024703050003',
                    'role' => 'peserta',
                    'is_active' => true,
                    'email' => null,
                    'password' => null,
                ],
                'profile' => [
                    'assigned_admin_id' => $admin2->id,
                    'full_name' => 'Dewi Safitri',
                    'nik' => '3374024703050003',
                    'address' => 'Jl. Dr. Cipto No. 78, Semarang Timur',
                    'phone' => '085678901203',
                    'violation_type_id' => $vtKelahi?->id,
                    'case_notes' => 'Terlibat perkelahian di area publik (Mall Ciputra). Diselesaikan secara kekeluargaan. Wajib lapor sebagai bentuk pembinaan.',
                    'supervision_start' => '2026-03-15',
                    'supervision_end' => '2026-06-15',
                    'quota_type' => 'monthly',
                    'quota_amount' => 4,
                    'status' => 'active',
                    'location_id' => $locBalai?->id,
                ],
            ],
            [
                'user' => [
                    'name' => 'Fajar Nugroho',
                    'nik' => '3374011509020004',
                    'role' => 'peserta',
                    'is_active' => true,
                    'email' => null,
                    'password' => null,
                ],
                'profile' => [
                    'assigned_admin_id' => $admin2->id,
                    'full_name' => 'Fajar Nugroho',
                    'nik' => '3374011509020004',
                    'address' => 'Jl. Majapahit No. 112, Pedurungan',
                    'phone' => '087890123404',
                    'violation_type_id' => $vtVandal?->id,
                    'case_notes' => 'Tertangkap mencoret-coret fasilitas umum (halte BRT). Diwajibkan membersihkan dan menjalani program pembinaan.',
                    'supervision_start' => '2026-04-07',
                    'supervision_end' => '2026-07-07',
                    'quota_type' => 'weekly',
                    'quota_amount' => 1,
                    'status' => 'active',
                    'location_id' => $locBalai?->id,
                ],
            ],
            [
                'user' => [
                    'name' => 'Yoga Aditya',
                    'nik' => '3374012201060005',
                    'role' => 'peserta',
                    'is_active' => true,
                    'email' => null,
                    'password' => null,
                ],
                'profile' => [
                    'assigned_admin_id' => $admin1->id,
                    'full_name' => 'Yoga Aditya',
                    'nik' => '3374012201060005',
                    'address' => 'Jl. Sompok Baru No. 23, Lamper Tengah',
                    'phone' => '089012345605',
                    'violation_type_id' => $vtNarkoba?->id,
                    'case_notes' => 'Tertangkap memiliki ganja dalam jumlah kecil untuk pemakaian pribadi. Menjalani rehabilitasi sekaligus program wajib lapor.',
                    'supervision_start' => '2026-03-01',
                    'supervision_end' => '2026-08-31',
                    'quota_type' => 'monthly',
                    'quota_amount' => 4,
                    'status' => 'active',
                    'location_id' => $locPolres?->id,
                ],
            ],
        ];

        foreach ($participants as $data) {
            $user = User::updateOrCreate(
                ['nik' => $data['user']['nik']],
                $data['user']
            );

            $participant = Participant::updateOrCreate(
                ['user_id' => $user->id],
                array_merge($data['profile'], ['user_id' => $user->id])
            );

        }
    }
}
