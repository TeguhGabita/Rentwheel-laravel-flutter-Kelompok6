<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Pembayaran;
use App\Models\User;
use App\Notifications\PembayaranBaruNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PembayaranController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $pembayarans = $user->hasRole('admin')
            ? Pembayaran::with('booking.mobil')->latest()->paginate(10)
            : Pembayaran::whereHas('booking', fn ($q) => $q->where('user_id', $user->id))
                ->with('booking.mobil')->latest()->paginate(10);

        // Booking milik user yang belum ada pembayarannya (menunggu dibayar)
        $bookingBelumBayar = $user->hasRole('admin')
            ? collect()
            : Booking::where('user_id', $user->id)
                ->whereDoesntHave('pembayaran')
                ->with('mobil')
                ->latest()
                ->get();

        return view('pembayaran.index', compact('pembayarans', 'bookingBelumBayar'));
    }

    public function create(Request $request)
    {
        $bookingId = $request->query('booking_id');
        $booking = $bookingId ? Booking::with('mobil')->find($bookingId) : null;

        return view('pembayaran.create', compact('booking'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|exists:bookings,id',
            'metode_bayar' => 'required|in:transfer_bank,e_wallet,qris',
            'jumlah_bayar' => 'required|numeric|min:1',
            'bukti_pembayaran' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ], [
            'metode_bayar.in' => 'Pilih metode pembayaran yang tersedia.',
            'bukti_pembayaran.required' => 'Unggah bukti pembayaran terlebih dahulu.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $buktiPath = $request->file('bukti_pembayaran')->store('bukti-pembayaran', 'public');

        $pembayaran = Pembayaran::create([
            'booking_id' => $request->booking_id,
            'tanggal_bayar' => now(),
            'metode_bayar' => $request->metode_bayar,
            'jumlah_bayar' => $request->jumlah_bayar,
            'status_bayar' => 'pending',
            'bukti_pembayaran' => $buktiPath,
        ]);

        // Kirim notifikasi ke semua admin bahwa ada pembayaran baru yang perlu diverifikasi
        $pembayaran->load('booking.mobil', 'booking.user');
        $admins = User::all()->filter(fn ($u) => $u->hasRole('admin'));
        Notification::send($admins, new PembayaranBaruNotification($pembayaran));

        return redirect()->route('pembayaran.index')
            ->with('status', 'Bukti pembayaran berhasil dikirim. Menunggu verifikasi admin.');
    }
}
