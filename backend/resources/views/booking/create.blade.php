<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-neutral-800 leading-tight">Buat Booking</h2>
                <p class="text-sm text-neutral-500 mt-1">Isi detail pemesanan mobil Anda.</p>
            </div>
            <a href="{{ route('mobil.index') }}" class="rounded-xl border border-neutral-200 px-4 py-2 text-sm font-medium text-neutral-700 hover:bg-neutral-50">
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="rounded-3xl border border-neutral-200 bg-white p-8 shadow-sm">
                <form action="{{ route('booking.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <div class="rounded-2xl bg-neutral-50 p-4">
                        <p class="text-sm text-neutral-500">Mobil yang dipilih</p>
                        <p class="mt-1 text-lg font-semibold text-neutral-900">
                            {{ $mobil->nama_mobil ?? 'Pilih mobil terlebih dahulu' }}
                        </p>
                        @if ($mobil)
                            <input type="hidden" name="mobil_id" value="{{ $mobil->id }}">
                        @endif
                    </div>

                    <div>
                        <label for="mobil_id" class="block text-sm font-medium text-neutral-700">Pilih Mobil</label>
                        <select name="mobil_id" id="mobil_id" class="mt-2 w-full rounded-xl border border-neutral-200 px-4 py-3 text-sm focus:border-amber-400 focus:outline-none focus:ring-1 focus:ring-amber-400">
                            <option value="">-- Pilih mobil --</option>
                            @php
                                $mobils = App\Models\Mobil::where('status', 'tersedia')->get();
                            @endphp
                            @foreach ($mobils as $item)
                                <option value="{{ $item->id }}" {{ old('mobil_id') == $item->id || ($mobil && $mobil->id == $item->id) ? 'selected' : '' }}>
                                    {{ $item->nama_mobil }} - {{ $item->merk }}
                                </option>
                            @endforeach
                        </select>
                        @error('mobil_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <label for="tanggal_mulai" class="block text-sm font-medium text-neutral-700">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" id="tanggal_mulai" value="{{ old('tanggal_mulai') }}" required
                                   class="mt-2 w-full rounded-xl border border-neutral-200 px-4 py-3 text-sm focus:border-amber-400 focus:outline-none focus:ring-1 focus:ring-amber-400">
                            @error('tanggal_mulai')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="tanggal_selesai" class="block text-sm font-medium text-neutral-700">Tanggal Selesai</label>
                            <input type="date" name="tanggal_selesai" id="tanggal_selesai" value="{{ old('tanggal_selesai') }}" required
                                   class="mt-2 w-full rounded-xl border border-neutral-200 px-4 py-3 text-sm focus:border-amber-400 focus:outline-none focus:ring-1 focus:ring-amber-400">
                            @error('tanggal_selesai')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-neutral-700 mb-2">Metode Pembayaran</label>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="flex items-center gap-3 rounded-xl border border-neutral-200 px-4 py-3 text-sm cursor-pointer has-[:checked]:border-amber-400 has-[:checked]:bg-amber-50">
                                <input type="radio" name="metode_pembayaran" value="tunai"
                                       {{ old('metode_pembayaran') == 'tunai' ? 'checked' : '' }}
                                       class="text-amber-500 focus:ring-amber-400" required>
                                <span>Tunai</span>
                            </label>
                            <label class="flex items-center gap-3 rounded-xl border border-neutral-200 px-4 py-3 text-sm cursor-pointer has-[:checked]:border-amber-400 has-[:checked]:bg-amber-50">
                                <input type="radio" name="metode_pembayaran" value="virtual"
                                       {{ old('metode_pembayaran') == 'virtual' ? 'checked' : '' }}
                                       class="text-amber-500 focus:ring-amber-400" required>
                                <span>Virtual (Transfer/Online)</span>
                            </label>
                        </div>
                        @error('metode_pembayaran')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="rounded-xl bg-amber-500 px-5 py-3 text-sm font-semibold text-white hover:bg-amber-600">
                            Simpan Booking
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>