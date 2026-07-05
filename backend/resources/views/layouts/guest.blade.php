<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'RentWheel') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        .font-display { font-family: 'Space Grotesk', sans-serif; }

        .grid-pattern {
            background-image:
                linear-gradient(rgba(251,191,36,0.06) 1px, transparent 1px),
                linear-gradient(90deg, rgba(251,191,36,0.06) 1px, transparent 1px);
            background-size: 48px 48px;
        }

        .glow {
            box-shadow: 0 0 120px 40px rgba(251,191,36,0.15);
        }
    </style>
</head>
<body class="antialiased bg-[#0a0a0f]">
    <div class="min-h-screen flex">

        <!-- Panel kiri: branding immersive -->
        <div class="hidden lg:flex lg:w-[55%] relative overflow-hidden bg-[#0a0a0f]">
            <!-- background layers -->
            <div class="absolute inset-0 grid-pattern"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] glow rounded-full"></div>
            <div class="absolute -top-32 -right-32 w-96 h-96 bg-gradient-to-br from-amber-500/20 to-transparent rounded-full blur-3xl"></div>
            <div class="absolute -bottom-32 -left-20 w-80 h-80 bg-gradient-to-tr from-orange-600/20 to-transparent rounded-full blur-3xl"></div>

            <!-- content -->
            <div class="relative z-10 flex flex-col justify-between p-14 w-full">
                <!-- logo -->
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center">
                        <svg class="w-6 h-6 text-slate-900" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M5 11l1.5-4.5A2 2 0 018.4 5h7.2a2 2 0 011.9 1.5L19 11m-14 0v6a1 1 0 001 1h1a1 1 0 001-1v-1h8v1a1 1 0 001 1h1a1 1 0 001-1v-6m-14 0h14M7 15h.01M17 15h.01"
                                  stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                        </svg>
                    </div>
                    <span class="font-display text-2xl font-bold text-white tracking-tight">RentWheel</span>
                </div>

                <!-- hero copy -->
                <div class="my-auto py-16">
                    <span class="inline-block px-3 py-1 rounded-full bg-amber-400/10 border border-amber-400/20 text-amber-400 text-xs font-medium tracking-wide mb-6">
                        PLATFORM SEWA MOBIL TERPERCAYA
                    </span>
                    <h1 class="font-display text-5xl font-bold text-white leading-[1.1] mb-6">
                        Perjalananmu,<br />
                        <span class="bg-gradient-to-r from-amber-300 to-orange-400 bg-clip-text text-transparent">
                            Mobil Pilihanmu.
                        </span>
                    </h1>
                    <p class="text-slate-400 text-lg max-w-md leading-relaxed">
                        Ratusan unit siap jalan, proses booking dalam hitungan menit, tanpa ribet.
                    </p>
                </div>
            </div>
        </div>

        <!-- Panel kanan: form -->
        <div class="w-full lg:w-[45%] flex items-center justify-center p-6 sm:p-12 bg-[#0d0d14] relative">
            <div class="absolute inset-0 grid-pattern opacity-40 lg:hidden"></div>

            <div class="w-full max-w-sm relative z-10">
                <!-- mobile logo -->
                <div class="lg:hidden flex items-center justify-center gap-2 mb-10">
                    <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center">
                        <svg class="w-5 h-5 text-slate-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                  d="M5 11l1.5-4.5A2 2 0 018.4 5h7.2a2 2 0 011.9 1.5L19 11m-14 0v6a1 1 0 001 1h1a1 1 0 001-1v-1h8v1a1 1 0 001 1h1a1 1 0 001-1v-6m-14 0h14M7 15h.01M17 15h.01" />
                        </svg>
                    </div>
                    <span class="font-display text-xl font-bold text-white">RentWheel</span>
                </div>

                {{ $slot }}
            </div>
        </div>
    </div>
</body>
</html>
