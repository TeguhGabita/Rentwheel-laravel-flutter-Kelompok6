<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Mobil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $bookings = $user->hasRole('admin')
            ? Booking::with(['mobil', 'user'])->latest()->paginate(10)
            : Booking::with('mobil')->where('user_id', $user->id)->latest()->paginate(10);

        return view('booking.index', compact('bookings'));
    }

    public function create(Request $request)
    {
        $mobilId = $request->query('mobil_id');
        $mobil = $mobilId ? Mobil::find($mobilId) : null;

        return view('booking.create', compact('mobil'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobil_id' => 'required|exists:mobils,id',
            'tanggal_mulai' => 'required|date|after_or_equal:today',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $mobil = Mobil::findOrFail($request->mobil_id);
        $jumlahHari = Carbon::parse($request->tanggal_mulai)
            ->diffInDays(Carbon::parse($request->tanggal_selesai)) + 1;

        Booking::create([
            'mobil_id' => $mobil->id,
            'user_id' => $request->user()->id,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'total_harga' => $mobil->harga_sewa_per_hari * $jumlahHari,
            'status' => 'dipesan',
        ]);

        return redirect()->route('booking.index')->with('status', 'Booking berhasil dibuat.');
    }

    public function show(Booking $booking)
    {
        $this->authorizeBooking($booking);

        $booking->load(['mobil', 'pembayaran']);

        return view('booking.show', compact('booking'));
    }

    private function authorizeBooking(Booking $booking)
    {
        if (! auth()->user()->hasRole('admin') && $booking->user_id !== auth()->id()) {
            abort(403);
        }
    }
}
