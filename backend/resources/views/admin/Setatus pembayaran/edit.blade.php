<x-admin-layout>
    <x-slot name="title">Edit Pembayaran</x-slot>
    <x-slot name="subtitle">Perbarui data pembayaran #{{ $pembayaran->id }}</x-slot>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 sm:p-8 max-w-3xl">
        <form action="{{ route('admin.pembayaran.update', $pembayaran) }}" method="POST">
            @csrf
            @method('PUT')
            @include('admin.pembayaran._form')

            <div class="flex items-center gap-3 mt-8">
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition-colors">
                    Simpan Perubahan
                </button>
                <a href="{{ route('admin.pembayaran.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-sm font-semibold hover:bg-slate-50 transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>
</x-admin-layout>
