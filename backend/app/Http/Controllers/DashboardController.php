<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Booking;
use App\Models\Mobil;
use App\Models\Pembayaran;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // jika login admin
        if (Auth::user()->hasRole('admin')) {

            $totalMobil = Mobil::count();
            $totalUser = User::role('user')->count();
            $totalBooking = Booking::count();
            $totalPembayaran = Pembayaran::count();

            $bookingBerjalan = Booking::where('status','berjalan')->count();
            $bookingSelesai = Booking::where('status','selesai')->count();

            $pendapatan = Pembayaran::where('status_bayar', 'lunas')
            ->sum('jumlah_bayar');

            // ==============================
            // Data grafik pendapatan 6 bulan terakhir
            // ==============================
            $labelBulan = [];
            $dataPendapatan = [];

            for ($i = 5; $i >= 0; $i--) {
                $bulanIni = Carbon::now()->subMonths($i);

                $labelBulan[] = $bulanIni->translatedFormat('M Y'); // contoh: "Jul 2026"

                $total = Pembayaran::where('status_bayar', 'lunas')
                    ->whereYear('tanggal_bayar', $bulanIni->year)
                    ->whereMonth('tanggal_bayar', $bulanIni->month)
                    ->sum('jumlah_bayar');

                $dataPendapatan[] = (float) $total;
            }

            return view('admin.dashboard', compact(
                'totalMobil',
                'totalUser',
                'totalBooking',
                'totalPembayaran',
                'bookingBerjalan',
                'bookingSelesai',
                'pendapatan',
                'labelBulan',
                'dataPendapatan'
            ));
        }

        // jika login user biasa
        $userId = Auth::id();

        // Booking aktif = status masih berjalan / dipesan (belum selesai/batal)
        $bookingAktif = Booking::where('user_id', $userId)
            ->whereIn('status', ['dipesan', 'berjalan'])
            ->count();

        // Riwayat booking = semua booking milik user ini (total keseluruhan)
        $totalBooking = Booking::where('user_id', $userId)->count();

        // Menunggu pembayaran = booking yang belum ada data pembayarannya
        $menungguBayar = Booking::where('user_id', $userId)
            ->whereDoesntHave('pembayaran')
            ->count();

        return view('beranda.index', compact(
            'bookingAktif',
            'totalBooking',
            'menungguBayar'
        ));
    }
}
