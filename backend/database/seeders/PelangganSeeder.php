<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Catatan: tabel `pelanggans` sudah dihapus dari skema (lihat migration
 * 2026_07_04_170455_drop_pelanggans_table.php). Data "pelanggan" sekarang
 * disimpan langsung di tabel `users` dengan role `user`. Seeder ini
 * dipertahankan namanya (PelangganSeeder) agar tidak mengubah urutan
 * pemanggilan di DatabaseSeeder, tapi isinya membuat akun User dengan role user.
 */
class PelangganSeeder extends Seeder
{
    public function run(): void
    {
        $pelanggans = [
            [
                'name' => 'Budi Santoso',
                'email' => 'budi@rentwheel.test',
                'no_ktp' => '3273010101900001',
                'no_hp' => '081234567890',
                'alamat' => 'Jl. Merdeka No. 10, Bandung',
            ],
            [
                'name' => 'Siti Aminah',
                'email' => 'siti@rentwheel.test',
                'no_ktp' => '3273010101900002',
                'no_hp' => '081234567891',
                'alamat' => 'Jl. Asia Afrika No. 25, Bandung',
            ],
            [
                'name' => 'Andi Wijaya',
                'email' => 'andi@rentwheel.test',
                'no_ktp' => '3273010101900003',
                'no_hp' => '081234567892',
                'alamat' => 'Jl. Dago No. 5, Bandung',
            ],
            [
                'name' => 'Rina Kurniawati',
                'email' => 'rina@rentwheel.test',
                'no_ktp' => '3273010101900004',
                'no_hp' => '081234567893',
                'alamat' => 'Jl. Cihampelas No. 88, Bandung',
            ],
            [
                'name' => 'Doni Prasetyo',
                'email' => 'doni@rentwheel.test',
                'no_ktp' => '3273010101900005',
                'no_hp' => '081234567894',
                'alamat' => 'Jl. Braga No. 15, Bandung',
            ],
        ];

        foreach ($pelanggans as $pelanggan) {
            $user = User::firstOrCreate(
                ['email' => $pelanggan['email']],
                [
                    'name' => $pelanggan['name'],
                    'password' => Hash::make('password'),
                    'no_ktp' => $pelanggan['no_ktp'],
                    'no_hp' => $pelanggan['no_hp'],
                    'alamat' => $pelanggan['alamat'],
                    'email_verified_at' => now(),
                ]
            );

            if (! $user->hasRole('user')) {
                $user->assignRole('user');
            }
        }
    }
}
