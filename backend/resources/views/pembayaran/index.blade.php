<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-neutral-800 leading-tight">Riwayat Pembayaran</h2>
                <p class="text-sm text-neutral-500 mt-1">Pantau status pembayaran booking Anda.</p>
            </div>
            <a href="{{ route('booking.index') }}" class="rounded-xl border border-neutral-200 px-4 py-2 text-sm font-medium text-neutral-700 hover:bg-neutral-50">
                Kembali ke Booking
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

            @if ($pembayarans->count())
                <div class="space-y-4">
                    @foreach ($pembayarans as $pembayaran)
                        <div class="rounded-3xl border border-neutral-200 bg-white p-6 shadow-sm">
                            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                                <div>
                                    <p class="text-sm font-medium text-emerald-600">
                                        {{ $pembayaran->booking->mobil->nama_mobil ?? 'Mobil' }}
                                    </p>
                                    <h3 class="mt-1 text-lg font-semibold text-neutral-900">
                                        {{ $pembayaran->booking->mobil->merk ?? '-' }}
                                    </h3>
                                    <p class="mt-2 text-sm text-neutral-500">
                                        {{ optional($pembayaran->tanggal_bayar)->format('d M Y H:i') ?? '-' }}
                                    </p>
                                </div>

                                <div class="flex flex-col gap-3 md:items-end">
                                    <span class="inline-flex w-fit rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                                        {{ ucfirst($pembayaran->status_bayar ?? 'pending') }}
                                    </span>
                                    <p class="text-sm text-neutral-600">
                                        Jumlah: <span class="font-semibold text-neutral-900">Rp {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $pembayarans->links() }}
                </div>
            @else
                <div class="rounded-2xl border border-dashed border-neutral-300 bg-white p-10 text-center text-neutral-500">
                    Belum ada pembayaran yang tercatat.
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
