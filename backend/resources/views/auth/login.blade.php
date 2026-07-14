<x-guest-layout>

<div class="w-full">

    {{-- Logo & nama brand --}}
    <div class="flex flex-col items-center mb-8">
        <div class="inline-flex items-center gap-2.5 bg-white/95 backdrop-blur border border-neutral-200 rounded-full pl-2 pr-4 py-2 shadow-sm">
            <div class="w-8 h-8 rounded-full bg-amber-500 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 16l-2 2m0 0l-2-2m2 2V9a2 2 0 012-2h8a2 2 0 012 2v9m-12 0h12m-2 0l2 2m-2-2l2-2" />
                </svg>
            </div>
            <span class="font-semibold text-neutral-900 text-[15px]">RentWheel</span>
        </div>
    </div>

    <div class="bg-white border border-neutral-200 rounded-2xl p-8">
        <div class="mb-6">
            <h2 class="text-xl font-semibold text-neutral-900">Masuk</h2>
            <p class="text-sm text-neutral-500 mt-1">Silakan masuk untuk melanjutkan</p>
        </div>

        @if (session('status'))
            <div class="mb-4 text-sm text-green-600">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium text-neutral-700 mb-1.5">Email</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    class="w-full rounded-lg border border-neutral-300 px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-amber-500/40"
                >
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-neutral-700 mb-1.5">Kata sandi</label>
                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    class="w-full rounded-lg border border-neutral-300 px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-amber-500/40"
                >
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between text-sm">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="remember">
                    Ingat saya
                </label>

                @if(Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-amber-600">
                        Lupa kata sandi?
                    </a>
                @endif
            </div>

            <button class="w-full bg-neutral-900 text-white rounded-lg py-3">
                Masuk
            </button>

        </form>
    </div>

    @if(Route::has('register'))
        <p class="text-center mt-6 text-sm">
            Belum punya akun?
            <a href="{{ route('register') }}" class="font-semibold">
                Daftar
            </a>
        </p>
    @endif

</div>

</x-guest-layout>
