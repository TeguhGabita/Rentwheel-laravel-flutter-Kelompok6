<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Pembayaran;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    /**
     * Menampilkan halaman laporan booking.
     * Berisi Mobil, Harga, Tanggal Mulai, Tanggal Selesai, dan Status.
     */
    public function index(Request $request)
    {
        $bookings = Booking::with(['mobil', 'user'])
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('mobil', fn ($m) => $m->where('nama_mobil', 'like', "%{$search}%"))
                        ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.Laporan.index', compact('bookings'));
    }

    /**
     * Memproses pemilihan data booking untuk dicetak.
     */
    public function cetak(Request $request)
    {
        $request->validate([
            'booking_ids' => 'required|array|min:1',
            'booking_ids.*' => 'exists:bookings,id',
        ], [
            'booking_ids.required' => 'Pilih minimal satu booking untuk dicetak.',
        ]);

        $bookingIds = $request->input('booking_ids', []);

        $bookings = Booking::with(['mobil', 'user'])
            ->whereIn('id', $bookingIds)
            ->latest()
            ->get();

        $totalHarga = $bookings->sum('total_harga');

        return view('admin.Laporan.cetak', compact('bookings', 'totalHarga'));
    }

    /**
     * Update status booking.
     */
    public function updateStatus(Request $request, Booking $booking)
    {
        $request->validate([
            'status' => 'required|in:dipesan,berjalan,selesai,batal',
        ]);

        $booking->update([
            'status' => $request->status,
        ]);

        return back()->with('status', 'Status booking berhasil diperbarui.');
    }

    /**
     * Menampilkan laporan pembayaran.
     */
    public function pembayaran(Request $request)
    {
        $pembayarans = Pembayaran::with(['booking.mobil', 'booking.user'])
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('booking.mobil', fn ($m) => $m->where('nama_mobil', 'like', "%{$search}%"))
                        ->orWhereHas('booking.user', fn ($u) => $u->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.Laporan.pembayaran', compact('pembayarans'));
    }
}
