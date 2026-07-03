<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Seeder;


class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['City Car', 'MPV', 'SUV', 'Minibus'] as $nama) {
            Kategori::create(['nama_kategori' => $nama]);
        }
    }
}
