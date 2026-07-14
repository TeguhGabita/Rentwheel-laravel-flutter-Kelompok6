{{-- resources/views/booking/show.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detail Booking #{{ $booking->id }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Informasi Booking</h3>
                <table class="w-full text-sm">
                    <tr>
                        <td class="py-2 pr-4 font-medium text-gray-600 w-48">Status</td>
                        <td class="py-2">
                            <span class="px-2 py-1 rounded text-white text-xs
                                {{ $booking->status == 'selesai' ? 'bg-green-500' : 'bg-yellow-500' }}">
                                {{ ucfirst($booking->status) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="py-2 pr-4 font-medium text-gray-600">Tanggal Mulai</td>
                        <td class="py-2">{{ \Carbon\Carbon::parse($booking->tanggal_mulai)->translatedFormat('d F Y') }}</td>
                    </tr>
                    <tr>
                        <td class="py-2 pr-4 font-medium text-gray-600">Tanggal Selesai</td>
                        <td class="py-2">{{ \Carbon\Carbon::parse($booking->tanggal_selesai)->translatedFormat('d F Y') }}</td>
                    </tr>
                    <tr>
                        <td class="py-2 pr-4 font-medium text-gray-600">Total Harga</td>
                        <td class="py-2">Rp {{ number_format($booking->total_harga, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="py-2 pr-4 font-medium text-gray-600">Metode Pembayaran</td>
                        <td class="py-2">{{ ucfirst($booking->metode_pembayaran ?? '-') }}</td>
                    </tr>
                    <tr>
                        <td class="py-2 pr-4 font-medium text-gray-600">Status Pembayaran</td>
                        <td class="py-2">
                            @if($booking->sudahDibayar())
                                <span class="px-2 py-1 rounded bg-green-500 text-white text-xs">Sudah Dibayar</span>
                            @else
                                <span class="px-2 py-1 rounded bg-red-500 text-white text-xs">Belum Dibayar</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>

            @if($booking->mobil)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Detail Mobil</h3>
                <p><strong>Nama Mobil:</strong> {{ $booking->mobil->nama ?? '-' }}</p>
                <p><strong>Harga Sewa per Hari:</strong> Rp {{ number_format($booking->mobil->harga_sewa_per_hari ?? 0, 0, ',', '.') }}</p>
            </div>
            @endif

            @if($booking->pembayaran)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Detail Pembayaran</h3>
                <p><strong>Tanggal Bayar:</strong> {{ $booking->pembayaran->created_at->format('d/m/Y H:i') }}</p>
            </div>
            @endif

            <a href="{{ route('booking.index') }}"
               class="inline-block bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                Kembali
            </a>
        </div>
    </div>
</x-app-layout>
