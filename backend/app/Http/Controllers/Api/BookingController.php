<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Mobil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $bookings = Booking::with(['mobil', 'pelanggan'])->get();

        return response()->json($bookings);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobil_id' => 'required|exists:mobils,id',
            'pelanggan_id' => 'required|exists:pelanggans,id',
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
            'pelanggan_id' => $request->pelanggan_id,
            'user_id' => $request->user()->id, // admin yang sedang login
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'total_harga' => $mobil->harga_sewa_per_hari * $jumlahHari,
            'status' => 'dipesan',
        ]);

        return response()->json([
            'message' => 'Booking berhasil dibuat',
            'booking' => $booking->load(['mobil', 'pelanggan']),
        ], 201);
    }

    public function show($id)
    {
        $booking = Booking::with(['mobil', 'pelanggan', 'pembayaran'])->find($id);

        if (!$booking) {
            return response()->json(['message' => 'Booking tidak ditemukan'], 404);
        }

        return response()->json($booking);
    }
}
