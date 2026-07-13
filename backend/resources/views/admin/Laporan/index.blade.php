<x-admin-layout>
    <x-slot name="title">Laporan</x-slot>
    <x-slot name="subtitle">Lihat laporan booking dan pembayaran</x-slot>

    <!-- Tab Navigation -->
    <div class="mb-6 border-b border-slate-200">
        <div class="flex gap-4">
            <a href="{{ route('admin.laporan.index') }}"
               class="px-4 py-3 text-sm font-medium border-b-2 {{ request()->routeIs('admin.laporan.index') ? 'border-amber-400 text-amber-600' : 'border-transparent text-slate-600 hover:text-slate-900' }} transition-colors">
                Laporan Booking
            </a>
            <a href="{{ route('admin.laporan.pembayaran') }}"
               class="px-4 py-3 text-sm font-medium border-b-2 {{ request()->routeIs('admin.laporan.pembayaran') ? 'border-amber-400 text-amber-600' : 'border-transparent text-slate-600 hover:text-slate-900' }} transition-colors">
                Laporan Pembayaran
            </a>
        </div>
    </div>

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
                <option value="dipesan" @selected(request('status') == 'dipesan')>Dipesan</option>
                <option value="berjalan" @selected(request('status') == 'berjalan')>Berjalan</option>
                <option value="selesai" @selected(request('status') == 'selesai')>Selesai</option>
                <option value="batal" @selected(request('status') == 'batal')>Batal</option>
            </select>
        </form>

        <button type="button" onclick="document.getElementById('cetakForm').classList.toggle('hidden')" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-amber-500 text-white text-sm font-semibold hover:bg-amber-600 transition-colors whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Cetak Laporan
        </button>
    </div>

    @if (session('status'))
        <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm">
            {{ session('status') }}
        </div>
    @endif

    <form id="cetakForm" class="hidden mb-6 bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-slate-900">Pilih Booking untuk Dicetak</h3>
            <button type="button" onclick="document.getElementById('cetakForm').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form action="{{ route('admin.laporan.cetak') }}" method="POST" class="space-y-2 mb-6 max-h-96 overflow-y-auto p-4 bg-slate-50 rounded-lg">
            @csrf
            @forelse ($bookings as $booking)
                <label class="flex items-center p-3 border border-slate-200 rounded-lg hover:bg-white cursor-pointer transition-colors">
                    <input type="checkbox" name="booking_ids[]" value="{{ $booking->id }}" class="w-4 h-4 text-amber-500 rounded">
                    <span class="ml-3 flex-1">
                        <span class="font-medium text-slate-900">{{ $booking->mobil->nama_mobil }}</span>
                        <span class="text-sm text-slate-500"> - {{ $booking->user->name }}</span>
                        <div class="text-xs text-slate-400">{{ \Carbon\Carbon::parse($booking->tanggal_mulai)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($booking->tanggal_selesai)->format('d/m/Y') }}</div>
                    </span>
                    <span class="text-sm font-semibold text-amber-600">Rp {{ number_format($booking->total_harga, 0, ',', '.') }}</span>
                </label>
            @empty
                <p class="text-sm text-slate-500 text-center py-4">Tidak ada booking yang sesuai.</p>
            @endempty

            @if ($bookings->count() > 0)
                <div class="flex items-center gap-3 mt-6 pt-4 border-t border-slate-200">
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-amber-500 text-white text-sm font-semibold hover:bg-amber-600 transition-colors">
                        Cetak
                    </button>
                    <button type="button" onclick="document.getElementById('cetakForm').classList.add('hidden')" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-sm font-semibold hover:bg-slate-50 transition-colors">
                        Batal
                    </button>
                </div>
            @endif
        </form>
    </form>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 text-slate-500 uppercase text-xs tracking-wide">
                    <tr>
                        <th class="px-6 py-3 font-semibold">Mobil</th>
                        <th class="px-6 py-3 font-semibold">Pelanggan</th>
                        <th class="px-6 py-3 font-semibold">Tanggal Mulai</th>
                        <th class="px-6 py-3 font-semibold">Tanggal Selesai</th>
                        <th class="px-6 py-3 font-semibold">Total Harga</th>
                        <th class="px-6 py-3 font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($bookings as $booking)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-900">{{ $booking->mobil->nama_mobil }}</div>
                                <div class="text-xs text-slate-500">{{ $booking->mobil->plat_nomor }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-900">{{ $booking->user->name }}</div>
                                <div class="text-xs text-slate-500">{{ $booking->user->email }}</div>
                            </td>
                            <td class="px-6 py-4">
                                {{ \Carbon\Carbon::parse($booking->tanggal_mulai)->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4">
                                {{ \Carbon\Carbon::parse($booking->tanggal_selesai)->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 font-medium text-slate-900">
                                Rp {{ number_format($booking->total_harga, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusColors = [
                                        'dipesan' => 'bg-yellow-100 text-yellow-700',
                                        'berjalan' => 'bg-blue-100 text-blue-700',
                                        'selesai' => 'bg-green-100 text-green-700',
                                        'batal' => 'bg-red-100 text-red-700',
                                    ];
                                @endphp
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold {{ $statusColors[$booking->status] ?? 'bg-slate-100 text-slate-700' }}">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-500">
                                Tidak ada data booking.
                            </td>
                        </tr>
                    @endempty
                </tbody>
            </table>
        </div>
    </div>

    @if ($bookings->hasPages())
        <div class="mt-6">
            {{ $bookings->links() }}
        </div>
    @endif
</x-admin-layout>
