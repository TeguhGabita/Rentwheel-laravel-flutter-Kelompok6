<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-neutral-800 leading-tight">Buat Booking</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-2xl border">
                <h3 class="font-semibold mb-4">Form Booking</h3>

                <form method="POST" action="{{ route('booking.store') }}">
                    @csrf
                    <input type="hidden" name="mobil_id" value="{{ $mobil->id ?? '' }}">

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm">Tanggal mulai</label>
                            <input type="date" name="tanggal_mulai" class="mt-1 block w-full border rounded p-2" required>
                        </div>
                        <div>
                            <label class="block text-sm">Tanggal selesai</label>
                            <input type="date" name="tanggal_selesai" class="mt-1 block w-full border rounded p-2" required>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button class="px-4 py-2 bg-amber-500 text-white rounded">Pesan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
