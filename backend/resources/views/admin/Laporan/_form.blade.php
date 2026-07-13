@php
    $b = $booking ?? null;
@endphp

<div class="grid grid-cols-1 sm:grid-cols-2 gap-5"
     x-data="{
        harga: {{ old('mobil_id', $b->mobil_id ?? '') ? ($mobils->find(old('mobil_id', $b->mobil_id))->harga_sewa_per_hari ?? 0) : 0 }},
        mulai: '{{ old('tanggal_mulai', $b->tanggal_mulai ?? '') }}',
        selesai: '{{ old('tanggal_selesai', $b->tanggal_selesai ?? '') }}',
        get totalHari() {
            if (!this.mulai || !this.selesai) return 0;
            const d1 = new Date(this.mulai), d2 = new Date(this.selesai);
            const diff = Math.round((d2 - d1) / 86400000) + 1;
            return diff > 0 ? diff : 0;
        },
        get totalHarga() { return this.harga * this.totalHari; }
     }">
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Mobil</label>
        <select name="mobil_id" x-on:change="harga = $event.target.selectedOptions[0].dataset.harga || 0"
                class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 focus:border-amber-400 focus:ring-amber-400 focus:outline-none focus:ring-1">
            <option value="">-- Pilih Mobil --</option>
            @foreach ($mobils as $mobil)
                <option value="{{ $mobil->id }}" data-harga="{{ $mobil->harga_sewa_per_hari }}"
                        @selected(old('mobil_id', $b->mobil_id ?? '') == $mobil->id)>
                    {{ $mobil->nama_mobil }} — Rp{{ number_format($mobil->harga_sewa_per_hari, 0, ',', '.') }}/hari
                </option>
            @endforeach
        </select>
        @error('mobil_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Pelanggan</label>
        <select name="user_id" class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 focus:border-amber-400 focus:ring-amber-400 focus:outline-none focus:ring-1">
            <option value="">-- Pilih Pelanggan --</option>
            @foreach ($pelanggans as $pelanggan)
                <option value="{{ $pelanggan->id }}" @selected(old('user_id', $b->user_id ?? '') == $pelanggan->id)>
                    {{ $pelanggan->name }} ({{ $pelanggan->email }})
                </option>
            @endforeach
        </select>
        @error('user_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Tanggal Mulai</label>
        <input type="date" name="tanggal_mulai" x-model="mulai" value="{{ old('tanggal_mulai', $b->tanggal_mulai ?? '') }}"
               class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 focus:border-amber-400 focus:ring-amber-400 focus:outline-none focus:ring-1">
        @error('tanggal_mulai') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Tanggal Selesai</label>
        <input type="date" name="tanggal_selesai" x-model="selesai" value="{{ old('tanggal_selesai', $b->tanggal_selesai ?? '') }}"
               class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 focus:border-amber-400 focus:ring-amber-400 focus:outline-none focus:ring-1">
        @error('tanggal_selesai') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Status</label>
        <select name="status" class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 focus:border-amber-400 focus:ring-amber-400 focus:outline-none focus:ring-1">
            @foreach (['dipesan', 'berjalan', 'selesai', 'batal'] as $status)
                <option value="{{ $status }}" @selected(old('status', $b->status ?? 'dipesan') == $status)>
                    {{ ucfirst($status) }}
                </option>
            @endforeach
        </select>
        @error('status') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Estimasi Total Harga</label>
        <div class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 bg-slate-50 text-slate-600">
            <span x-text="totalHari"></span> hari &times; Rp<span x-text="harga.toLocaleString('id-ID')"></span>
            = <strong>Rp<span x-text="totalHarga.toLocaleString('id-ID')"></span></strong>
        </div>
        <p class="text-xs text-slate-400 mt-1">Total harga dihitung otomatis oleh sistem saat disimpan.</p>
    </div>
</div>
