<x-admin-layout>
    <x-slot name="title">Tambah Booking</x-slot>
    <x-slot name="subtitle">Buat transaksi penyewaan baru</x-slot>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 sm:p-8 max-w-3xl">
        <form action="{{ route('admin.booking.store') }}" method="POST">
            @csrf
            @include('admin.booking._form')

            <div class="flex items-center gap-3 mt-8">
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition-colors">
                    Simpan
                </button>
                <a href="{{ route('admin.booking.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-sm font-semibold hover:bg-slate-50 transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>
</x-admin-layout>
