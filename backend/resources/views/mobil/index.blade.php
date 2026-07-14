<x-app-layout>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600&family=Space+Mono:wght@400;700&display=swap');

        .font-display { font-family: 'Space Grotesk', sans-serif; }
        .font-mono-plate { font-family: 'Space Mono', monospace; }
        body, .font-body { font-family: 'Inter', sans-serif; }

        .plate-badge {
            background: #111318;
            border: 2px solid #3a3f4a;
            box-shadow: inset 0 0 0 1px rgba(255,255,255,0.05);
        }
        .plate-badge .plate-flag {
            background: linear-gradient(180deg, #2E6F95 0%, #1f4d68 100%);
        }
        .status-dot {
            box-shadow: 0 0 0 3px rgba(255,255,255,0.6);
        }
        .car-card {
            transition: transform .25s ease, box-shadow .25s ease;
        }
        .car-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px -12px rgba(16,18,21,0.25);
        }
        .filter-input {
            background: #1d2027;
            border: 1px solid #2d323d;
            color: #EDEFF2;
        }
        .filter-input::placeholder { color: #6b7280; }
        .filter-input:focus {
            border-color: #F5A623;
            box-shadow: 0 0 0 3px rgba(245,166,35,0.18);
            outline: none;
        }
        .filter-label { color: #8B93A3; }
    </style>

    <x-slot name="header">
        <div class="rounded-3xl bg-[#16181D] px-5 py-6 sm:px-8 sm:py-7 font-body">
            <div class="flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="font-mono-plate text-[11px] uppercase tracking-[0.2em] text-[#F5A623]">Armada &middot; {{ $mobils->total() ?? $mobils->count() }} unit</p>
                    <h2 class="font-display text-2xl sm:text-3xl font-semibold text-white leading-tight mt-1">Daftar Mobil</h2>
                    <p class="text-sm text-[#8B93A3] mt-1">Temukan mobil yang sesuai untuk kebutuhan Anda.</p>
                </div>
            </div>

            <form method="GET" class="mt-6 grid grid-cols-1 gap-3 md:grid-cols-12 md:items-end">
                <div class="md:col-span-4">
                    <label class="text-xs font-medium filter-label" for="search">Cari mobil</label>
                    <div class="relative mt-1.5">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-[#6b7280]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m0 0a7.5 7.5 0 10-10.6 0 7.5 7.5 0 0010.6 0z" />
                        </svg>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                               placeholder="Nama atau merk mobil"
                               class="filter-input w-full rounded-xl pl-9 pr-4 py-2.5 text-sm">
                    </div>
                </div>

                <div class="md:col-span-2">
                    <label class="text-xs font-medium filter-label" for="merk">Merk</label>
                    <select name="merk" id="merk"
                            class="filter-input mt-1.5 w-full rounded-xl px-3 py-2.5 text-sm">
                        <option value="">Semua Merk</option>
                        @foreach ($daftarMerk as $merk)
                            <option value="{{ $merk }}" {{ request('merk') == $merk ? 'selected' : '' }}>
                                {{ $merk }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-3 grid grid-cols-2 gap-2">
                    <div>
                        <label class="text-xs font-medium filter-label" for="harga_min">Harga Min</label>
                        <input type="number" name="harga_min" id="harga_min" value="{{ request('harga_min') }}"
                               placeholder="0"
                               class="filter-input mt-1.5 w-full rounded-xl px-3 py-2.5 text-sm font-mono-plate">
                    </div>
                    <div>
                        <label class="text-xs font-medium filter-label" for="harga_max">Harga Max</label>
                        <input type="number" name="harga_max" id="harga_max" value="{{ request('harga_max') }}"
                               placeholder="1000000"
                               class="filter-input mt-1.5 w-full rounded-xl px-3 py-2.5 text-sm font-mono-plate">
                    </div>
                </div>

                <div class="md:col-span-3">
                    <label class="text-xs font-medium filter-label">Ketersediaan</label>
                    <div class="mt-1.5 grid grid-cols-3 gap-1.5 rounded-xl bg-[#1d2027] border border-[#2d323d] p-1">
                        @php $statusNow = request('status', ''); @endphp
                        <label class="cursor-pointer">
                            <input type="radio" name="status" value="" class="peer hidden" {{ $statusNow === '' ? 'checked' : '' }} onchange="this.form.submit()">
                            <span class="block rounded-lg py-1.5 text-center text-xs font-medium text-[#8B93A3] peer-checked:bg-[#F5A623] peer-checked:text-[#16181D] peer-checked:font-semibold">Semua</span>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="status" value="tersedia" class="peer hidden" {{ $statusNow === 'tersedia' ? 'checked' : '' }} onchange="this.form.submit()">
                            <span class="block rounded-lg py-1.5 text-center text-xs font-medium text-[#8B93A3] peer-checked:bg-[#F5A623] peer-checked:text-[#16181D] peer-checked:font-semibold">Tersedia</span>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="status" value="servis" class="peer hidden" {{ $statusNow === 'servis' ? 'checked' : '' }} onchange="this.form.submit()">
                            <span class="block rounded-lg py-1.5 text-center text-xs font-medium text-[#8B93A3] peer-checked:bg-[#F5A623] peer-checked:text-[#16181D] peer-checked:font-semibold">Servis</span>
                        </label>
                    </div>
                </div>

                <noscript>
                    <div class="md:col-span-12">
                        <button type="submit" class="rounded-xl bg-[#F5A623] px-4 py-2.5 text-sm font-semibold text-[#16181D]">Cari</button>
                    </div>
                </noscript>
            </form>

            @if(request()->hasAny(['search', 'merk', 'harga_min', 'harga_max', 'status']))
                <div class="mt-3 flex justify-end">
                    <a href="{{ route('mobil.index') }}"
                       class="text-xs font-medium text-[#8B93A3] hover:text-[#F5A623] underline underline-offset-2">
                        Reset filter
                    </a>
                </div>
            @endif
        </div>
    </x-slot>

    <div class="py-8 font-body bg-[#F4F5F7]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if ($mobils->count())
                <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($mobils as $mobil)
                        @php $tersedia = $mobil->status === 'tersedia'; @endphp
                        <div class="car-card overflow-hidden rounded-3xl border border-neutral-200 bg-white shadow-sm">
                            <div class="relative h-48 bg-gradient-to-br from-[#1d2027] to-[#2d323d]">
                                @if ($mobil->foto)
                                    <img src="{{ asset('storage/' . $mobil->foto) }}" alt="{{ $mobil->nama_mobil }}" class="h-full w-full object-cover">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/0 to-black/0"></div>
                                @else
                                    <div class="flex h-full items-center justify-center text-center text-white/70">
                                        <div>
                                            <p class="font-display text-lg font-semibold">{{ $mobil->nama_mobil }}</p>
                                            <p class="text-sm">Tidak ada foto</p>
                                        </div>
                                    </div>
                                @endif

                                <span class="absolute top-3 left-3 rounded-full bg-white/90 backdrop-blur px-3 py-1 text-xs font-semibold text-[#16181D]">
                                    {{ $mobil->kategori->nama_kategori ?? '-' }}
                                </span>

                                <div class="plate-badge absolute bottom-3 right-3 flex items-stretch overflow-hidden rounded-md">
                                    <span class="plate-flag w-2"></span>
                                    <span class="font-mono-plate px-2.5 py-1 text-sm font-bold tracking-wider text-white">
                                        {{ $mobil->plat_nomor }}
                                    </span>
                                </div>
                            </div>

                            <div class="p-5 space-y-4">
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <h3 class="font-display font-semibold text-neutral-900">{{ $mobil->nama_mobil }}</h3>
                                        <p class="text-sm text-neutral-500">{{ $mobil->merk }}</p>
                                    </div>
                                    <span class="flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold {{ $tersedia ? 'bg-emerald-50 text-emerald-700' : 'bg-orange-50 text-orange-700' }}">
                                        <span class="status-dot h-1.5 w-1.5 rounded-full {{ $tersedia ? 'bg-emerald-500' : 'bg-orange-500' }}"></span>
                                        {{ ucfirst($mobil->status) }}
                                    </span>
                                </div>

                                <div class="flex items-center justify-between border-t border-dashed border-neutral-200 pt-4">
                                    <div>
                                        <p class="text-xs text-neutral-500">Harga per hari</p>
                                        <p class="font-mono-plate text-lg font-bold text-neutral-900">Rp {{ number_format($mobil->harga_sewa_per_hari, 0, ',', '.') }}</p>
                                    </div>
                                    <a href="{{ route('mobil.show', $mobil) }}" class="rounded-xl bg-[#16181D] px-4 py-2 text-sm font-semibold text-white hover:bg-[#F5A623] hover:text-[#16181D] transition-colors">
                                        Lihat Detail
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $mobils->links() }}
                </div>
            @else
                <div class="rounded-3xl border border-dashed border-neutral-300 bg-white p-10 text-center text-neutral-500">
                    <p class="font-display font-semibold text-neutral-700">Belum ada mobil yang tersedia</p>
                    <p class="text-sm mt-1">Coba ubah filter pencarian Anda.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
