<x-admin-layout>
    <x-slot name="title">Pembayaran</x-slot>
    <x-slot name="subtitle">Kelola pembayaran dari transaksi penyewaan (fitur bonus)</x-slot>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <form method="GET" class="w-full sm:max-w-xs">
            <div class="relative">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari mobil atau metode bayar..."
                       class="w-full pl-9 pr-3 py-2.5 text-sm rounded-xl border border-slate-200 focus:border-amber-400 focus:ring-amber-400 focus:outline-none focus:ring-1">
            </div>
        </form>

        <a href="{{ route('admin.pembayaran.create') }}"
           class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition-colors whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            Tambah Pembayaran
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 text-slate-500 uppercase text-xs tracking-wide">
                    <tr>
                        <th class="px-6 py-3 font-semibold">Booking</th>
                        <th class="px-6 py-3 font-semibold">Tanggal Bayar</th>
                        <th class="px-6 py-3 font-semibold">Metode</th>
                        <th class="px-6 py-3 font-semibold">Jumlah</th>
                        <th class="px-6 py-3 font-semibold">Status</th>
                        <th class="px-6 py-3 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($pembayarans as $pembayaran)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="px-6 py-4">
                                <p class="font-semibold text-slate-900">{{ $pembayaran->booking->mobil->nama_mobil ?? '-' }}</p>
                                <p class="text-xs text-slate-500">{{ $pembayaran->booking->user->name ?? '-' }}</p>
                            </td>
                            <td class="px-6 py-4 text-slate-600">{{ \Carbon\Carbon::parse($pembayaran->tanggal_bayar)->format('d M Y') }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $pembayaran->metode_bayar }}</td>
                            <td class="px-6 py-4 text-slate-600">Rp{{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}</td>
                            <td class="px-6 py-4">
                                @php
                                    $badge = [
                                        'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'lunas' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'gagal' => 'bg-red-50 text-red-700 border-red-200',
                                    ][$pembayaran->status_bayar] ?? 'bg-slate-100 text-slate-600 border-slate-200';
                                @endphp
                                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium border {{ $badge }} capitalize">{{ $pembayaran->status_bayar }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('admin.pembayaran.edit', $pembayaran) }}" class="text-slate-500 hover:text-amber-500 font-medium text-xs">Edit</a>
                                    <form action="{{ route('admin.pembayaran.destroy', $pembayaran) }}" method="POST"
                                          onsubmit="return confirm('Yakin ingin menghapus data pembayaran ini?');">
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
                                Belum ada data pembayaran{{ request('search') ? ' yang cocok dengan pencarian.' : '.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($pembayarans->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $pembayarans->links() }}
            </div>
        @endif
    </div>
</x-admin-layout>
