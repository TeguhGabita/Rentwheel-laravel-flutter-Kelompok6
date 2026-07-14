<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Mobil;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Notifications\BookingStatusUpdatedNotification;
use App\Notifications\BookingBaruNotification;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Admin bisa lihat semua booking. User biasa (pelanggan) cuma bisa
        // lihat booking miliknya sendiri. Sebelumnya endpoint ini selalu
        // mengembalikan SEMUA booking dari SEMUA user tanpa filter, sehingga
        // data booking pelanggan lain bisa bocor kalau dipakai untuk halaman
        // "Riwayat Booking" milik pelanggan.
        //
        // 'pembayaran' ditambahkan di sini supaya status & jumlah pembayaran
        // ikut terkirim ke aplikasi (dipakai untuk hitung "Nunggu Bayar" di Beranda).
        $query = Booking::with(['mobil', 'user', 'pembayaran']);

        if (!$user->hasRole('admin')) {
            $query->where('user_id', $user->id);
        }

        $bookings = $query->orderBy('created_at', 'desc')->get();

        return response()->json($bookings);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobil_id' => 'required|exists:mobils,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }
        $mobil = Mobil::find($request->mobil_id);
        $jumlahHari = Carbon::parse($request->tanggal_mulai)
            ->diffInDays(Carbon::parse($request->tanggal_selesai)) + 1;

        $booking = Booking::create([
            'mobil_id' => $request->mobil_id,
            'user_id' => $request->user()->id, // user yang sedang login (pemesan)
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'total_harga' => $mobil->harga_sewa_per_hari * $jumlahHari,
            'status' => 'dipesan',
        ]);

        $booking->load(['mobil', 'user']);

        // Kirim notifikasi ke semua admin bahwa ada booking baru masuk
        $admins = User::role('admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new BookingBaruNotification($booking));
        }

        return response()->json([
            'message' => 'Booking berhasil dibuat',
            'booking' => $booking,
        ], 201);
    }

    public function show($id)
    {
        $booking = Booking::with(['mobil', 'user', 'pembayaran'])->find($id);
        if (!$booking) {
            return response()->json(['message' => 'Booking tidak ditemukan'], 404);
        }
        return response()->json($booking);
    }

    /**
     * PUT /booking/{id}/status
     * Ubah status booking (dipesan / berjalan / selesai / batal).
     * Dipakai oleh admin dari halaman Laporan.
     */
    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:dipesan,berjalan,selesai,batal',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        $booking = Booking::with('user')->find($id);

        if (!$booking) {
            return response()->json(['message' => 'Booking tidak ditemukan'], 404);
        }

        $booking->status = $request->status;
        $booking->save();

        // Kirim notifikasi ke user pemilik booking
        if ($booking->user) {
            $booking->user->notify(new BookingStatusUpdatedNotification($booking));
        }

        return response()->json([
            'success' => true,
            'message' => 'Status booking berhasil diubah',
            'data' => $booking->load(['mobil', 'user', 'pembayaran']),
        ], 200);
    }
}
