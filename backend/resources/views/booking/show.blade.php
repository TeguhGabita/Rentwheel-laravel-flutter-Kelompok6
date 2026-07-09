<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-neutral-800 leading-tight">
            Detail Booking
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-2xl border space-y-6">

                <div>
                    <h3 class="font-semibold text-xl">
                        Booking untuk {{ $booking->mobil->nama_mobil ?? '-' }}
                    </h3>
                    <p class="text-sm text-slate-500 mt-1">
                        {{ $booking->tanggal_mulai }} — {{ $booking->tanggal_selesai }}
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-slate-700">

                    <div class="space-y-2">
                        <div class="font-medium text-slate-900">Detail Mobil</div>
                        <div>Kategori: {{ $booking->mobil->kategori->nama ?? '-' }}</div>
                        <div>Plat Nomor: {{ $booking->mobil->plat_nomor }}</div>
                        <div>Merk: {{ $booking->mobil->merk }}</div>
                    </div>

                    <div class="space-y-2">
                        <div class="font-medium text-slate-900">Ringkasan Booking</div>
                        <div>Status Booking: {{ ucfirst($booking->status) }}</div>
                        <div>Total Harga: Rp{{ number_format($booking->total_harga, 0, ',', '.') }}</div>
                    </div>

                </div>

                <div class="flex flex-wrap gap-3 mt-4">

                    <a href="{{ route('booking.index') }}"
                        class="inline-flex items-center justify-center px-4 py-3 bg-slate-100 text-slate-700 rounded-2xl text-sm font-semibold hover:bg-slate-200 transition">
                        Kembali
                    </a>

                    <a href="{{ route('pembayaran.create', ['booking_id' => $booking->id]) }}"
                        class="inline-flex items-center justify-center px-4 py-3 bg-amber-500 text-white rounded-2xl text-sm font-semibold hover:bg-amber-600 transition">
                        Bayar
                    </a>

<form action="{{ route('booking.destroy', $booking->id) }}" method="POST">
    @csrf
    @method('DELETE')

    <button type="submit"
        class="inline-flex items-center justify-center px-4 py-3 bg-red-500 text-white rounded-2xl text-sm font-semibold hover:bg-red-600 transition"
        onclick="return confirm('Yakin ingin membatalkan booking ini?')">
        Batal Booking
    </button>
</form>

                </div>

            </div>
        </div>
    </div>
</x-app-layout>