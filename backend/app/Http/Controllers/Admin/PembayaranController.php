<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PembayaranRequest;
use App\Models\Booking;
use App\Models\Pembayaran;
use Illuminate\Http\Request;

class PembayaranController extends Controller
{
    /**
     * Daftar pembayaran.
     */
    public function index(Request $request)
    {
        $pembayarans = Pembayaran::with([
                'booking.mobil',
                'booking.user'
            ])
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('booking.mobil', function ($m) use ($search) {
                        $m->where('nama_mobil', 'like', "%{$search}%");
                    })
                    ->orWhereHas('booking.user', function ($u) use ($search) {
                        $u->where('name', 'like', "%{$search}%");
                    })
                    ->orWhere('metode_bayar', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.pembayaran.index', compact('pembayarans'));
    }

    /**
     * Form tambah pembayaran.
     */
    public function create()
    {
        // Hanya booking yang belum memiliki pembayaran
        $bookings = Booking::with(['mobil', 'user'])
            ->whereDoesntHave('pembayaran')
            ->latest()
            ->get();

        return view('admin.pembayaran.create', compact('bookings'));
    }

    /**
     * Simpan pembayaran.
     */
    public function store(PembayaranRequest $request)
{
    Pembayaran::create($request->validated());

    return redirect()
        ->route('admin.pembayaran.index')
        ->with('status','Pembayaran berhasil ditambahkan.');
}


    /**
     * Form edit pembayaran.
     */
    public function edit(Pembayaran $pembayaran)
    {
        $bookings = Booking::with(['mobil', 'user'])
            ->latest()
            ->get();

        return view('admin.pembayaran.edit', compact('pembayaran', 'bookings'));
    }

    /**
     * Update pembayaran.
     */
   public function update(PembayaranRequest $request, Pembayaran $pembayaran)
{
    $pembayaran->update($request->validated());

    if ($pembayaran->status_bayar == 'lunas') {

        $pembayaran->booking()->update([
            'status' => 'dipesan'
        ]);

    } elseif ($pembayaran->status_bayar == 'gagal') {

        $pembayaran->booking()->update([
            'status' => 'batal'
        ]);
    }

    return redirect()
        ->route('admin.pembayaran.index')
        ->with('status','Status pembayaran berhasil diperbarui.');
}

    /**
     * Hapus pembayaran.
     */
    public function destroy(Pembayaran $pembayaran)
    {
        // Booking kembali menjadi dipesan
        if ($pembayaran->booking) {
            $pembayaran->booking->update([
                'status' => 'dipesan'
            ]);
        }

        $pembayaran->delete();

        return redirect()
            ->route('admin.pembayaran.index')
            ->with('status', 'Pembayaran berhasil dihapus.');
    }
}
