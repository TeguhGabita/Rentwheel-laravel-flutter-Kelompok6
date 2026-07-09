<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BookingRequest;
use App\Models\Booking;
use App\Models\Mobil;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $bookings = Booking::with(['mobil', 'user'])
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('mobil', fn ($m) => $m->where('nama_mobil', 'like', "%{$search}%"))
                        ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"));
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.booking.index', compact('bookings'));
    }

    public function create()
    {
        $mobils = Mobil::orderBy('nama_mobil')->get();
        $pelanggans = User::role('user')->orderBy('name')->get();

        return view('admin.booking.create', compact('mobils', 'pelanggans'));
    }

    public function store(BookingRequest $request)
    {
        $data = $request->validated();

        if ($this->isBentrok($data['mobil_id'], $data['tanggal_mulai'], $data['tanggal_selesai'])) {
            return back()->withInput()->withErrors([
                'tanggal_mulai' => 'Mobil ini sudah dibooking pada rentang tanggal tersebut.',
            ]);
        }

        $mobil = Mobil::findOrFail($data['mobil_id']);
        $jumlahHari = Carbon::parse($data['tanggal_mulai'])->diffInDays(Carbon::parse($data['tanggal_selesai'])) + 1;
        $data['total_harga'] = $mobil->harga_sewa_per_hari * $jumlahHari;

        Booking::create($data);

        return redirect()->route('admin.booking.index')->with('status', 'Booking berhasil ditambahkan.');
    }

    public function edit(Booking $booking)
    {
        $mobils = Mobil::orderBy('nama_mobil')->get();
        $pelanggans = User::role('user')->orderBy('name')->get();

        return view('admin.booking.edit', compact('booking', 'mobils', 'pelanggans'));
    }

    public function update(BookingRequest $request, Booking $booking)
    {
        $data = $request->validated();

        if ($this->isBentrok($data['mobil_id'], $data['tanggal_mulai'], $data['tanggal_selesai'], $booking->id)) {
            return back()->withInput()->withErrors([
                'tanggal_mulai' => 'Mobil ini sudah dibooking pada rentang tanggal tersebut.',
            ]);
        }

        $mobil = Mobil::findOrFail($data['mobil_id']);
        $jumlahHari = Carbon::parse($data['tanggal_mulai'])->diffInDays(Carbon::parse($data['tanggal_selesai'])) + 1;
        $data['total_harga'] = $mobil->harga_sewa_per_hari * $jumlahHari;

        $booking->update($data);

        return redirect()->route('admin.booking.index')->with('status', 'Booking berhasil diperbarui.');
    }

    public function destroy(Booking $booking)
    {
        $booking->delete();

        return redirect()->route('admin.booking.index')->with('status', 'Booking berhasil dihapus.');
    }

    private function isBentrok(int $mobilId, string $mulai, string $selesai, ?int $ignoreBookingId = null): bool
    {
        return Booking::where('mobil_id', $mobilId)
            ->where('status', '!=', 'batal')
            ->when($ignoreBookingId, fn ($q) => $q->where('id', '!=', $ignoreBookingId))
            ->where('tanggal_mulai', '<=', $selesai)
            ->where('tanggal_selesai', '>=', $mulai)
            ->exists();
    }
}
