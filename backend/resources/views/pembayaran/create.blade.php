<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-neutral-800 leading-tight">Catat Pembayaran</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-2xl border">
                <form method="POST" action="{{ route('pembayaran.store') }}">
                    @csrf
                    <input type="hidden" name="booking_id" value="{{ $booking->id ?? '' }}">

                    <div class="mb-3">
                        <label class="block text-sm">Metode Bayar</label>
                        <input name="metode_bayar" class="mt-1 block w-full border rounded p-2">
                    </div>
                    <div class="mb-3">
                        <label class="block text-sm">Jumlah</label>
                        <input name="jumlah_bayar" type="number" class="mt-1 block w-full border rounded p-2">
                    </div>

                    <button class="px-4 py-2 bg-amber-500 text-white rounded">Simpan</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
