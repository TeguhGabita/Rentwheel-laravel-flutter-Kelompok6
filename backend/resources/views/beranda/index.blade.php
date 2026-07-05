<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-neutral-800 leading-tight">Beranda</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- Hero sapaan --}}
            <div class="relative overflow-hidden rounded-3xl bg-neutral-900 px-7 py-10 sm:px-10 sm:py-12">
                <div class="absolute inset-0 opacity-[0.06]"
                     style="background-image: radial-gradient(circle, #ffffff 1px, transparent 1px); background-size: 22px 22px;"></div>

                <div class="absolute -right-16 -top-16 w-64 h-64 rounded-full bg-amber-500/10 blur-2xl"></div>
                <div class="absolute -right-8 bottom-0 w-40 h-40 rounded-full bg-amber-500/10 blur-2xl"></div>

                <div class="relative z-10 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-6">
                    <div>
                        <span class="inline-block text-xs font-medium text-amber-400 bg-amber-400/10 border border-amber-400/20 rounded-full px-3 py-1 mb-4">
                            Selamat datang kembali
                        </span>
                        <h1 class="text-2xl sm:text-3xl font-semibold text-white leading-tight">
                            Halo, {{ auth()->user()->name }} 👋
                        </h1>
                        <p class="text-neutral-400 text-sm mt-2">
                            Mau sewa mobil ke mana hari ini?
                        </p>
                    </div>

                    <a href="{{ route('mobil.index') }}"
                       class="inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-400 text-neutral-900 text-sm font-semibold rounded-xl px-5 py-3 transition shrink-0 w-fit">
                        Cari Mobil
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                </div>
            </div>

            {{-- Ringkasan statistik --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

                <div class="bg-white border border-neutral-200 rounded-2xl p-5 flex items-center gap-4">
                    <div class="w-11 h-11 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-neutral-500">Booking aktif</p>
                        <p class="text-xl font-semibold text-neutral-900">{{ $bookingAktif ?? 0 }}</p>
                    </div>
                </div>

                <div class="bg-white border border-neutral-200 rounded-2xl p-5 flex items-center gap-4">
                    <div class="w-11 h-11 rounded-xl bg-violet-50 flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-violet-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-neutral-500">Riwayat booking</p>
                        <p class="text-xl font-semibold text-neutral-900">{{ $totalBooking ?? 0 }}</p>
                    </div>
                </div>

                <div class="bg-white border border-neutral-200 rounded-2xl p-5 flex items-center gap-4">
                    <div class="w-11 h-11 rounded-xl bg-amber-50 flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-neutral-500">Menunggu pembayaran</p>
                        <p class="text-xl font-semibold text-amber-600">{{ $menungguBayar ?? 0 }}</p>
                    </div>
                </div>

            </div>

            {{-- Menu utama --}}
            <div>
                <h3 class="text-sm font-medium text-neutral-500 mb-3">Menu</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">

                    {{-- Cari Mobil --}}
                    <a href="{{ route('mobil.index') }}"
                       class="group relative bg-white border border-neutral-200 rounded-2xl p-6 overflow-hidden hover:border-amber-300 hover:shadow-lg hover:shadow-amber-100/50 transition-all duration-200">
                        <div class="absolute -right-6 -top-6 w-24 h-24 rounded-full bg-amber-50 group-hover:scale-125 transition-transform duration-300"></div>
                        <div class="relative z-10">
                            <div class="w-12 h-12 rounded-xl bg-amber-500 flex items-center justify-center mb-5 shadow-sm shadow-amber-500/30">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 16l-2 2m0 0l-2-2m2 2V9a2 2 0 012-2h8a2 2 0 012 2v9m-12 0h12m-2 0l2 2m-2-2l2-2" />
                                </svg>
                            </div>
                            <h4 class="font-semibold text-neutral-900 text-[15px]">Cari Mobil</h4>
                            <p class="text-sm text-neutral-500 mt-1.5 leading-relaxed">Lihat unit yang tersedia untuk disewa</p>
                            <span class="inline-flex items-center gap-1 text-sm font-medium text-amber-600 mt-4 group-hover:gap-2 transition-all">
                                Lihat katalog
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                            </span>
                        </div>
                    </a>

                    {{-- Booking Saya --}}
                    <a href="{{ route('booking.index') }}"
                       class="group relative bg-white border border-neutral-200 rounded-2xl p-6 overflow-hidden hover:border-blue-300 hover:shadow-lg hover:shadow-blue-100/50 transition-all duration-200">
                        <div class="absolute -right-6 -top-6 w-24 h-24 rounded-full bg-blue-50 group-hover:scale-125 transition-transform duration-300"></div>
                        <div class="relative z-10">
                            <div class="w-12 h-12 rounded-xl bg-blue-600 flex items-center justify-center mb-5 shadow-sm shadow-blue-500/30">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <h4 class="font-semibold text-neutral-900 text-[15px]">Booking Saya</h4>
                            <p class="text-sm text-neutral-500 mt-1.5 leading-relaxed">Kelola dan pantau status pesananmu</p>
                            <span class="inline-flex items-center gap-1 text-sm font-medium text-blue-600 mt-4 group-hover:gap-2 transition-all">
                                Lihat semua
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                            </span>
                        </div>
                    </a>

                    {{-- Pembayaran --}}
                    <a href="{{ route('pembayaran.index') }}"
                       class="group relative bg-white border border-neutral-200 rounded-2xl p-6 overflow-hidden hover:border-emerald-300 hover:shadow-lg hover:shadow-emerald-100/50 transition-all duration-200">
                        <div class="absolute -right-6 -top-6 w-24 h-24 rounded-full bg-emerald-50 group-hover:scale-125 transition-transform duration-300"></div>
                        <div class="relative z-10">
                            <div class="w-12 h-12 rounded-xl bg-emerald-600 flex items-center justify-center mb-5 shadow-sm shadow-emerald-500/30">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <h4 class="font-semibold text-neutral-900 text-[15px]">Pembayaran</h4>
                            <p class="text-sm text-neutral-500 mt-1.5 leading-relaxed">Riwayat dan status pembayaran sewamu</p>
                            <span class="inline-flex items-center gap-1 text-sm font-medium text-emerald-600 mt-4 group-hover:gap-2 transition-all">
                                Lihat riwayat
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                            </span>
                        </div>
                    </a>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
