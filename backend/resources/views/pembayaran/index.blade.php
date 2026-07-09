<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-neutral-800 leading-tight">Pembayaran</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-2xl border">
                <h3 class="font-semibold mb-4">Daftar Pembayaran</h3>

                @if($pembayarans->count())
                    <ul class="space-y-3">
                        @foreach($pembayarans as $p)
                            <li class="p-3 border rounded-md flex justify-between items-center">
                                <div>
                                    <div class="font-medium">Booking: {{ $p->booking->mobil->nama_mobil ?? '-' }}</div>
                                    <div class="text-sm text-neutral-500">{{ $p->tanggal_bayar }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="font-semibold">Rp{{ number_format($p->jumlah_bayar ?? 0,0,',','.') }}</div>
                                </div>
                            </li>
                        @endforeach
                    </ul>

                    <div class="mt-4">
                        {{ $pembayarans->links() }}
                    </div>
                @else
                    <div class="text-center text-neutral-500">Belum ada pembayaran.</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
