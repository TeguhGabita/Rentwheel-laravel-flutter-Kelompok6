<x-admin-layout>
    <x-slot name="title">Laporan Booking</x-slot>
    <x-slot name="subtitle">
        Laporan Booking dan Pembayaran
    </x-slot>

    {{-- Notifikasi sukses update status --}}
    @if (session('status'))
        <div class="mb-6 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm px-4 py-3">
            {{ session('status') }}
        </div>
    @endif

    {{-- Filter Tanggal --}}
    <form method="GET" class="flex flex-col sm:flex-row items-start sm:items-end gap-3 mb-6 bg-white rounded-2xl border p-4">
        <div class="flex flex-col gap-1">
            <label class="text-xs font-medium text-slate-500">Dari Tanggal</label>
            <input type="date" name="tanggal_dari" value="{{ request('tanggal_dari') }}"
                   class="px-3 py-2 text-sm rounded-lg border border-slate-200 focus:border-amber-400 focus:ring-amber-400 focus:outline-none focus:ring-1">
        </div>

        <div class="flex flex-col gap-1">
            <label class="text-xs font-medium text-slate-500">Sampai Tanggal</label>
            <input type="date" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}"
                   class="px-3 py-2 text-sm rounded-lg border border-slate-200 focus:border-amber-400 focus:ring-amber-400 focus:outline-none focus:ring-1">
        </div>

        <button type="submit"
                class="px-5 py-2 bg-slate-900 hover:bg-slate-800 text-white text-sm font-semibold rounded-lg">
            Terapkan
        </button>

        @if (request('tanggal_dari') || request('tanggal_sampai'))
            <a href="{{ route('admin.laporan.index') }}"
               class="px-5 py-2 text-sm font-medium text-slate-500 hover:text-slate-700">
                Reset
            </a>
        @endif
    </form>

    {{-- Tombol Cetak --}}
    <div class="flex justify-end mb-6">
        <button
            type="button"
            id="btnBukaCetak"
            onclick="document.getElementById('cetakForm').classList.toggle('hidden')"
            class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 rounded-xl text-white font-semibold">
            Cetak Laporan
        </button>
    </div>

    {{-- Form Cetak --}}
    <form
        id="cetakForm"
        action="{{ route('admin.laporan.cetak') }}"
        method="POST"
        class="{{ $errors->has('booking_ids') ? '' : 'hidden' }} bg-white rounded-2xl border p-6 mb-6">

        @csrf

        <h3 class="font-semibold text-lg mb-4">
            Pilih Data Booking
        </h3>

        @error('booking_ids')
            <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-600 text-sm px-4 py-3">
                {{ $message }}
            </div>
        @enderror

        <div class="space-y-2 max-h-96 overflow-y-auto">

            @foreach($bookings as $booking)

                <label class="flex items-center p-4 border rounded-lg hover:bg-slate-50">

                    <input
                        type="checkbox"
                        name="booking_ids[]"
                        value="{{ $booking->id }}"
                        {{ in_array($booking->id, old('booking_ids', [])) ? 'checked' : '' }}
                        class="mr-4">

                    <div class="flex-1">

                        <div class="font-semibold">
                            {{ $booking->mobil->nama_mobil }}
                        </div>

                        <div class="text-sm text-slate-500">
                            {{ $booking->user->name }}
                        </div>

                        <div class="text-xs text-slate-400">
                            {{ \Carbon\Carbon::parse($booking->tanggal_mulai)->format('d/m/Y') }}
                            -
                            {{ \Carbon\Carbon::parse($booking->tanggal_selesai)->format('d/m/Y') }}
                        </div>

                    </div>

                    <div class="font-semibold text-amber-600">
                        Rp {{ number_format($booking->total_harga,0,',','.') }}
                    </div>

                </label>

            @endforeach

        </div>

        <div class="flex gap-3 mt-6">

            <button
                type="submit"
                class="px-5 py-2 bg-amber-500 rounded-xl text-white">

                Cetak

            </button>

            <button
                type="button"
                onclick="document.getElementById('cetakForm').classList.add('hidden')"
                class="px-5 py-2 border rounded-xl">

                Batal

            </button>

        </div>

    </form>

    {{-- Tabel --}}
    <div class="bg-white rounded-2xl border overflow-hidden">

        <table class="w-full">

            <thead class="bg-slate-50">

                <tr>

                    <th class="p-4 text-left">Mobil</th>
                    <th class="p-4 text-left">Pelanggan</th>
                    <th class="p-4 text-left">Tanggal</th>
                    <th class="p-4 text-left">Total Booking</th>
                    <th class="p-4 text-left">Pembayaran</th>
                    <th class="p-4 text-left">Status Booking</th>
                    <th class="p-4 text-left">Aksi</th>

                </tr>

            </thead>

            <tbody>

            @forelse($bookings as $booking)

                <tr class="border-t">

                    <td class="p-4">
                        {{ $booking->mobil->nama_mobil }}
                    </td>

                    <td class="p-4">
                        {{ $booking->user->name }}
                    </td>

                    <td class="p-4">
                        {{ \Carbon\Carbon::parse($booking->tanggal_mulai)->format('d/m/Y') }}
                    </td>

                    <td class="p-4">
                        Rp {{ number_format($booking->total_harga,0,',','.') }}
                    </td>

                    <td class="p-4">

                        @if($booking->pembayaran)

                            @php
                                $labelBayar = [
                                    'lunas' => ['Lunas', 'text-green-600'],
                                    'pending' => ['Menunggu Konfirmasi', 'text-amber-600'],
                                    'gagal' => ['Gagal', 'text-red-500'],
                                ][$booking->pembayaran->status_bayar] ?? [ucfirst($booking->pembayaran->status_bayar), 'text-slate-500'];
                            @endphp

                            <span class="font-semibold {{ $labelBayar[1] }}">
                                {{ $labelBayar[0] }}
                            </span>

                            <br>

                            <small>
                                Rp {{ number_format($booking->pembayaran->jumlah_bayar,0,',','.') }}
                            </small>

                            <div class="mt-1">
                                <form action="{{ route('admin.laporan.updateStatusBayar', $booking) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status_bayar" onchange="this.form.submit()"
                                            class="text-xs rounded-lg border border-slate-200 px-2 py-1 focus:border-amber-400 focus:ring-amber-400 focus:outline-none focus:ring-1">
                                        <option value="pending" {{ $booking->pembayaran->status_bayar == 'pending' ? 'selected' : '' }}>Menunggu Konfirmasi</option>
                                        <option value="lunas" {{ $booking->pembayaran->status_bayar == 'lunas' ? 'selected' : '' }}>Lunas</option>
                                        <option value="gagal" {{ $booking->pembayaran->status_bayar == 'gagal' ? 'selected' : '' }}>Gagal</option>
                                    </select>
                                </form>
                            </div>

                        @else

                            <span class="text-red-500">
                                Belum Bayar
                            </span>

                        @endif

                    </td>

                    <td class="p-4">
                        @php
                            $badgeStatus = [
                                'dipesan' => 'bg-amber-50 text-amber-700 border-amber-200',
                                'berjalan' => 'bg-blue-50 text-blue-700 border-blue-200',
                                'selesai' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                'batal' => 'bg-red-50 text-red-600 border-red-200',
                            ][$booking->status] ?? 'bg-slate-100 text-slate-600 border-slate-200';
                        @endphp
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium border {{ $badgeStatus }}">
                            {{ ucfirst($booking->status) }}
                        </span>
                    </td>

                    <td class="p-4">
                        <form action="{{ route('admin.laporan.updateStatus', $booking) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <select name="status" onchange="this.form.submit()"
                                    class="text-xs rounded-lg border border-slate-200 px-2 py-1.5 focus:border-amber-400 focus:ring-amber-400 focus:outline-none focus:ring-1">
                                <option value="dipesan" {{ $booking->status == 'dipesan' ? 'selected' : '' }}>Dipesan</option>
                                <option value="berjalan" {{ $booking->status == 'berjalan' ? 'selected' : '' }}>Berjalan</option>
                                <option value="selesai" {{ $booking->status == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                <option value="batal" {{ $booking->status == 'batal' ? 'selected' : '' }}>Batal</option>
                            </select>
                        </form>
                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="7" class="text-center p-6 text-slate-500">
                        Tidak ada data{{ (request('tanggal_dari') || request('tanggal_sampai')) ? ' pada rentang tanggal ini.' : '.' }}
                    </td>

                </tr>

            @endforelse

            </tbody>

        </table>

    </div>

    <div class="mt-6">
        {{ $bookings->links() }}
    </div>

</x-admin-layout>
