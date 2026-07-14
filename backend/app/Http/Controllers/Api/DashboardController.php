<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Mobil;
use App\Models\Pembayaran;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        return response()->json([
            'total_mobil' => Mobil::count(),
            'total_user' => User::count(),
            'total_booking' => Booking::count(),
            'total_pembayaran' => Pembayaran::count(),
            'booking_berjalan' => Booking::where('status', 'berjalan')->count(),
            'booking_selesai' => Booking::where('status', 'selesai')->count(),
            // Setiap record pembayaran dianggap sebagai uang masuk,
            // jadi langsung dijumlahkan tanpa filter status_bayar.
            'pendapatan' => Pembayaran::sum('jumlah_bayar'),
        ]);
    }
}
