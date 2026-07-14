<x-app-layout>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600&family=Space+Mono:wght@400;700&display=swap');

        .font-display { font-family: 'Space Grotesk', sans-serif; }
        .font-mono-plate { font-family: 'Space Mono', monospace; }
        body, .font-body { font-family: 'Inter', sans-serif; }

        .status-dot {
            box-shadow: 0 0 0 3px rgba(255,255,255,0.6);
        }

        .plate-badge {
            background: #111318;
            border: 2px solid #3a3f4a;
            box-shadow: inset 0 0 0 1px rgba(255,255,255,0.05);
        }
        .plate-badge .plate-flag {
            background: linear-gradient(180deg, #2E6F95 0%, #1f4d68 100%);
        }

        /* Ticket-stub card: dashed divider with punched circle notches */
        .ticket {
            position: relative;
            transition: transform .2s ease, box-shadow .2s ease;
        }
        .ticket:hover {
            transform: translateY(-2px);
            box-shadow: 0 16px 32px -14px rgba(16,18,21,0.22);
        }
        .ticket-stub {
            border-left: 2px dashed #E2E4E9;
        }
        .ticket-notch {
            position: absolute;
            width: 22px;
            height: 22px;
            background: #F4F5F7;
            border-radius: 9999px;
            left: -12px;
        }
    </style>

    <x-slot name="header">
        <div class="rounded-3xl bg-gradient-to-br from-[#F5A623] to-[#D9820F] px-5 py-6 sm:px-8 sm:py-7 font-body">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="font-mono-plate text-[11px] uppercase tracking-[0.2em] text-white/80">Riwayat &middot; {{ $bookings->total() ?? $bookings->count() }} booking</p>
                    <h2 class="font-display text-2xl sm:text-3xl font-semibold text-white leading-tight mt-1">Riwayat Booking</h2>
                    <p class="text-sm text-white/80 mt-1">Lihat daftar pemesanan mobil Anda.</p>
                </div>
                <a href="{{ route('mobil.index') }}" class="w-fit rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-[#D9820F] hover:bg-white/90">
                    Cari Mobil
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 font-body bg-[#F4F5F7]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif

            @if ($bookings->count())
                <div class="space-y-4">
                    @foreach ($bookings as $booking)
                        @php
                            $statusRaw = strtolower($booking->status ?? '');
                            $statusStyle = match(true) {
                                str_contains($statusRaw, 'batal') => ['bg' => 'bg-red-50', 'text' => 'text-red-700', 'dot' => 'bg-red-500'],
                                str_contains($statusRaw, 'selesai') || str_contains($statusRaw, 'konfirmasi') || str_contains($statusRaw, 'confirm') => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'dot' => 'bg-emerald-500'],
                                default => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'dot' => 'bg-amber-500'],
                            };
                        @endphp
                        <div class="ticket flex flex-col md:flex-row overflow-hidden rounded-3xl border border-neutral-200 bg-white shadow-sm">
                            <div class="flex-1 p-6">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="font-mono-plate text-[11px] uppercase tracking-[0.15em] text-[#D9820F]">{{ $booking->mobil->nama_mobil ?? '-' }}</p>
                                        <h3 class="mt-1 font-display text-lg font-semibold text-neutral-900">{{ $booking->mobil->merk ?? '-' }}</h3>
                                    </div>
                                    <div class="plate-badge flex items-stretch overflow-hidden rounded-md shrink-0">
                                        <span class="plate-flag w-2"></span>
                                        <span class="font-mono-plate px-2.5 py-1 text-xs font-bold tracking-wider text-white">
                                            BK-{{ str_pad($booking->id, 4, '0', STR_PAD_LEFT) }}
                                        </span>
                                    </div>
                                </div>
                                <p class="mt-3 flex items-center gap-2 text-sm text-neutral-500">
                                    <svg class="h-4 w-4 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    {{ \Carbon\Carbon::parse($booking->tanggal_mulai)->format('d M Y') }} &mdash; {{ \Carbon\Carbon::parse($booking->tanggal_selesai)->format('d M Y') }}
                                </p>
                            </div>

                            <div class="ticket-stub relative flex flex-row items-center justify-between gap-4 p-6 md:w-64 md:flex-col md:items-end md:justify-center">
                                <span class="ticket-notch -top-[13px] md:top-auto md:-top-[13px]"></span>
                                <span class="ticket-notch -bottom-[13px] md:top-auto md:-bottom-[13px]"></span>

                                <span class="inline-flex w-fit items-center gap-1.5 rounded-full {{ $statusStyle['bg'] }} {{ $statusStyle['text'] }} px-3 py-1 text-xs font-semibold">
                                    <span class="status-dot h-1.5 w-1.5 rounded-full {{ $statusStyle['dot'] }}"></span>
                                    {{ ucfirst($booking->status) }}
                                </span>
                                <div class="text-right">
                                    <p class="text-xs text-neutral-500">Total</p>
                                    <p class="font-mono-plate text-base font-bold text-neutral-900">Rp {{ number_format($booking->total_harga, 0, ',', '.') }}</p>
                                </div>
                                @if (Route::has('booking.show'))
                                    <a href="{{ route('booking.show', $booking) }}" class="w-fit rounded-xl bg-[#16181D] px-4 py-2 text-xs font-semibold text-white hover:bg-[#F5A623] hover:text-[#16181D] transition-colors">
                                        Lihat Detail
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $bookings->links() }}
                </div>
            @else
                <div class="rounded-3xl border border-dashed border-neutral-300 bg-white p-10 text-center text-neutral-500">
                    <p class="font-display font-semibold text-neutral-700">Belum ada booking yang dibuat</p>
                    <p class="text-sm mt-1">Cari mobil yang Anda butuhkan dan buat pemesanan pertama Anda.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
