@php
    $m = $mobil ?? null;
@endphp

<div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Nama Mobil</label>
        <input type="text" name="nama_mobil" value="{{ old('nama_mobil', $m->nama_mobil ?? '') }}"
               class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 focus:border-amber-400 focus:ring-amber-400 focus:outline-none focus:ring-1"
               placeholder="Contoh: Avanza">
        @error('nama_mobil') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Merk</label>
        <input type="text" name="merk" value="{{ old('merk', $m->merk ?? '') }}"
               class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 focus:border-amber-400 focus:ring-amber-400 focus:outline-none focus:ring-1"
               placeholder="Contoh: Toyota">
        @error('merk') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Kategori</label>
        <select name="kategori_id" class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 focus:border-amber-400 focus:ring-amber-400 focus:outline-none focus:ring-1">
            <option value="">-- Pilih Kategori --</option>
            @foreach ($kategoris as $kategori)
                <option value="{{ $kategori->id }}" @selected(old('kategori_id', $m->kategori_id ?? '') == $kategori->id)>
                    {{ $kategori->nama_kategori }}
                </option>
            @endforeach
        </select>
        @error('kategori_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Plat Nomor</label>
        <input type="text" name="plat_nomor" value="{{ old('plat_nomor', $m->plat_nomor ?? '') }}"
               class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 focus:border-amber-400 focus:ring-amber-400 focus:outline-none focus:ring-1"
               placeholder="Contoh: D 1234 ABC">
        @error('plat_nomor') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Harga Sewa / Hari (Rp)</label>
        <input type="number" step="1000" min="0" name="harga_sewa_per_hari" value="{{ old('harga_sewa_per_hari', $m->harga_sewa_per_hari ?? '') }}"
               class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 focus:border-amber-400 focus:ring-amber-400 focus:outline-none focus:ring-1"
               placeholder="Contoh: 300000">
        @error('harga_sewa_per_hari') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Status</label>
        <select name="status" class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 focus:border-amber-400 focus:ring-amber-400 focus:outline-none focus:ring-1">
            @foreach (['tersedia', 'disewa', 'servis'] as $status)
                <option value="{{ $status }}" @selected(old('status', $m->status ?? 'tersedia') == $status)>
                    {{ ucfirst($status) }}
                </option>
            @endforeach
        </select>
        @error('status') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Foto Mobil</label>
        @if ($m && $m->foto)
            <img src="{{ asset('storage/'.$m->foto) }}" class="w-24 h-24 object-cover rounded-lg mb-2 border border-slate-200">
        @endif
        <input type="file" name="foto" accept="image/*"
               class="w-full text-sm text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
        <p class="text-xs text-slate-400 mt-1">Format gambar, maksimal 2MB. Kosongkan jika tidak ingin mengubah foto.</p>
        @error('foto') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>
</div>
