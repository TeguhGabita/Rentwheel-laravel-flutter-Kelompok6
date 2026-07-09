<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-neutral-800 leading-tight">Mobil</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-2xl font-semibold text-slate-900">Mobil</h3>
                    <p class="text-sm text-slate-500 mt-1">Pilih mobil yang tersedia untuk disewa.</p>
                </div>
                @if(auth()->user()->hasRole('admin'))
                    <a href="{{ route('mobil.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-amber-500 text-white rounded-lg shadow-sm hover:bg-amber-600 transition">
                        Tambah Mobil
                    </a>
                @endif
            </div>

            @if($mobils->count())
                <div class="space-y-4">
                    @foreach($mobils as $mobil)
                        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                            <div class="grid grid-cols-1 lg:grid-cols-[1.5fr_1fr] gap-4 p-6">
                                <div class="space-y-3">
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <h4 class="text-xl font-semibold text-slate-900">{{ $mobil->nama_mobil }}</h4>
                                            <p class="text-sm text-slate-500">{{ $mobil->kategori->nama ?? 'Tanpa kategori' }}</p>
                                        </div>
                                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $mobil->status === 'tersedia' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                            {{ ucfirst($mobil->status) }}
                                        </span>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4 text-sm text-slate-600">
                                        <div>
                                            <div class="text-[10px] uppercase tracking-[0.18em] text-slate-400">Plat Nomor</div>
                                            <div class="mt-1 text-base text-slate-900">{{ $mobil->plat_nomor }}</div>
                                        </div>
                                        <div>
                                            <div class="text-[10px] uppercase tracking-[0.18em] text-slate-400">Merk</div>
                                            <div class="mt-1 text-base text-slate-900">{{ $mobil->merk }}</div>
                                        </div>
                                        <div>
                                            <div class="text-[10px] uppercase tracking-[0.18em] text-slate-400">Harga / hari</div>
                                            <div class="mt-1 text-base text-slate-900">Rp{{ number_format($mobil->harga_sewa_per_hari,0,',','.') }}</div>
                                        </div>
                                        <div>
                                            <div class="text-[10px] uppercase tracking-[0.18em] text-slate-400">Status</div>
                                            <div class="mt-1 text-base text-slate-900">{{ ucfirst($mobil->status) }}</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex flex-col justify-between gap-4">
                                    <div class="flex flex-wrap gap-3">
                                        <a href="{{ route('mobil.show', $mobil) }}" class="inline-flex items-center justify-center min-w-[100px] px-4 py-3 border border-slate-200 rounded-2xl text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                                            Detail
                                        </a>

                                        @if(auth()->user()->hasRole('admin'))
                                            <a href="{{ route('mobil.edit', $mobil) }}" class="inline-flex items-center justify-center min-w-[100px] px-4 py-3 border border-slate-200 rounded-2xl text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                                                Edit
                                            </a>
                                        @endif
                                    </div>

                                    <div class="text-right">
                                        @if($mobil->status === 'tersedia')
                                            <a href="{{ route('booking.create', ['mobil_id' => $mobil->id]) }}" class="inline-flex items-center justify-center w-full px-5 py-3 bg-amber-500 text-white rounded-2xl text-sm font-semibold hover:bg-amber-600 transition">
                                                Sewa
                                            </a>
                                        @else
                                            <button type="button" disabled class="inline-flex items-center justify-center w-full px-5 py-3 bg-slate-200 text-slate-500 rounded-2xl text-sm font-semibold cursor-not-allowed">
                                                Tidak tersedia
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
+                </div>

                <div class="mt-6">
                    {{ $mobils->links() }}
                </div>
            @else
                <div class="bg-white p-10 rounded-3xl border border-slate-200 text-center text-slate-600">
                    Tidak ada mobil tersedia.
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
