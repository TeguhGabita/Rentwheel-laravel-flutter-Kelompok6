<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-neutral-800 leading-tight">Riwayat Pembayaran</h2>
                <p class="text-sm text-neutral-500 mt-1">Pantau status pembayaran booking Anda.</p>
            </div>
            <a href="{{ route('booking.index') }}" class="rounded-xl border border-neutral-200 px-4 py-2 text-sm font-medium text-neutral-700 hover:bg-neutral-50 transition">
                Kembali ke Booking
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            @if (session('status'))
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif

            {{-- ============ HERO / BANNER ============ --}}
            <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-amber-500 to-orange-500 px-8 py-10 shadow-lg">
                <div class="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-white/10"></div>
                <div class="absolute -right-24 -bottom-24 h-56 w-56 rounded-full bg-white/10"></div>

                <div class="relative flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-white/80">
                            Pembayaran &middot; {{ $pembayarans->total() ?? $pembayarans->count() }} Transaksi
                        </p>
                        <h1 class="mt-2 text-3xl font-bold text-white">Riwayat Pembayaran</h1>
                        <p class="mt-1 text-sm text-white/90">Lihat status dan detail setiap pembayaran booking Anda.</p>
                    </div>
                    <a href="{{ route('booking.index') }}"
                       class="inline-flex w-fit items-center justify-center rounded-xl bg-white px-5 py-2.5 text-sm font-semibold text-amber-600 shadow-sm hover:bg-neutral-50 transition">
                        Cari Mobil
                    </a>
                </div>
            </div>

            {{-- ============ MENUNGGU PEMBAYARAN ============ --}}
            @if (isset($bookingBelumBayar) && $bookingBelumBayar->count())
                <div>
                    <h3 class="mb-3 flex items-center gap-2 text-sm font-medium text-neutral-500">
                        <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                        Menunggu Pembayaran ({{ $bookingBelumBayar->count() }})
                    </h3>

                    <div class="space-y-4">
                        @foreach ($bookingBelumBayar as $booking)
                            <div class="group relative rounded-3xl border border-amber-200 bg-amber-50/40 p-6 shadow-sm transition hover:shadow-md">
                                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">

                                    <div class="flex items-start gap-4">
                                        <div class="hidden h-12 w-12 flex-shrink-0 items-center justify-center rounded-2xl bg-amber-100 text-amber-600 md:flex">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-amber-600">
                                                {{ $booking->mobil->nama_mobil ?? 'Mobil' }}
                                            </p>
                                            <h3 class="mt-1 text-lg font-semibold text-neutral-900">
                                                {{ $booking->mobil->merk ?? '-' }}
                                            </h3>
                                            <p class="mt-2 flex items-center gap-1.5 text-sm text-neutral-500">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                {{ \Carbon\Carbon::parse($booking->tanggal_mulai)->format('d M Y') }}
                                                &mdash;
                                                {{ \Carbon\Carbon::parse($booking->tanggal_selesai)->format('d M Y') }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex flex-col gap-3 md:items-end">
                                        <span class="inline-flex w-fit items-center gap-1.5 rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                                            <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                            Menunggu Pembayaran
                                        </span>
                                        <p class="text-sm text-neutral-600">
                                            Total: <span class="font-semibold text-neutral-900">Rp {{ number_format($booking->total_harga, 0, ',', '.') }}</span>
                                        </p>
                                        <a href="{{ route('pembayaran.create', ['booking_id' => $booking->id]) }}"
                                           class="inline-flex items-center justify-center rounded-xl bg-amber-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-amber-600">
                                            Bayar Sekarang
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- ============ RIWAYAT PEMBAYARAN ============ --}}
            <div>
                <h3 class="mb-3 flex items-center gap-2 text-sm font-medium text-neutral-500">
                    <span class="h-1.5 w-1.5 rounded-full bg-neutral-400"></span>
                    Riwayat Pembayaran
                </h3>

                @if ($pembayarans->count())
                    <div class="space-y-4">
                        @foreach ($pembayarans as $pembayaran)
                            @php
                                $statusConfig = [
                                    'pending' => ['badge' => 'bg-amber-50 text-amber-700', 'dot' => 'bg-amber-500'],
                                    'lunas'   => ['badge' => 'bg-emerald-50 text-emerald-700', 'dot' => 'bg-emerald-500'],
                                    'ditolak' => ['badge' => 'bg-red-50 text-red-600', 'dot' => 'bg-red-500'],
                                ][$pembayaran->status_bayar] ?? ['badge' => 'bg-slate-100 text-slate-600', 'dot' => 'bg-slate-400'];
                            @endphp

                            <div class="group relative rounded-3xl border border-neutral-200 bg-white p-6 shadow-sm transition hover:shadow-md hover:border-neutral-300">
                                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">

                                    <div class="flex items-start gap-4">
                                        <div class="hidden h-12 w-12 flex-shrink-0 items-center justify-center rounded-2xl bg-neutral-100 text-neutral-500 md:flex">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a4 4 0 00-8 0v2M5 9h14l1 12H4L5 9z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-emerald-600">
                                                {{ $pembayaran->booking->mobil->nama_mobil ?? 'Mobil' }}
                                            </p>
                                            <h3 class="mt-1 text-lg font-semibold text-neutral-900">
                                                {{ $pembayaran->booking->mobil->merk ?? '-' }}
                                            </h3>
                                            <p class="mt-2 flex items-center gap-1.5 text-sm text-neutral-500">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                {{ optional($pembayaran->tanggal_bayar)->format('d M Y H:i') ?? '-' }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex flex-col gap-3 md:items-end">
                                        <span class="inline-flex w-fit items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold {{ $statusConfig['badge'] }}">
                                            <span class="h-1.5 w-1.5 rounded-full {{ $statusConfig['dot'] }}"></span>
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
                    <div class="rounded-3xl border border-dashed border-neutral-300 bg-white p-12 text-center">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-neutral-100 text-neutral-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a4 4 0 00-8 0v2M5 9h14l1 12H4L5 9z" />
                            </svg>
                        </div>
                        <p class="mt-4 text-sm font-medium text-neutral-500">Belum ada pembayaran yang tercatat.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
