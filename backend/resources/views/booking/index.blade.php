<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-neutral-800 leading-tight">Booking</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-2xl border">
                <h3 class="font-semibold mb-4">Daftar Booking</h3>

                @if($bookings->count())
                    <ul class="space-y-3">
                        @foreach($bookings as $b)
                            <li class="p-3 border rounded-md flex flex-wrap justify-between items-center gap-3">
                                <div>
                                    <div class="font-medium">{{ $b->mobil->nama_mobil ?? '–' }}</div>
                                    <div class="text-sm text-neutral-500">{{ $b->tanggal_mulai }} — {{ $b->tanggal_selesai }}</div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('booking.show', $b) }}" class="text-amber-600 font-semibold">Lihat</a>
                                    <form action="{{ route('booking.cancel', $b) }}" method="POST" onsubmit="return confirm('Batalkan booking ini? Data booking akan dihapus.')" class="inline">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center justify-center px-3 py-2 bg-red-500 text-white rounded-lg text-sm font-semibold hover:bg-red-600 transition">
                                            Batal Booking
                                        </button>
                                    </form>
                                </div>
                            </li>
                        @endforeach
                    </ul>

                    <div class="mt-4">
                        {{ $bookings->links() }}
                    </div>
                @else
                    <div class="text-center text-neutral-500">Belum ada booking.</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
