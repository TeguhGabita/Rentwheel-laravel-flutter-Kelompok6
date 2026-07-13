<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Booking;
use App\Models\Mobil;
use App\Models\User;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        $mobils = Mobil::all();
        // Ambil user dengan role 'user'
        $users = User::role('user')->get();
        $admin = User::role('admin')->first();

        if ($mobils->isEmpty() || $users->isEmpty()) {
            return;
        }

        $bookings = [
            [
                'mobil_id' => $mobils[0]->id,
                'user_id' => $users[0]->id ?? $admin->id,
                'tanggal_mulai' => '2026-07-01',
                'tanggal_selesai' => '2026-07-03',
                'total_harga' => $mobils[0]->harga_sewa_per_hari * 2,
                'status' => 'selesai',
            ],
            [
                'mobil_id' => $mobils[1]->id ?? $mobils[0]->id,
                'user_id' => $users[1]->id ?? $users[0]->id,
                'tanggal_mulai' => '2026-07-05',
                'tanggal_selesai' => '2026-07-07',
                'total_harga' => ($mobils[1]->harga_sewa_per_hari ?? $mobils[0]->harga_sewa_per_hari) * 2,
                'status' => 'berjalan',
            ],
            [
                'mobil_id' => $mobils[2]->id ?? $mobils[0]->id,
                'user_id' => $users[2]->id ?? $users[0]->id,
                'tanggal_mulai' => '2026-07-10',
                'tanggal_selesai' => '2026-07-12',
                'total_harga' => ($mobils[2]->harga_sewa_per_hari ?? $mobils[0]->harga_sewa_per_hari) * 2,
                'status' => 'dipesan',
            ],
            [
                'mobil_id' => $mobils[3]->id ?? $mobils[0]->id,
                'user_id' => $users[3]->id ?? $users[0]->id,
                'tanggal_mulai' => '2026-06-20',
                'tanggal_selesai' => '2026-06-22',
                'total_harga' => ($mobils[3]->harga_sewa_per_hari ?? $mobils[0]->harga_sewa_per_hari) * 2,
                'status' => 'batal',
            ],
        ];

        foreach ($bookings as $booking) {
            Booking::create($booking);
        }
    }
}
