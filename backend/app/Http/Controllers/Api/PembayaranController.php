<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PembayaranController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|exists:bookings,id',
            'metode_bayar' => 'required|string',
            'jumlah_bayar' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        $pembayaran = Pembayaran::create([
            'booking_id' => $request->booking_id,
            'tanggal_bayar' => now(),
            'metode_bayar' => $request->metode_bayar,
            'jumlah_bayar' => $request->jumlah_bayar,
            'status_bayar' => 'lunas',
        ]);

        Booking::where('id', $request->booking_id)->update(['status' => 'dikonfirmasi']);

        return response()->json([
            'message' => 'Pembayaran berhasil',
            'pembayaran' => $pembayaran,
        ], 201);
    }
}
