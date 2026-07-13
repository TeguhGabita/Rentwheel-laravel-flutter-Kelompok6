<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-neutral-800 leading-tight">Riwayat Booking</h2>
                <p class="text-sm text-neutral-500 mt-1">Lihat daftar pemesanan mobil Anda.</p>
            </div>
            <a href="{{ route('mobil.index') }}" class="rounded-xl border border-neutral-200 px-4 py-2 text-sm font-medium text-neutral-700 hover:bg-neutral-50">
                Cari Mobil
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-6 rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                    {{ session('status') }}
                </div>
            @endif

            @if ($bookings->count())
                <div class="space-y-4">
                    @foreach ($bookings as $booking)
                        <div class="rounded-3xl border border-neutral-200 bg-white p-6 shadow-sm">
                            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                                <div>
                                    <p class="text-sm font-medium text-amber-600">{{ $booking->mobil->nama_mobil ?? '-' }}</p>
                                    <h3 class="mt-1 text-lg font-semibold text-neutral-900">{{ $booking->mobil->merk ?? '-' }}</h3>
                                    <p class="mt-2 text-sm text-neutral-500">
                                        {{ \Carbon\Carbon::parse($booking->tanggal_mulai)->format('d M Y') }} - {{ \Carbon\Carbon::parse($booking->tanggal_selesai)->format('d M Y') }}
                                    </p>
                                </div>

                                <div class="flex flex-col gap-3 md:items-end">
                                    <span class="inline-flex w-fit rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                    <p class="text-sm text-neutral-600">Total: <span class="font-semibold text-neutral-900">Rp {{ number_format($booking->total_harga, 0, ',', '.') }}</span></p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $bookings->links() }}
                </div>
            @else
                <div class="rounded-2xl border border-dashed border-neutral-300 bg-white p-10 text-center text-neutral-500">
                    Belum ada booking yang dibuat.
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
