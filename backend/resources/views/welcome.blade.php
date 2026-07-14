<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'RentWheel') }} — Sewa Mobil Mudah &amp; Terpercaya</title>

        @fonts

        <style>
            @import url('https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600;700&family=Space+Mono:wght@400;700&display=swap');

            .font-display { font-family: 'Space Grotesk', sans-serif; }
            .font-mono-plate { font-family: 'Space Mono', monospace; }
            body, .font-body { font-family: 'Inter', sans-serif; }

            .status-dot { box-shadow: 0 0 0 3px rgba(255,255,255,0.6); }

            .hero-grid {
                background-image:
                    linear-gradient(rgba(255,255,255,0.045) 1px, transparent 1px),
                    linear-gradient(90deg, rgba(255,255,255,0.045) 1px, transparent 1px);
                background-size: 44px 44px;
            }

            .glow-amber {
                background: radial-gradient(circle, rgba(245,166,35,0.35) 0%, rgba(245,166,35,0) 70%);
            }

            .plate-badge {
                background: #111318;
                border: 2px solid #3a3f4a;
                box-shadow: inset 0 0 0 1px rgba(255,255,255,0.05);
            }
            .plate-badge .plate-flag { background: linear-gradient(180deg, #2E6F95 0%, #1f4d68 100%); }

            .lift-card { transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease; }
            .lift-card:hover { transform: translateY(-4px); box-shadow: 0 20px 40px -12px rgba(16,18,21,0.18); }

            .step-line { border-top: 2px dashed #2d323d; }
        </style>

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="font-body bg-[#F4F5F7] text-[#16181D] antialiased">

        {{-- ============ NAVBAR ============ --}}
        <header class="sticky top-0 z-30 border-b border-[#2d323d]/10 bg-[#16181D]">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                <a href="/" class="flex items-center gap-2">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#F5A623]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#16181D]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l1.5-4.5A2 2 0 018.4 7h7.2a2 2 0 011.9 1.5L19 13m-14 0v5a1 1 0 001 1h1a1 1 0 001-1v-1h8v1a1 1 0 001 1h1a1 1 0 001-1v-5m-14 0h14" />
                        </svg>
                    </span>
                    <span class="font-display text-lg font-bold text-white">RentWheel</span>
                </a>

                <nav class="hidden items-center gap-8 md:flex">
                    <a href="#armada" class="text-sm font-medium text-[#8B93A3] hover:text-white transition">Armada</a>
                    <a href="#cara-kerja" class="text-sm font-medium text-[#8B93A3] hover:text-white transition">Cara Kerja</a>
                    <a href="#kenapa-kami" class="text-sm font-medium text-[#8B93A3] hover:text-white transition">Kenapa Kami</a>
                </nav>

                @if (Route::has('login'))
                    <div class="flex items-center gap-3">
                        @auth
                            <a href="{{ url('/dashboard') }}"
                               class="rounded-xl bg-[#F5A623] px-4 py-2 text-sm font-semibold text-[#16181D] hover:bg-[#e2951a] transition">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                               class="text-sm font-medium text-[#EDEFF2] hover:text-[#F5A623] transition">
                                Masuk
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}"
                                   class="rounded-xl bg-[#F5A623] px-4 py-2 text-sm font-semibold text-[#16181D] hover:bg-[#e2951a] transition">
                                    Daftar
                                </a>
                            @endif
                        @endauth
                    </div>
                @endif
            </div>
        </header>

        {{-- ============ HERO ============ --}}
        <section class="relative overflow-hidden bg-[#16181D]">
            <div class="hero-grid absolute inset-0"></div>
            <div class="glow-amber absolute -top-32 right-0 h-96 w-96"></div>
            <div class="glow-amber absolute bottom-0 left-0 h-72 w-72 opacity-60"></div>

            <div class="relative mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8 lg:py-28">
                <div class="grid items-center gap-12 lg:grid-cols-2">
                    <div>
                        <span class="font-mono-plate inline-flex items-center gap-2 rounded-full border border-[#2d323d] bg-[#1d2027] px-3 py-1 text-[11px] uppercase tracking-[0.2em] text-[#F5A623]">
                            <span class="status-dot h-1.5 w-1.5 rounded-full bg-[#F5A623]"></span>
                            Sewa mobil terpercaya
                        </span>

                        <h1 class="font-display mt-5 text-4xl font-bold leading-tight text-white sm:text-5xl">
                            Sewa Mobil Jadi<br class="hidden sm:block"> Lebih <span class="text-[#F5A623]">Mudah</span>
                        </h1>

                        <p class="mt-5 max-w-md text-base text-[#8B93A3]">
                            Pesan mobil dalam hitungan menit, bayar aman secara online, dan pantau setiap booking Anda dari satu dashboard.
                        </p>

                        <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                            <a href="{{ Route::has('register') ? route('register') : '#armada' }}"
                               class="inline-flex items-center justify-center rounded-xl bg-[#F5A623] px-6 py-3 text-sm font-semibold text-[#16181D] hover:bg-[#e2951a] transition">
                                Mulai Sewa Sekarang
                            </a>
                            <a href="#cara-kerja"
                               class="inline-flex items-center justify-center rounded-xl border border-[#2d323d] bg-[#1d2027] px-6 py-3 text-sm font-semibold text-[#EDEFF2] hover:border-[#F5A623] hover:text-[#F5A623] transition">
                                Lihat Cara Kerja
                            </a>
                        </div>

                        <div class="mt-10 grid grid-cols-3 gap-4 border-t border-[#2d323d] pt-6">
                            <div>
                                <p class="font-mono-plate text-2xl font-bold text-white">120+</p>
                                <p class="text-xs text-[#8B93A3] mt-1">Unit Armada</p>
                            </div>
                            <div>
                                <p class="font-mono-plate text-2xl font-bold text-white">8.4K</p>
                                <p class="text-xs text-[#8B93A3] mt-1">Booking Selesai</p>
                            </div>
                            <div>
                                <p class="font-mono-plate text-2xl font-bold text-white">4.9<span class="text-[#F5A623]">★</span></p>
                                <p class="text-xs text-[#8B93A3] mt-1">Rating Pengguna</p>
                            </div>
                        </div>
                    </div>

                    <div class="relative">
                        <div class="lift-card overflow-hidden rounded-3xl border border-[#2d323d] bg-[#1d2027] shadow-2xl">
                            <div class="relative h-56 bg-gradient-to-br from-[#2d323d] to-[#16181D]">
                                <div class="flex h-full items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 text-[#F5A623]/40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l1.5-4.5A2 2 0 018.4 7h7.2a2 2 0 011.9 1.5L19 13m-14 0v5a1 1 0 001 1h1a1 1 0 001-1v-1h8v1a1 1 0 001 1h1a1 1 0 001-1v-5m-14 0h14" />
                                    </svg>
                                </div>
                                <span class="absolute top-3 left-3 rounded-full bg-white/90 px-3 py-1 text-xs font-semibold text-[#16181D]">
                                    MPV
                                </span>
                                <div class="plate-badge absolute bottom-3 right-3 flex items-stretch overflow-hidden rounded-md">
                                    <span class="plate-flag w-2"></span>
                                    <span class="font-mono-plate px-2.5 py-1 text-sm font-bold tracking-wider text-white">B 1234 RWL</span>
                                </div>
                            </div>
                            <div class="p-5 space-y-4">
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <h3 class="font-display font-semibold text-white">Avanza</h3>
                                        <p class="text-sm text-[#8B93A3]">Toyota</p>
                                    </div>
                                    <span class="flex items-center gap-1.5 rounded-full bg-emerald-500/10 px-2.5 py-1 text-xs font-semibold text-emerald-400">
                                        <span class="status-dot h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                        Tersedia
                                    </span>
                                </div>
                                <div class="flex items-center justify-between border-t border-dashed border-[#2d323d] pt-4">
                                    <div>
                                        <p class="text-xs text-[#8B93A3]">Harga per hari</p>
                                        <p class="font-mono-plate text-lg font-bold text-white">Rp 300.000</p>
                                    </div>
                                    <span class="rounded-xl bg-[#F5A623] px-4 py-2 text-sm font-semibold text-[#16181D]">
                                        Lihat Detail
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ============ ARMADA ============ --}}
        <section id="armada" class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                <p class="font-mono-plate text-[11px] uppercase tracking-[0.2em] text-[#F5A623]">Armada Kami</p>
                <h2 class="font-display mt-2 text-3xl font-bold text-neutral-900">Pilihan Mobil Terbaik</h2>
                <p class="mt-3 text-sm text-neutral-500">Beragam pilihan armada siap disewa, sesuai kebutuhan perjalanan Anda.</p>
            </div>

            <div class="mt-12 grid gap-6 md:grid-cols-3">
                @php
                    $armada = [
                        ['tipe' => 'MPV', 'nama' => 'Avanza', 'merk' => 'Toyota', 'plat' => 'B 1234 RWL', 'harga' => 'Rp 300.000', 'tersedia' => true],
                        ['tipe' => 'SUV', 'nama' => 'Fortuner', 'merk' => 'Toyota', 'plat' => 'B 5678 RWL', 'harga' => 'Rp 650.000', 'tersedia' => true],
                        ['tipe' => 'City Car', 'nama' => 'Brio', 'merk' => 'Honda', 'plat' => 'B 9012 RWL', 'harga' => 'Rp 250.000', 'tersedia' => false],
                    ];
                @endphp

                @foreach ($armada as $mobil)
                    <div class="lift-card overflow-hidden rounded-3xl border border-neutral-200 bg-white shadow-sm">
                        <div class="relative h-44 bg-gradient-to-br from-neutral-200 to-neutral-100">
                            <div class="flex h-full items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l1.5-4.5A2 2 0 018.4 7h7.2a2 2 0 011.9 1.5L19 13m-14 0v5a1 1 0 001 1h1a1 1 0 001-1v-1h8v1a1 1 0 001 1h1a1 1 0 001-1v-5m-14 0h14" />
                                </svg>
                            </div>
                            <span class="absolute top-3 left-3 rounded-full bg-white/90 px-3 py-1 text-xs font-semibold text-[#16181D]">
                                {{ $mobil['tipe'] }}
                            </span>
                            <div class="plate-badge absolute bottom-3 right-3 flex items-stretch overflow-hidden rounded-md">
                                <span class="plate-flag w-2"></span>
                                <span class="font-mono-plate px-2.5 py-1 text-xs font-bold tracking-wider text-white">{{ $mobil['plat'] }}</span>
                            </div>
                        </div>
                        <div class="p-5 space-y-4">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <h3 class="font-display font-semibold text-neutral-900">{{ $mobil['nama'] }}</h3>
                                    <p class="text-sm text-neutral-500">{{ $mobil['merk'] }}</p>
                                </div>
                                @if ($mobil['tersedia'])
                                    <span class="flex items-center gap-1.5 rounded-full bg-emerald-500/10 px-2.5 py-1 text-xs font-semibold text-emerald-600">
                                        <span class="status-dot h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                        Tersedia
                                    </span>
                                @else
                                    <span class="flex items-center gap-1.5 rounded-full bg-red-500/10 px-2.5 py-1 text-xs font-semibold text-red-600">
                                        <span class="status-dot h-1.5 w-1.5 rounded-full bg-red-500"></span>
                                        Disewa
                                    </span>
                                @endif
                            </div>
                            <div class="flex items-center justify-between border-t border-dashed border-neutral-200 pt-4">
                                <div>
                                    <p class="text-xs text-neutral-500">Harga per hari</p>
                                    <p class="font-mono-plate text-lg font-bold text-neutral-900">{{ $mobil['harga'] }}</p>
                                </div>
                                <a href="{{ Route::has('login') ? route('login') : '#' }}"
                                   class="rounded-xl bg-[#F5A623] px-4 py-2 text-sm font-semibold text-[#16181D] hover:bg-[#e2951a] transition">
                                    Lihat Detail
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- ============ CARA KERJA ============ --}}
        <section id="cara-kerja" class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                <p class="font-mono-plate text-[11px] uppercase tracking-[0.2em] text-[#F5A623]">Cara Kerja</p>
                <h2 class="font-display mt-2 text-3xl font-bold text-neutral-900">Tiga Langkah Menuju Perjalanan</h2>
                <p class="mt-3 text-sm text-neutral-500">Tanpa antre, tanpa ribet — semua bisa dilakukan dari HP Anda.</p>
            </div>

            <div class="mt-12 grid gap-6 md:grid-cols-3">
                @php
                    $steps = [
                        ['no' => '01', 'title' => 'Pilih Mobil', 'desc' => 'Cari armada sesuai kebutuhan, filter berdasarkan merk, harga, dan ketersediaan.'],
                        ['no' => '02', 'title' => 'Booking & Bayar', 'desc' => 'Tentukan tanggal sewa dan lakukan pembayaran online secara aman.'],
                        ['no' => '03', 'title' => 'Jemput Mobil', 'desc' => 'Pantau status booking dan ambil mobil sesuai jadwal yang dipilih.'],
                    ];
                @endphp

                @foreach ($steps as $step)
                    <div class="lift-card rounded-3xl border border-neutral-200 bg-white p-6 shadow-sm">
                        <span class="font-mono-plate text-3xl font-bold text-[#F5A623]/30">{{ $step['no'] }}</span>
                        <h3 class="font-display mt-3 text-lg font-semibold text-neutral-900">{{ $step['title'] }}</h3>
                        <p class="mt-2 text-sm text-neutral-500">{{ $step['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- ============ KENAPA KAMI ============ --}}
        <section id="kenapa-kami" class="bg-[#16181D]">
            <div class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-2xl text-center">
                    <p class="font-mono-plate text-[11px] uppercase tracking-[0.2em] text-[#F5A623]">Kenapa RentWheel</p>
                    <h2 class="font-display mt-2 text-3xl font-bold text-white">Dibangun untuk Ketenangan Anda</h2>
                </div>

                <div class="mt-12 grid gap-6 md:grid-cols-3">
                    @php
                        $features = [
                            ['icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'title' => 'Armada Terverifikasi', 'desc' => 'Setiap unit diperiksa rutin dan terjamin kelayakannya sebelum disewakan.'],
                            ['icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'title' => 'Proses Cepat', 'desc' => 'Booking hingga konfirmasi pembayaran selesai dalam hitungan menit.'],
                            ['icon' => 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'title' => 'Pembayaran Aman', 'desc' => 'Riwayat transaksi tercatat rapi dan dapat dipantau kapan saja.'],
                        ];
                    @endphp

                    @foreach ($features as $feature)
                        <div class="lift-card rounded-3xl border border-[#2d323d] bg-[#1d2027] p-6">
                            <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-[#F5A623]/10 text-[#F5A623]">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $feature['icon'] }}" />
                                </svg>
                            </span>
                            <h3 class="font-display mt-4 text-lg font-semibold text-white">{{ $feature['title'] }}</h3>
                            <p class="mt-2 text-sm text-[#8B93A3]">{{ $feature['desc'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- ============ CTA ============ --}}
        <section class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
            <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-[#F5A623] to-[#e2951a] px-8 py-12 text-center shadow-lg sm:px-16">
                <div class="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-white/10"></div>
                <div class="absolute -left-16 -bottom-16 h-48 w-48 rounded-full bg-white/10"></div>
                <div class="relative">
                    <h2 class="font-display text-2xl font-bold text-[#16181D] sm:text-3xl">Siap Memulai Perjalanan Anda?</h2>
                    <p class="mt-2 text-sm text-[#16181D]/80">Daftar sekarang dan temukan mobil yang cocok untuk kebutuhan Anda.</p>
                    <div class="mt-6 flex flex-col items-center justify-center gap-3 sm:flex-row">
                        <a href="{{ Route::has('register') ? route('register') : '#armada' }}"
                           class="inline-flex items-center justify-center rounded-xl bg-[#16181D] px-6 py-3 text-sm font-semibold text-white hover:bg-[#0d0e11] transition">
                            Daftar Gratis
                        </a>
                        @if (Route::has('login'))
                            <a href="{{ route('login') }}"
                               class="inline-flex items-center justify-center rounded-xl border border-[#16181D]/20 bg-white/20 px-6 py-3 text-sm font-semibold text-[#16181D] hover:bg-white/30 transition">
                                Sudah Punya Akun? Masuk
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        {{-- ============ FOOTER ============ --}}
        <footer class="border-t border-[#2d323d]/10 bg-[#16181D]">
            <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
                <div class="flex flex-col items-center justify-between gap-4 sm:flex-row">
                    <div class="flex items-center gap-2">
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#F5A623]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#16181D]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l1.5-4.5A2 2 0 018.4 7h7.2a2 2 0 011.9 1.5L19 13m-14 0v5a1 1 0 001 1h1a1 1 0 001-1v-1h8v1a1 1 0 001 1h1a1 1 0 001-1v-5m-14 0h14" />
                            </svg>
                        </span>
                        <span class="font-display text-sm font-semibold text-white">RentWheel</span>
                    </div>
                    <p class="font-mono-plate text-xs text-[#8B93A3]">&copy; {{ date('Y') }} RentWheel. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </body>
</html>
