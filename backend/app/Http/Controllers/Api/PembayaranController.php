<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Pembayaran;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Notifications\PembayaranStatusUpdatedNotification;
use App\Notifications\PembayaranBaruNotification;

class PembayaranController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_id'       => 'required|exists:bookings,id',
            'metode_bayar'     => 'required|string|max:100',
            'jumlah_bayar'     => 'required|numeric|min:0',
            'bukti_pembayaran' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $booking = Booking::find($request->booking_id);

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking tidak ditemukan.'
            ], 404);
        }

        if (Pembayaran::where('booking_id', $booking->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Booking ini sudah memiliki pembayaran.'
            ], 409);
        }

        $buktiPath = null;
        if ($request->hasFile('bukti_pembayaran')) {
            $buktiPath = $request->file('bukti_pembayaran')->store('bukti-pembayaran', 'public');
        }

        $pembayaran = Pembayaran::create([
            'booking_id'        => $booking->id,
            'tanggal_bayar'     => now(),
            'metode_bayar'      => $request->metode_bayar,
            'jumlah_bayar'      => $request->jumlah_bayar,
            'status_bayar'      => 'pending',
            'bukti_pembayaran'  => $buktiPath,
        ]);

        $pembayaran->load('booking.user', 'booking.mobil');

        // Kirim notifikasi ke semua admin bahwa ada pembayaran baru masuk
        $admins = User::role('admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new PembayaranBaruNotification($pembayaran));
        }

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran berhasil dikirim, menunggu konfirmasi admin.',
            'data'    => $pembayaran,
        ], 201);
    }

    public function index(Request $request)
    {
        $user = $request->user();

        $query = Pembayaran::with(['booking.mobil', 'booking.user']);

        if (!$user->hasRole('admin')) {
            $query->whereHas('booking', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        $pembayaran = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $pembayaran,
        ]);
    }

    public function show($id)
    {
        $pembayaran = Pembayaran::with(['booking.mobil', 'booking.user'])->find($id);

        if (!$pembayaran) {
            return response()->json([
                'success' => false,
                'message' => 'Pembayaran tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $pembayaran,
        ]);
    }

    /**
     * PUT /pembayaran/{id}/status
     * Admin acc/tolak pembayaran: pending -> lunas / ditolak.
     * Setelah status berubah, user pemilik booking (via relasi booking->user)
     * dikirimi notifikasi database supaya muncul di halaman notifikasi Flutter.
     */
    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status_bayar' => 'required|in:pending,lunas,ditolak',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors(),
            ], 422);
        }

        // eager load booking.user supaya bisa langsung notify tanpa query tambahan
        $pembayaran = Pembayaran::with('booking.user')->find($id);

        if (!$pembayaran) {
            return response()->json([
                'success' => false,
                'message' => 'Pembayaran tidak ditemukan.'
            ], 404);
        }

        $pembayaran->status_bayar = $request->status_bayar;
        $pembayaran->save();

        // Kirim notifikasi ke user pemilik booking terkait pembayaran ini
        $user = $pembayaran->booking->user ?? null;

        if ($user) {
            $user->notify(new PembayaranStatusUpdatedNotification($pembayaran));
        }

        return response()->json([
            'success' => true,
            'message' => 'Status pembayaran berhasil diubah.',
            'data'    => $pembayaran->load(['booking.mobil', 'booking.user']),
        ]);
    }
}
