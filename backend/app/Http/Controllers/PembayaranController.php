<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
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

        return view('pembayaran.index', compact('pembayarans'));
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
            'metode_bayar' => 'required|string',
            'jumlah_bayar' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        Pembayaran::create([
            'booking_id' => $request->booking_id,
            'tanggal_bayar' => now(),
            'metode_bayar' => $request->metode_bayar,
            'jumlah_bayar' => $request->jumlah_bayar,
            'status_bayar' => 'lunas',
        ]);

        Booking::where('id', $request->booking_id)->update(['status' => 'berjalan']);

        return redirect()->route('pembayaran.index')->with('status', 'Pembayaran berhasil dicatat.');
    }
}
