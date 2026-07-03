<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Mobil;
use App\Models\Kategori;

class MobilSeeder extends Seeder
{
    public function run(): void
    {
        $cityCar = Kategori::where('nama_kategori', 'City Car')->first();
        $mpv = Kategori::where('nama_kategori', 'MPV')->first();
        $suv = Kategori::where('nama_kategori', 'SUV')->first();

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
    }
}
