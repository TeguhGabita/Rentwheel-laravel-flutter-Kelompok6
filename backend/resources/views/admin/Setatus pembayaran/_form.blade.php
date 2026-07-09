@php
    $p = $pembayaran ?? null;
@endphp

<div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
    <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Booking</label>
        <select name="booking_id" class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 focus:border-amber-400 focus:ring-amber-400 focus:outline-none focus:ring-1">
            <option value="">-- Pilih Booking --</option>
            @foreach ($bookings as $booking)
                <option value="{{ $booking->id }}" @selected(old('booking_id', $p->booking_id ?? '') == $booking->id)>
                    #{{ $booking->id }} — {{ $booking->mobil->nama_mobil ?? '-' }} ({{ $booking->user->name ?? '-' }}) — Rp{{ number_format($booking->total_harga, 0, ',', '.') }}
                </option>
            @endforeach
        </select>
        @error('booking_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Tanggal Bayar</label>
        <input type="date" name="tanggal_bayar" value="{{ old('tanggal_bayar', isset($p->tanggal_bayar) ? \Carbon\Carbon::parse($p->tanggal_bayar)->format('Y-m-d') : now()->format('Y-m-d')) }}"
               class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 focus:border-amber-400 focus:ring-amber-400 focus:outline-none focus:ring-1">
        @error('tanggal_bayar') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Metode Bayar</label>
        <input type="text" name="metode_bayar" value="{{ old('metode_bayar', $p->metode_bayar ?? '') }}"
               class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 focus:border-amber-400 focus:ring-amber-400 focus:outline-none focus:ring-1"
               placeholder="Contoh: Transfer Bank, QRIS, Tunai">
        @error('metode_bayar') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Jumlah Bayar (Rp)</label>
        <input type="number" step="1000" min="0" name="jumlah_bayar" value="{{ old('jumlah_bayar', $p->jumlah_bayar ?? '') }}"
               class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 focus:border-amber-400 focus:ring-amber-400 focus:outline-none focus:ring-1">
        @error('jumlah_bayar') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Status Pembayaran</label>
        <select name="status_bayar" class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 focus:border-amber-400 focus:ring-amber-400 focus:outline-none focus:ring-1">
            @foreach (['pending', 'lunas', 'gagal'] as $status)
                <option value="{{ $status }}" @selected(old('status_bayar', $p->status_bayar ?? 'pending') == $status)>
                    {{ ucfirst($status) }}
                </option>
            @endforeach
        </select>
        @error('status_bayar') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>
</div>
