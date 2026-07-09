<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-neutral-800 leading-tight">Detail Mobil</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 bg-white p-6 rounded-2xl border">
            <h1 class="text-2xl font-semibold">{{ $mobil->nama_mobil }}</h1>
            <p class="text-sm text-neutral-500">Merk: {{ $mobil->merk }}</p>
            <p class="mt-4">Kategori: {{ $mobil->kategori->nama ?? '-' }}</p>
            <p class="mt-4">Deskripsi: {{ $mobil->deskripsi ?? 'Tidak ada deskripsi.' }}</p>

            <a href="{{ route('mobil.index') }}" class="inline-block mt-6 text-amber-600">Kembali</a>
        </div>
    </div>
</x-app-layout>
