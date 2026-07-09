<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Booking;
use App\Models\Mobil;
use App\Models\Pelanggan;
use App\Models\User;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        $mobils = Mobil::all();
        $pelanggans = User::all();
        $admin = User::role('admin')->first();

        $bookings = [
            [
                'mobil_id' => $mobils[0]->id,
                'pelanggan_id' => $pelanggans[0]->id,
                'user_id' => $admin->id,
                'tanggal_mulai' => '2026-07-01',
                'tanggal_selesai' => '2026-07-03',
                'total_harga' => $mobils[0]->harga_sewa_per_hari * 2,
                'status' => 'selesai',
            ],
            [
                'mobil_id' => $mobils[1]->id,
                'pelanggan_id' => $pelanggans[1]->id,
                'user_id' => $admin->id,
                'tanggal_mulai' => '2026-07-05',
                'tanggal_selesai' => '2026-07-07',
                'total_harga' => $mobils[1]->harga_sewa_per_hari * 2,
                'status' => 'berjalan',
            ],
            [
                'mobil_id' => $mobils[2]->id,
                'pelanggan_id' => $pelanggans[2]->id,
                'user_id' => $admin->id,
                'tanggal_mulai' => '2026-07-10',
                'tanggal_selesai' => '2026-07-12',
                'total_harga' => $mobils[2]->harga_sewa_per_hari * 2,
                'status' => 'dipesan',
            ],
            [
                'mobil_id' => $mobils[3]->id,
                'pelanggan_id' => $pelanggans[3]->id,
                'user_id' => $admin->id,
                'tanggal_mulai' => '2026-06-20',
                'tanggal_selesai' => '2026-06-22',
                'total_harga' => $mobils[3]->harga_sewa_per_hari * 2,
                'status' => 'batal',
            ],
        ];

        foreach ($bookings as $booking) {
            Booking::create($booking);
        }
    }
}
