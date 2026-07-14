@php
    $p = $pembayaran ?? null;
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-5">

    <div class="md:col-span-2">
        <label class="block mb-2 font-medium">
            Booking
        </label>

        <select name="booking_id" class="w-full border rounded-lg px-3 py-2">
            <option value="">Pilih Booking</option>

            @foreach($bookings as $booking)
                <option
                    value="{{ $booking->id }}"
                    @selected(old('booking_id',$p->booking_id ?? '')==$booking->id)
                >
                    Booking #{{ $booking->id }}
                    -
                    {{ $booking->user->name }}
                    -
                    {{ $booking->mobil->nama_mobil }}
                    -
                    Rp{{ number_format($booking->total_harga,0,',','.') }}
                </option>
            @endforeach

        </select>

        @error('booking_id')
            <small class="text-red-600">{{ $message }}</small>
        @enderror
    </div>


    <div>
        <label class="block mb-2 font-medium">
            Tanggal Bayar
        </label>

        <input
            type="date"
            name="tanggal_bayar"
            class="w-full border rounded-lg px-3 py-2"
            value="{{ old('tanggal_bayar',$p->tanggal_bayar ?? now()->format('Y-m-d')) }}"
        >

        @error('tanggal_bayar')
            <small class="text-red-600">{{ $message }}</small>
        @enderror
    </div>


    <div>
        <label class="block mb-2 font-medium">
            Metode Pembayaran
        </label>

        <select
            name="metode_bayar"
            class="w-full border rounded-lg px-3 py-2"
        >
            <option value="Transfer Bank">Transfer Bank</option>
            <option value="QRIS">QRIS</option>
            <option value="Cash">Cash</option>
        </select>

        @error('metode_bayar')
            <small class="text-red-600">{{ $message }}</small>
        @enderror
    </div>


    <div>
        <label class="block mb-2 font-medium">
            Jumlah Bayar
        </label>

        <input
            type="number"
            name="jumlah_bayar"
            class="w-full border rounded-lg px-3 py-2"
            value="{{ old('jumlah_bayar',$p->jumlah_bayar ?? '') }}"
        >

        @error('jumlah_bayar')
            <small class="text-red-600">{{ $message }}</small>
        @enderror
    </div>


    <div>
        <label class="block mb-2 font-medium">
            Status Pembayaran
        </label>

        <select
            name="status_bayar"
            class="w-full border rounded-lg px-3 py-2"
        >
            <option value="pending">Pending</option>
            <option value="lunas">Lunas</option>
            <option value="gagal">Gagal</option>
        </select>

        @error('status_bayar')
            <small class="text-red-600">{{ $message }}</small>
        @enderror
    </div>

</div>
