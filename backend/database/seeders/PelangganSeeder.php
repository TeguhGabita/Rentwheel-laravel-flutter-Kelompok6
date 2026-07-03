<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pelanggan;

class PelangganSeeder extends Seeder
{
    public function run(): void
    {
        $pelanggans = [
            [
                'nama' => 'Budi Santoso',
                'no_ktp' => '3273010101900001',
                'no_hp' => '081234567890',
                'alamat' => 'Jl. Merdeka No. 10, Bandung',
            ],
            [
                'nama' => 'Siti Aminah',
                'no_ktp' => '3273010101900002',
                'no_hp' => '081234567891',
                'alamat' => 'Jl. Asia Afrika No. 25, Bandung',
            ],
            [
                'nama' => 'Andi Wijaya',
                'no_ktp' => '3273010101900003',
                'no_hp' => '081234567892',
                'alamat' => 'Jl. Dago No. 5, Bandung',
            ],
            [
                'nama' => 'Rina Kurniawati',
                'no_ktp' => '3273010101900004',
                'no_hp' => '081234567893',
                'alamat' => 'Jl. Cihampelas No. 88, Bandung',
            ],
            [
                'nama' => 'Doni Prasetyo',
                'no_ktp' => '3273010101900005',
                'no_hp' => '081234567894',
                'alamat' => 'Jl. Braga No. 15, Bandung',
            ],
        ];

        foreach ($pelanggans as $pelanggan) {
            Pelanggan::firstOrCreate(
                ['no_ktp' => $pelanggan['no_ktp']],
                $pelanggan
            );
        }
    }
}
