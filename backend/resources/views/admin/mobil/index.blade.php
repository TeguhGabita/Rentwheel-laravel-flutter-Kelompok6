<x-admin-layout>
    <x-slot name="title">Data Mobil</x-slot>
    <x-slot name="subtitle">Kelola armada mobil RentWheel</x-slot>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <form method="GET" class="w-full sm:max-w-xs">
            <div class="relative">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, merk, atau plat nomor..."
                       class="w-full pl-9 pr-3 py-2.5 text-sm rounded-xl border border-slate-200 focus:border-amber-400 focus:ring-amber-400 focus:outline-none focus:ring-1">
            </div>
        </form>

        <a href="{{ route('admin.mobil.create') }}"
           class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition-colors whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            Tambah Mobil
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 text-slate-500 uppercase text-xs tracking-wide">
                    <tr>
                        <th class="px-6 py-3 font-semibold">Mobil</th>
                        <th class="px-6 py-3 font-semibold">Kategori</th>
                        <th class="px-6 py-3 font-semibold">Plat Nomor</th>
                        <th class="px-6 py-3 font-semibold">Harga / Hari</th>
                        <th class="px-6 py-3 font-semibold">Status</th>
                        <th class="px-6 py-3 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($mobils as $mobil)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center overflow-hidden flex-shrink-0">
                                        @if ($mobil->foto)
                                            <img src="{{ asset('storage/'.$mobil->foto) }}" alt="{{ $mobil->nama_mobil }}" class="w-full h-full object-cover">
                                        @else
                                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 0h-12"/>
                                            </svg>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-semibold text-slate-900">{{ $mobil->nama_mobil }}</p>
                                        <p class="text-xs text-slate-500">{{ $mobil->merk }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-600">{{ $mobil->kategori->nama_kategori ?? '-' }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $mobil->plat_nomor }}</td>
                            <td class="px-6 py-4 text-slate-600">Rp{{ number_format($mobil->harga_sewa_per_hari, 0, ',', '.') }}</td>
                            <td class="px-6 py-4">
                                @php
                                    $badge = [
                                        'tersedia' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'disewa' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'servis' => 'bg-slate-100 text-slate-600 border-slate-200',
                                    ][$mobil->status] ?? 'bg-slate-100 text-slate-600 border-slate-200';
                                @endphp
                                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium border {{ $badge }} capitalize">{{ $mobil->status }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('admin.mobil.edit', $mobil) }}" class="text-slate-500 hover:text-amber-500 font-medium text-xs">Edit</a>
                                    <form action="{{ route('admin.mobil.destroy', $mobil) }}" method="POST"
                                          onsubmit="return confirm('Yakin ingin menghapus data mobil {{ $mobil->nama_mobil }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-slate-500 hover:text-red-500 font-medium text-xs">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                Belum ada data mobil{{ request('search') ? ' yang cocok dengan pencarian.' : '.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($mobils->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $mobils->links() }}
            </div>
        @endif
    </div>
</x-admin-layout>
