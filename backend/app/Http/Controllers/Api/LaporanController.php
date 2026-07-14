<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Pembayaran;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $bookings = Booking::with(['mobil', 'user', 'pembayaran'])
            ->orderBy('created_at', 'desc')
            ->get();

        $transaksi = $bookings->map(function ($booking) {
            $pembayaran = $booking->pembayaran;

            return [
                'id' => $booking->id,
                'booking_id' => $booking->id,
                'nama_mobil' => $booking->mobil->nama ?? $booking->mobil->nama_mobil ?? '-',
                'nama_pelanggan' => $booking->user->name ?? '-',
                'tanggal' => $booking->created_at?->format('Y-m-d'),
                'status' => $booking->status ?? '-',
                'total_bayar' => $pembayaran?->jumlah_bayar ?? 0,
                'pembayaran_id' => $pembayaran?->id,
                'status_bayar' => $pembayaran?->status_bayar ?? 'pending',
            ];
        });

        $totalPendapatan = Pembayaran::where('status_bayar', 'lunas')->sum('jumlah_bayar');
        $totalBooking = Booking::count();

        return response()->json([
            'success' => true,
            'data' => [
                'total_pendapatan' => $totalPendapatan,
                'total_booking' => $totalBooking,
                'transaksi' => $transaksi,
            ],
        ]);
    }
}
