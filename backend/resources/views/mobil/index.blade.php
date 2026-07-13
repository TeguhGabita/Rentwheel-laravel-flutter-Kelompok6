<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="font-semibold text-xl text-neutral-800 leading-tight">Daftar Mobil</h2>
                <p class="text-sm text-neutral-500 mt-1">Temukan mobil yang sesuai untuk kebutuhan Anda.</p>
            </div>

            <form method="GET" class="w-full md:w-80">
                <label class="sr-only" for="search">Cari mobil</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                       placeholder="Cari nama atau merk mobil"
                       class="w-full rounded-xl border border-neutral-200 px-4 py-2.5 text-sm focus:border-amber-400 focus:outline-none focus:ring-1 focus:ring-amber-400">
            </form>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if ($mobils->count())
                <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($mobils as $mobil)
                        <div class="overflow-hidden rounded-3xl border border-neutral-200 bg-white shadow-sm">
                            <div class="flex h-48 items-center justify-center bg-slate-100">
                                @if ($mobil->foto)
                                    <img src="{{ asset('storage/' . $mobil->foto) }}" alt="{{ $mobil->nama_mobil }}" class="h-full w-full object-cover">
                                @else
                                    <div class="text-center text-slate-500">
                                        <p class="text-lg font-semibold">{{ $mobil->nama_mobil }}</p>
                                        <p class="text-sm">Tidak ada foto</p>
                                    </div>
                                @endif
                            </div>

                            <div class="p-5 space-y-4">
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <h3 class="font-semibold text-neutral-900">{{ $mobil->nama_mobil }}</h3>
                                        <p class="text-sm text-neutral-500">{{ $mobil->merk }}</p>
                                    </div>
                                    <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-medium text-amber-700">
                                        {{ $mobil->kategori->nama_kategori ?? '-' }}
                                    </span>
                                </div>

                                <div class="flex items-center justify-between text-sm text-neutral-600">
                                    <span>Plat: {{ $mobil->plat_nomor }}</span>
                                    <span class="font-semibold text-neutral-900">{{ ucfirst($mobil->status) }}</span>
                                </div>

                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-xs text-neutral-500">Harga per hari</p>
                                        <p class="text-lg font-semibold text-neutral-900">Rp {{ number_format($mobil->harga_sewa_per_hari, 0, ',', '.') }}</p>
                                    </div>
                                    <a href="{{ route('mobil.show', $mobil) }}" class="rounded-xl bg-amber-500 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-600">
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
                <div class="rounded-2xl border border-dashed border-neutral-300 bg-white p-10 text-center text-neutral-500">
                    Belum ada mobil yang tersedia.
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
