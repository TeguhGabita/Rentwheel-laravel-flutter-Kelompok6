<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Mobil;
use App\Models\KategoriMobil;
use Faker\Factory as FakerFactory;

class MobilSeeder extends Seeder
{
    public function run(): void
    {
        $cityCar = KategoriMobil::where('nama_kategori', 'City Car')->first();
        $mpv = KategoriMobil::where('nama_kategori', 'MPV')->first();
        $suv = KategoriMobil::where('nama_kategori', 'SUV')->first();

        $mobils = [
            [
                'kategori_id' => $cityCar->id,
                'nama_mobil' => 'Brio',
                'merk' => 'Honda',
                'plat_nomor' => 'D 1234 ABC',
                'harga_sewa_per_hari' => 250000,
                'status' => 'tersedia',
            ],
            [
                'kategori_id' => $mpv->id,
                'nama_mobil' => 'Avanza',
                'merk' => 'Toyota',
                'plat_nomor' => 'D 5678 DEF',
                'harga_sewa_per_hari' => 300000,
                'status' => 'tersedia',
            ],
            [
                'kategori_id' => $mpv->id,
                'nama_mobil' => 'Xenia',
                'merk' => 'Daihatsu',
                'plat_nomor' => 'D 9012 GHI',
                'harga_sewa_per_hari' => 280000,
                'status' => 'disewa',
            ],
            [
                'kategori_id' => $suv->id,
                'nama_mobil' => 'Fortuner',
                'merk' => 'Toyota',
                'plat_nomor' => 'D 3456 JKL',
                'harga_sewa_per_hari' => 800000,
                'status' => 'tersedia',
            ],
            [
                'kategori_id' => $suv->id,
                'nama_mobil' => 'Pajero Sport',
                'merk' => 'Mitsubishi',
                'plat_nomor' => 'D 7890 MNO',
                'harga_sewa_per_hari' => 750000,
                'status' => 'servis',
            ],
        ];

        foreach ($mobils as $mobil) {
            Mobil::firstOrCreate(
                ['plat_nomor' => $mobil['plat_nomor']],
                $mobil
            );
        }

        // ================================================
        // Tambahan: generate 30 data mobil dummy untuk tes pagination
        // ================================================
        $faker = FakerFactory::create('id_ID');

        $kategoriList = collect([$cityCar, $mpv, $suv])->filter(); // buang null kalau ada kategori tidak ketemu

        if ($kategoriList->isEmpty()) {
            $this->command->warn('Kategori City Car / MPV / SUV tidak ditemukan. Lewati generate data dummy.');
            return;
        }

        $merkMobil = [
            'Honda' => ['Brio', 'Mobilio', 'HR-V', 'CR-V', 'Civic', 'Jazz'],
            'Toyota' => ['Avanza', 'Innova', 'Fortuner', 'Rush', 'Yaris', 'Calya'],
            'Daihatsu' => ['Xenia', 'Terios', 'Ayla', 'Sigra', 'Rocky'],
            'Suzuki' => ['Ertiga', 'XL7', 'Baleno', 'Ignis'],
            'Mitsubishi' => ['Xpander', 'Pajero Sport', 'Outlander'],
            'Nissan' => ['Livina', 'Terra', 'Magnite'],
            'Hyundai' => ['Stargazer', 'Creta', 'Palisade'],
        ];

        $statusList = ['tersedia', 'disewa', 'servis'];

        for ($i = 1; $i <= 30; $i++) {
            $merk = $faker->randomElement(array_keys($merkMobil));
            $nama = $faker->randomElement($merkMobil[$merk]);
            $kategori = $kategoriList->random();

            $platNomor = strtoupper('D ' . $faker->numberBetween(1000, 9999) . ' ' . $faker->lexify('???'));

            // pastikan plat nomor unik, hindari duplikat kalau seeder dijalankan ulang
            Mobil::firstOrCreate(
                ['plat_nomor' => $platNomor],
                [
                    'kategori_id' => $kategori->id,
                    'nama_mobil' => $nama,
                    'merk' => $merk,
                    'plat_nomor' => $platNomor,
                    'harga_sewa_per_hari' => $faker->randomElement([150000, 200000, 250000, 300000, 400000, 500000, 600000, 750000, 800000]),
                    'status' => $faker->randomElement($statusList),
                ]
            );
        }

        $this->command->info('30 data mobil dummy berhasil ditambahkan.');
    }
}
