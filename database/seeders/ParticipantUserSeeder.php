<?php

namespace Database\Seeders;

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
     */
    public function run(): void
    {
        // Fetch admin users to assign as supervisors
        $admin1 = User::where('email', 'budi.santoso@polrestabes-smg.test')->first();
        $admin2 = User::where('email', 'siti.rahayu@polrestabes-smg.test')->first();

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
                    'violation_type' => 'Balap liar',
                    'case_notes' => 'Tertangkap saat razia balap liar di Jl. Siliwangi. Kendaraan Honda Beat, nopol H-1234-AB. Pelanggaran pertama.',
                    'supervision_start' => '2026-04-01',
                    'supervision_end' => '2026-06-30',
                    'quota_type' => 'weekly',
                    'quota_amount' => 1,
                    'status' => 'active',
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
                    'violation_type' => 'Kerusuhan / ketertiban umum',
                    'case_notes' => 'Terlibat tawuran antar kelompok di daerah Karanganyar Gunung. Tidak ada korban luka berat. Wajib pembinaan rutin.',
                    'supervision_start' => '2026-04-01',
                    'supervision_end' => '2026-07-31',
                    'quota_type' => 'weekly',
                    'quota_amount' => 2,
                    'status' => 'active',
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
                    'violation_type' => 'Perkelahian',
                    'case_notes' => 'Terlibat perkelahian di area publik (Mall Ciputra). Diselesaikan secara kekeluargaan. Wajib lapor sebagai bentuk pembinaan.',
                    'supervision_start' => '2026-03-15',
                    'supervision_end' => '2026-06-15',
                    'quota_type' => 'monthly',
                    'quota_amount' => 4,
                    'status' => 'active',
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
                    'violation_type' => 'Vandalisme',
                    'case_notes' => 'Tertangkap mencoret-coret fasilitas umum (halte BRT). Diwajibkan membersihkan dan menjalani program pembinaan.',
                    'supervision_start' => '2026-04-07',
                    'supervision_end' => '2026-07-07',
                    'quota_type' => 'weekly',
                    'quota_amount' => 1,
                    'status' => 'active',
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
                    'violation_type' => 'Penyalahgunaan narkoba ringan',
                    'case_notes' => 'Tertangkap memiliki ganja dalam jumlah kecil untuk pemakaian pribadi. Menjalani rehabilitasi sekaligus program wajib lapor.',
                    'supervision_start' => '2026-03-01',
                    'supervision_end' => '2026-08-31',
                    'quota_type' => 'monthly',
                    'quota_amount' => 4,
                    'status' => 'active',
                ],
            ],
        ];

        foreach ($participants as $data) {
            $user = User::updateOrCreate(
                ['nik' => $data['user']['nik']],
                $data['user']
            );

            Participant::updateOrCreate(
                ['user_id' => $user->id],
                array_merge($data['profile'], ['user_id' => $user->id])
            );
        }
    }
}
