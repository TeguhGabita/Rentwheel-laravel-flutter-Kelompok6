<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PembayaranRequest;
use App\Models\Booking;
use App\Models\Pembayaran;
use Illuminate\Http\Request;

class PembayaranController extends Controller
{
    public function index(Request $request)
    {
        $pembayarans = Pembayaran::with('booking.mobil', 'booking.user')
            ->when($request->search, function ($query, $search) {
                $query->whereHas('booking.mobil', fn ($m) => $m->where('nama_mobil', 'like', "%{$search}%"))
                    ->orWhere('metode_bayar', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.pembayaran.index', compact('pembayarans'));
    }

    public function create()
    {
        $bookings = Booking::with('mobil', 'user')->orderByDesc('id')->get();

        return view('admin.pembayaran.create', compact('bookings'));
    }

    public function store(PembayaranRequest $request)
    {
        Pembayaran::create($request->validated());

        return redirect()->route('admin.pembayaran.index')->with('status', 'Pembayaran berhasil dicatat.');
    }

    public function edit(Pembayaran $pembayaran)
    {
        $bookings = Booking::with('mobil', 'user')->orderByDesc('id')->get();

        return view('admin.pembayaran.edit', compact('pembayaran', 'bookings'));
    }

    public function update(PembayaranRequest $request, Pembayaran $pembayaran)
    {
        $pembayaran->update($request->validated());

        return redirect()->route('admin.pembayaran.index')->with('status', 'Pembayaran berhasil diperbarui.');
    }

    public function destroy(Pembayaran $pembayaran)
    {
        $pembayaran->delete();

        return redirect()->route('admin.pembayaran.index')->with('status', 'Pembayaran berhasil dihapus.');
    }
}
