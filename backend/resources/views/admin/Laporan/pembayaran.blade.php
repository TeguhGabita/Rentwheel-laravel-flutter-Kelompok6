<x-admin-layout>
    <x-slot name="title">Laporan Pembayaran</x-slot>
    <x-slot name="subtitle">Lihat laporan pembayaran pemesanan mobil</x-slot>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <form method="GET" class="w-full sm:max-w-md flex gap-2">
            <div class="relative flex-1">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari mobil atau pelanggan..."
                       class="w-full pl-9 pr-3 py-2.5 text-sm rounded-xl border border-slate-200 focus:border-amber-400 focus:ring-amber-400 focus:outline-none focus:ring-1">
            </div>
            <select name="status" class="px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 focus:border-amber-400 focus:ring-amber-400 focus:outline-none focus:ring-1">
                <option value="">Semua Status</option>
                <option value="pending" @selected(request('status') == 'pending')>Pending</option>
                <option value="confirmed" @selected(request('status') == 'confirmed')>Confirmed</option>
                <option value="failed" @selected(request('status') == 'failed')>Failed</option>
            </select>
        </form>

        <a href="{{ route('admin.laporan.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition-colors whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali ke Booking
        </a>
    </div>

    @if (session('status'))
        <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm">
            {{ session('status') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 text-slate-500 uppercase text-xs tracking-wide">
                    <tr>
                        <th class="px-6 py-3 font-semibold">Mobil</th>
                        <th class="px-6 py-3 font-semibold">Pelanggan</th>
                        <th class="px-6 py-3 font-semibold">Jumlah Pembayaran</th>
                        <th class="px-6 py-3 font-semibold">Metode</th>
                        <th class="px-6 py-3 font-semibold">Tanggal Pembayaran</th>
                        <th class="px-6 py-3 font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($pembayarans as $pembayaran)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-900">{{ $pembayaran->booking->mobil->nama_mobil ?? '-' }}</div>
                                <div class="text-xs text-slate-500">{{ $pembayaran->booking->mobil->plat_nomor ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-900">{{ $pembayaran->booking->user->name ?? '-' }}</div>
                                <div class="text-xs text-slate-500">{{ $pembayaran->booking->user->email ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 font-medium text-slate-900">
                                Rp {{ number_format($pembayaran->jumlah, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                                    {{ ucfirst($pembayaran->metode ?? 'N/A') }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                {{ $pembayaran->tanggal_pembayaran ? \Carbon\Carbon::parse($pembayaran->tanggal_pembayaran)->format('d/m/Y H:i') : '-' }}
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-yellow-100 text-yellow-700',
                                        'confirmed' => 'bg-green-100 text-green-700',
                                        'failed' => 'bg-red-100 text-red-700',
                                    ];
                                @endphp
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold {{ $statusColors[$pembayaran->status] ?? 'bg-slate-100 text-slate-700' }}">
                                    {{ ucfirst($pembayaran->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-500">
                                Tidak ada data pembayaran.
                            </td>
                        </tr>
                    @endempty
                </tbody>
            </table>
        </div>
    </div>

    @if ($pembayarans->hasPages())
        <div class="mt-6">
            {{ $pembayarans->links() }}
        </div>
    @endif
</x-admin-layout>
