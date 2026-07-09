<x-admin-layout>
    <x-slot name="title">Edit Kategori</x-slot>
    <x-slot name="subtitle">Perbarui data kategori {{ $kategori->nama_kategori }}</x-slot>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 sm:p-8 max-w-lg">
        <form action="{{ route('admin.kategori.update', $kategori) }}" method="POST">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Nama Kategori</label>
                <input type="text" name="nama_kategori" value="{{ old('nama_kategori', $kategori->nama_kategori) }}"
                       class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 focus:border-amber-400 focus:ring-amber-400 focus:outline-none focus:ring-1"
                       autofocus>
                @error('nama_kategori') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-3 mt-8">
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition-colors">
                    Simpan Perubahan
                </button>
                <a href="{{ route('admin.kategori.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-sm font-semibold hover:bg-slate-50 transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>
</x-admin-layout>
