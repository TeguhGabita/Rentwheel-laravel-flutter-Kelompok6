<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-neutral-800 leading-tight">Edit Mobil</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('mobil.update', $mobil) }}">
                @csrf
                @method('PUT')
                <div class="bg-white p-6 rounded-2xl border space-y-4">
                    <div>
                        <label class="block text-sm">Nama Mobil</label>
                        <input name="nama_mobil" value="{{ old('nama_mobil', $mobil->nama_mobil) }}" class="mt-1 block w-full border rounded p-2" required>
                    </div>

                    <div>
                        <label class="block text-sm">Kategori</label>
                        <select name="kategori_id" class="mt-1 block w-full border rounded p-2" required>
                            <option value="">Pilih kategori</option>
                            @foreach($kategoris as $k)
                                <option value="{{ $k->id }}" {{ $mobil->kategori_id == $k->id ? 'selected' : '' }}>{{ $k->nama_kategori }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm">Merk</label>
                        <input name="merk" value="{{ old('merk', $mobil->merk) }}" class="mt-1 block w-full border rounded p-2">
                    </div>

                    <div>
                        <label class="block text-sm">Plat Nomor</label>
                        <input name="plat_nomor" value="{{ old('plat_nomor', $mobil->plat_nomor) }}" class="mt-1 block w-full border rounded p-2" required>
                    </div>

                    <div>
                        <label class="block text-sm">Harga sewa per hari</label>
                        <input name="harga_sewa_per_hari" value="{{ old('harga_sewa_per_hari', $mobil->harga_sewa_per_hari) }}" type="number" step="0.01" class="mt-1 block w-full border rounded p-2" required>
                    </div>

                    <div>
                        <label class="block text-sm">Status</label>
                        <select name="status" class="mt-1 block w-full border rounded p-2">
                            <option value="tersedia" {{ $mobil->status == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                            <option value="disewa" {{ $mobil->status == 'disewa' ? 'selected' : '' }}>Disewa</option>
                            <option value="servis" {{ $mobil->status == 'servis' ? 'selected' : '' }}>Servis</option>
                        </select>
                    </div>

                    <div class="flex justify-end">
                        <button class="px-4 py-2 bg-amber-500 text-white rounded">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
