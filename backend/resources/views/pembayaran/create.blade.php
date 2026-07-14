<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-neutral-800 leading-tight">Bayar Booking</h2>
                <p class="text-sm text-neutral-500 mt-1">Pilih metode pembayaran dan unggah bukti transfer.</p>
            </div>
            <a href="{{ route('pembayaran.index') }}" class="rounded-xl border border-neutral-200 px-4 py-2 text-sm font-medium text-neutral-700 hover:bg-neutral-50">
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

            @if ($errors->any())
                <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if ($booking)
                <div class="rounded-3xl border border-amber-200 bg-gradient-to-br from-amber-500 to-orange-600 p-6 shadow-sm mb-6 text-white">
                    <p class="text-sm font-medium text-amber-100">
                        {{ $booking->mobil->nama_mobil ?? 'Mobil' }}
                    </p>
                    <h3 class="mt-1 text-lg font-semibold text-white">
                        {{ $booking->mobil->merk ?? '-' }} &mdash; {{ $booking->mobil->plat_nomor ?? '-' }}
                    </h3>
                    <p class="mt-2 text-sm text-amber-100">
                        Total yang harus dibayar
                    </p>
                    <p class="text-2xl font-bold text-white mt-1">
                        Rp {{ number_format($booking->total_harga ?? 0, 0, ',', '.') }}
                    </p>
                </div>
            @endif

            <form action="{{ route('pembayaran.store') }}" method="POST" enctype="multipart/form-data" class="rounded-3xl border border-neutral-200 bg-white p-6 shadow-sm space-y-6">
                @csrf

                <input type="hidden" name="booking_id" value="{{ $booking->id ?? old('booking_id') }}">
                <input type="hidden" name="jumlah_bayar" value="{{ $booking->total_harga ?? old('jumlah_bayar') }}">

                {{-- Pilihan Metode Pembayaran --}}
                <div>
                    <label class="block text-sm font-semibold text-neutral-800 mb-3">Pilih Metode Pembayaran</label>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">

                        <label class="cursor-pointer metode-option">
                            <input type="radio" name="metode_bayar" value="transfer_bank" class="peer sr-only metode-radio" checked>
                            <div class="rounded-2xl border-2 border-neutral-200 peer-checked:border-amber-500 peer-checked:bg-amber-50 peer-checked:shadow-md p-4 text-center transition-all">
                                <div class="w-11 h-11 mx-auto mb-2 rounded-xl bg-amber-100 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3.75 21h16.5M4.5 3h15l.75 4.5H3.75L4.5 3zM4.5 10.5v10.5m3.75-10.5v10.5m4.5-10.5v10.5m3.75-10.5v10.5"/>
                                    </svg>
                                </div>
                                <p class="text-sm font-medium text-neutral-800">Transfer Bank</p>
                            </div>
                        </label>

                        <label class="cursor-pointer metode-option">
                            <input type="radio" name="metode_bayar" value="e_wallet" class="peer sr-only metode-radio">
                            <div class="rounded-2xl border-2 border-neutral-200 peer-checked:border-amber-500 peer-checked:bg-amber-50 peer-checked:shadow-md p-4 text-center transition-all">
                                <div class="w-11 h-11 mx-auto mb-2 rounded-xl bg-amber-100 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 12a2.25 2.25 0 00-2.25-2.25H15a3 3 0 11-6 0H5.25A2.25 2.25 0 003 12m18 0v6a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 18v-6m18 0V9A2.25 2.25 0 0018.75 6.75H5.25A2.25 2.25 0 003 9v3"/>
                                    </svg>
                                </div>
                                <p class="text-sm font-medium text-neutral-800">E-Wallet</p>
                            </div>
                        </label>

                        <label class="cursor-pointer metode-option">
                            <input type="radio" name="metode_bayar" value="qris" class="peer sr-only metode-radio">
                            <div class="rounded-2xl border-2 border-neutral-200 peer-checked:border-amber-500 peer-checked:bg-amber-50 peer-checked:shadow-md p-4 text-center transition-all">
                                <div class="w-11 h-11 mx-auto mb-2 rounded-xl bg-amber-100 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3.75 4.5h4.5v4.5h-4.5v-4.5zM15.75 4.5h4.5v4.5h-4.5v-4.5zM3.75 15.75h4.5v4.5h-4.5v-4.5zM15.75 15.75h1.5v1.5h-1.5v-1.5zM18.75 15.75h1.5v1.5h-1.5v-1.5zM15.75 18.75h1.5v1.5h-1.5v-1.5zM18.75 18.75h1.5v1.5h-1.5v-1.5z"/>
                                    </svg>
                                </div>
                                <p class="text-sm font-medium text-neutral-800">QRIS</p>
                            </div>
                        </label>

                    </div>

                    {{-- Info Transfer Bank --}}
                    <div id="info-transfer_bank" class="metode-info mt-4 rounded-2xl bg-amber-50 border border-amber-200 p-4 text-sm text-neutral-700 space-y-1">
                        <p class="font-semibold text-amber-800">Transfer ke rekening berikut:</p>
                        <p>Bank BCA &mdash; 1234567890 a.n. RentWheel Indonesia</p>
                        <p>Bank Mandiri &mdash; 0987654321 a.n. RentWheel Indonesia</p>
                    </div>

                    {{-- Info E-Wallet --}}
                    <div id="info-e_wallet" class="metode-info hidden mt-4 rounded-2xl bg-amber-50 border border-amber-200 p-4 text-sm text-neutral-700 space-y-1">
                        <p class="font-semibold text-amber-800">Kirim ke nomor e-wallet berikut:</p>
                        <p>GoPay / OVO / DANA &mdash; 0812-3456-7890 a.n. RentWheel</p>
                    </div>

                    {{-- Info QRIS --}}
                    <div id="info-qris" class="metode-info hidden mt-4 rounded-2xl bg-amber-50 border border-amber-200 p-4 text-sm text-neutral-700 text-center space-y-2">
                        <p class="font-semibold text-amber-800">Scan QRIS berikut untuk membayar:</p>
                        <div class="w-40 h-40 mx-auto rounded-xl bg-white border-2 border-amber-300 flex items-center justify-center text-xs text-neutral-400">
                            QR Code
                        </div>
                        <p class="text-xs text-neutral-500">Berlaku untuk semua aplikasi pembayaran QRIS.</p>
                    </div>
                </div>

                {{-- Upload Bukti --}}
                <div>
                    <label for="bukti_pembayaran" class="block text-sm font-semibold text-neutral-800 mb-2">
                        Unggah Bukti Pembayaran
                    </label>
                    <input type="file" id="bukti_pembayaran" name="bukti_pembayaran" accept=".jpg,.jpeg,.png,.pdf"
                           class="block w-full text-sm text-neutral-600 rounded-xl border border-neutral-200 p-3 file:mr-4 file:rounded-lg file:border-0 file:bg-amber-100 file:px-4 file:py-2 file:text-sm file:font-medium file:text-amber-700 hover:file:bg-amber-200">
                    <p class="mt-1 text-xs text-neutral-400">Format JPG, PNG, atau PDF. Maksimal 2MB.</p>
                </div>

                <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-semibold py-3 transition-all shadow-sm">
                    Kirim Bukti Pembayaran
                </button>

            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const radios = document.querySelectorAll('.metode-radio');
            const infos = document.querySelectorAll('.metode-info');

            function updateInfo() {
                const selected = document.querySelector('.metode-radio:checked');
                if (!selected) return;

                infos.forEach(function (info) {
                    if (info.id === 'info-' + selected.value) {
                        info.classList.remove('hidden');
                    } else {
                        info.classList.add('hidden');
                    }
                });
            }

            radios.forEach(function (radio) {
                radio.addEventListener('change', updateInfo);
            });

            updateInfo();
        });
    </script>
</x-app-layout>