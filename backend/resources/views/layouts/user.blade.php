<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Beranda' }} — RentWheel</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        .font-display { font-family: 'Space Grotesk', sans-serif; }
    </style>
</head>
<body class="antialiased bg-[#f4f4f6]">
    <div class="min-h-screen flex flex-col">

        <!-- Navbar -->
        <header class="bg-[#0a0a0f] sticky top-0 z-30">
            <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
                <!-- Logo -->
                <a href="{{ route('beranda') }}" class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center">
                        <svg class="w-5 h-5 text-slate-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                  d="M5 11l1.5-4.5A2 2 0 018.4 5h7.2a2 2 0 011.9 1.5L19 11m-14 0v6a1 1 0 001 1h1a1 1 0 001-1v-1h8v1a1 1 0 001 1h1a1 1 0 001-1v-6m-14 0h14M7 15h.01M17 15h.01" />
                        </svg>
                    </div>
                    <span class="font-display text-lg font-bold text-white">RentWheel</span>
                </a>

                <!-- Nav links -->
                <nav class="hidden md:flex items-center gap-8">
                    <a href="{{ route('beranda') }}" class="text-sm font-medium text-amber-400">Beranda</a>
                    <a href="#" class="text-sm font-medium text-slate-400 hover:text-white transition-colors">Cari Mobil</a>
                    <a href="#" class="text-sm font-medium text-slate-400 hover:text-white transition-colors">Booking Saya</a>
                </nav>

                <!-- User menu -->
                <div class="flex items-center gap-4">
                    <div class="hidden sm:flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center text-slate-900 font-bold text-xs">
                            {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                        </div>
                        <span class="text-sm text-white font-medium">{{ auth()->user()->name ?? 'User' }}</span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm font-medium text-slate-400 hover:text-red-400 transition-colors">
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <!-- Page content -->
        <main class="flex-1">
            {{ $slot }}
        </main>

        <!-- Footer -->
        <footer class="bg-[#0a0a0f] py-6">
            <div class="max-w-6xl mx-auto px-6 text-center">
                <p class="text-slate-500 text-sm">&copy; {{ date('Y') }} RentWheel. Semua hak dilindungi.</p>
            </div>
        </footer>
    </div>
</body>
</html>
