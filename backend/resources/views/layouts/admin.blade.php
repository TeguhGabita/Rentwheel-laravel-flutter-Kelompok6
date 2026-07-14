<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Dashboard' }} — RentWheel Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        .font-display { font-family: 'Space Grotesk', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="antialiased bg-[#f4f4f6]">
    <div class="min-h-screen flex flex-col">

        <!-- Top navbar -->
        <header class="bg-[#0a0a0f] sticky top-0 z-30">
            <div class="px-6 py-3 flex items-center justify-between border-b border-white/5">
                <!-- Logo -->
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center">
                        <svg class="w-5 h-5 text-slate-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                  d="M5 11l1.5-4.5A2 2 0 018.4 5h7.2a2 2 0 011.9 1.5L19 11m-14 0v6a1 1 0 001 1h1a1 1 0 001-1v-1h8v1a1 1 0 001 1h1a1 1 0 001-1v-6m-14 0h14M7 15h.01M17 15h.01" />
                        </svg>
                    </div>
                    <span class="font-display text-lg font-bold text-white hidden sm:inline">RentWheel</span>
                </a>

                <!-- User menu -->
                <div class="flex items-center gap-4">

                    {{-- Dropdown Notifikasi --}}
                    @php
                        $notifikasiList = auth()->user()->unreadNotifications;
                        $jumlahNotif = $notifikasiList->count();
                    @endphp

                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" @click.away="open = false"
                                class="relative p-2 rounded-lg hover:bg-white/5 transition-colors">
                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
                            </svg>
                            @if($jumlahNotif > 0)
                                <span class="absolute top-1 right-1 min-w-[16px] h-4 px-1 bg-amber-500 rounded-full text-[10px] font-bold text-slate-900 flex items-center justify-center">
                                    {{ $jumlahNotif }}
                                </span>
                            @endif
                        </button>

                        <div x-show="open" x-cloak
                             class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-lg border border-slate-200 z-50 max-h-96 overflow-y-auto">
                            <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100">
                                <p class="text-sm font-semibold text-slate-900">Notifikasi</p>
                                @if($jumlahNotif > 0)
                                    <form method="POST" action="{{ route('notifikasi.bacaSemua') }}">
                                        @csrf
                                        <button type="submit" class="text-xs text-amber-600 hover:underline">
                                            Tandai semua dibaca
                                        </button>
                                    </form>
                                @endif
                            </div>

                            @forelse($notifikasiList as $notif)
                                <a href="{{ route('notifikasi.baca', $notif->id) }}"
                                   class="block px-4 py-3 border-b border-slate-50 hover:bg-slate-50 transition-colors">
                                    <p class="text-sm text-slate-700">{{ $notif->data['message'] ?? 'Notifikasi baru' }}</p>
                                    <p class="text-xs text-slate-400 mt-1">{{ $notif->created_at->diffForHumans() }}</p>
                                </a>
                            @empty
                                <p class="px-4 py-6 text-sm text-slate-400 text-center">Tidak ada notifikasi baru</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="hidden sm:flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center text-slate-900 font-bold text-xs flex-shrink-0">
                            {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                        </div>
                        <div class="leading-tight">
                            <p class="text-white text-sm font-medium">{{ auth()->user()->name ?? 'Admin' }}</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm font-medium text-slate-400 hover:text-red-400 transition-colors">
                            Keluar
                        </button>
                    </form>
                </div>
            </div>

            <!-- Menu utama (horizontal) -->
            <nav class="px-6 flex items-center gap-1 overflow-x-auto">
                <a href="{{ route('dashboard') }}"
                   class="flex items-center gap-2 px-4 py-3 text-sm font-medium border-b-2 {{ request()->routeIs('dashboard') ? 'border-amber-400 text-amber-400' : 'border-transparent text-slate-400 hover:text-white' }} transition-colors whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/>
                    </svg>
                    Dashboard
                </a>

                <a href="{{ route('admin.mobil.index') }}"
                   class="flex items-center gap-2 px-4 py-3 text-sm font-medium border-b-2 border-transparent text-slate-400 hover:text-white transition-colors whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4.5 13.5V9.75a1.5 1.5 0 01.44-1.06l2.12-2.12A1.5 1.5 0 018.12 6h7.76a1.5 1.5 0 011.06.44l2.12 2.12a1.5 1.5 0 01.44 1.06v3.75m-16.5 0h16.5m-16.5 0a1.5 1.5 0 00-1.5 1.5v2.25a1.5 1.5 0 001.5 1.5h.75a1.5 1.5 0 001.5-1.5v-.75h11.5v.75a1.5 1.5 0 001.5 1.5h.75a1.5 1.5 0 001.5-1.5V15a1.5 1.5 0 00-1.5-1.5"/>
                    </svg>
                    Data Mobil
                </a>

                <a href="{{ route('admin.laporan.index') }}"
                   class="flex items-center gap-2 px-4 py-3 text-sm font-medium border-b-2 {{ request()->routeIs('admin.laporan.*') ? 'border-amber-400 text-amber-400' : 'border-transparent text-slate-400 hover:text-white' }} transition-colors whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .984.75 1.75 1.972 1.75.23 0 .456-.044.782-.128A48.408 48.408 0 016.892 6.108V2.892m9.216 0v5.6m3 .397a5.338 5.338 0 00-5.6 5.6v3.75a6.011 6.011 0 003.139 5.338c1.712.895 3.891.84 5.823-.066A6.011 6.011 0 0021 16.892v-3.75a5.338 5.338 0 00-5.784-5.6z"/>
                    </svg>
                    Laporan
                </a>

                <a href="{{ route('admin.users.index') }}"
                   class="flex items-center gap-2 px-4 py-3 text-sm font-medium border-b-2 {{ request()->routeIs('admin.users.*') ? 'border-amber-400 text-amber-400' : 'border-transparent text-slate-400 hover:text-white' }} transition-colors whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                    </svg>
                    Manajemen User
                </a>

                <a href="{{ route('admin.kategori.index') }}"
                   class="flex items-center gap-2 px-4 py-3 text-sm font-medium border-b-2 border-transparent text-slate-400 hover:text-white transition-colors whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 6h.008v.008H6V6z"/>
                    </svg>
                    Kategori
                </a>

                <a href="{{ route('profile.edit') }}"
                   class="flex items-center gap-2 px-4 py-3 text-sm font-medium border-b-2 border-transparent text-slate-400 hover:text-white transition-colors whitespace-nowrap ml-auto">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 11-2.83 2.83l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 11-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 11-2.83-2.83l.06-.06A1.65 1.65 0 004.6 15a1.65 1.65 0 00-1.51-1H3a2 2 0 110-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 112.83-2.83l.06.06A1.65 1.65 0 009 4.6a1.65 1.65 0 001-1.51V3a2 2 0 114 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 112.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 110 4h-.09a1.65 1.65 0 00-1.51 1z"/>
                    </svg>
                    Pengaturan
                </a>
            </nav>
        </header>

       <!-- Page header -->
<div class="bg-white border-b border-slate-200 px-8 py-5">
    <h1 class="font-display text-xl font-bold text-slate-900">
        {{ $title ?? 'Dashboard' }}
    </h1>

    @isset($subtitle)
        <p class="text-sm text-slate-500 mt-1">
            {{ $subtitle }}
        </p>
    @endisset
</div>

<!-- Page content -->
<main class="flex-1 p-8">
    {{ $slot }}
</main>

</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Flash Message Handler -->
@if (session('status'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: @json(session('status')),
            timer: 2000,
            showConfirmButton: false
        });
    });
</script>
@endif

@if (session('error'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: @json(session('error')),
            confirmButtonColor: '#d33'
        });
    });
</script>
@endif

@if ($errors->any())
<script>
    document.addEventListener('DOMContentLoaded', function () {
        Swal.fire({
            icon: 'error',
            title: 'Validasi Gagal!',
            html: @json(implode('<br>', $errors->all())),
            confirmButtonColor: '#d33'
        });
    });
</script>
@endif

{{-- Tempat script tambahan dari halaman anak (misal Chart.js di dashboard) --}}
@stack('scripts')

</body>
</html>
