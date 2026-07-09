<?php

namespace Database\Seeders;

use App\Models\KategoriMobil;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['City Car', 'MPV', 'SUV', 'Minibus'] as $nama) {
            KategoriMobil::firstOrCreate(['nama_kategori' => $nama]);
        }
    }
}
