<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-neutral-800 leading-tight">Detail Mobil</h2>
                <p class="text-sm text-neutral-500 mt-1">Informasi lengkap mengenai mobil yang dipilih.</p>
            </div>
            <a href="{{ route('mobil.index') }}" class="rounded-xl border border-neutral-200 px-4 py-2 text-sm font-medium text-neutral-700 hover:bg-neutral-50">
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-3xl border border-neutral-200 bg-white shadow-sm">
                <div class="grid gap-0 lg:grid-cols-2">
                    <div class="flex h-80 items-center justify-center bg-slate-100">
                        @if ($mobil->foto)
                            <img src="{{ asset('storage/' . $mobil->foto) }}" alt="{{ $mobil->nama_mobil }}" class="h-full w-full object-cover">
                        @else
                            <div class="text-center text-slate-500">
                                <p class="text-lg font-semibold">{{ $mobil->nama_mobil }}</p>
                                <p class="text-sm">Tidak ada foto</p>
                            </div>
                        @endif
                    </div>

                    <div class="p-8 space-y-6">
                        <div>
                            <p class="text-sm font-medium text-amber-600">{{ $mobil->kategori->nama_kategori ?? '-' }}</p>
                            <h3 class="mt-2 text-2xl font-semibold text-neutral-900">{{ $mobil->nama_mobil }}</h3>
                            <p class="mt-2 text-neutral-600">{{ $mobil->merk }}</p>
                        </div>

                        <dl class="grid gap-4 sm:grid-cols-2">
                            <div class="rounded-2xl bg-neutral-50 p-4">
                                <dt class="text-xs uppercase tracking-wide text-neutral-500">Plat Nomor</dt>
                                <dd class="mt-1 font-semibold text-neutral-900">{{ $mobil->plat_nomor }}</dd>
                            </div>
                            <div class="rounded-2xl bg-neutral-50 p-4">
                                <dt class="text-xs uppercase tracking-wide text-neutral-500">Status</dt>
                                <dd class="mt-1 font-semibold text-neutral-900">{{ ucfirst($mobil->status) }}</dd>
                            </div>
                            <div class="rounded-2xl bg-neutral-50 p-4 sm:col-span-2">
                                <dt class="text-xs uppercase tracking-wide text-neutral-500">Harga Sewa</dt>
                                <dd class="mt-1 text-2xl font-semibold text-neutral-900">Rp {{ number_format($mobil->harga_sewa_per_hari, 0, ',', '.') }} / hari</dd>
                            </div>
                        </dl>

                        <a href="{{ route('booking.create') }}" class="inline-flex rounded-xl bg-amber-500 px-5 py-3 text-sm font-semibold text-white hover:bg-amber-600">
                            Pesan Sekarang
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
