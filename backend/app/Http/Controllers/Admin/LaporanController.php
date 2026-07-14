<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Notifications\BookingDikonfirmasiNotification;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    /**
     * Menampilkan laporan booking beserta pembayaran.
     */
    public function index(Request $request)
    {
        $bookings = Booking::with([
                'mobil',
                'user',
                'pembayaran'
            ])
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('mobil', function ($m) use ($search) {
                        $m->where('nama_mobil', 'like', "%{$search}%");
                    })
                    ->orWhereHas('user', function ($u) use ($search) {
                        $u->where('name', 'like', "%{$search}%");
                    });
                });
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->tanggal_dari, function ($query, $tanggalDari) {
                $query->whereDate('tanggal_mulai', '>=', $tanggalDari);
            })
            ->when($request->tanggal_sampai, function ($query, $tanggalSampai) {
                $query->whereDate('tanggal_mulai', '<=', $tanggalSampai);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.Laporan.index', compact('bookings'));
    }

    /**
     * Cetak laporan booking beserta pembayaran.
     */
    public function cetak(Request $request)
    {
        $request->validate([
            'booking_ids' => 'required|array|min:1',
            'booking_ids.*' => 'exists:bookings,id',
        ], [
            'booking_ids.required' => 'Pilih minimal satu booking.',
        ]);

        $bookings = Booking::with([
                'mobil',
                'user',
                'pembayaran'
            ])
            ->whereIn('id', $request->booking_ids)
            ->latest()
            ->get();

        $totalHarga = $bookings->sum('total_harga');

        $totalPembayaran = $bookings->sum(function ($booking) {
            return optional($booking->pembayaran)->jumlah_bayar ?? 0;
        });

        return view('admin.Laporan.cetak', compact(
            'bookings',
            'totalHarga',
            'totalPembayaran'
        ));
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

        // Kirim notifikasi ke user pemilik booking
        $booking->user->notify(new BookingDikonfirmasiNotification($booking));

        return back()->with('status', 'Status booking berhasil diperbarui.');
    }

    /**
     * Update status pembayaran (pending / lunas / gagal) milik sebuah booking.
     * Ini yang membuat angka "Pendapatan" di Dashboard ikut berubah,
     * karena Dashboard menjumlahkan Pembayaran dengan status_bayar = 'lunas'.
     */
    public function updateStatusBayar(Request $request, Booking $booking)
    {
        $request->validate([
            'status_bayar' => 'required|in:pending,lunas,gagal',
        ]);

        if (!$booking->pembayaran) {
            return back()->with('status', 'Booking ini belum punya data pembayaran.');
        }

        $booking->pembayaran->update([
            'status_bayar' => $request->status_bayar,
        ]);

        return back()->with('status', 'Status pembayaran berhasil diperbarui.');
    }
}
