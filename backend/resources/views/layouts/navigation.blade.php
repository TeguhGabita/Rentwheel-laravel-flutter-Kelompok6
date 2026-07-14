<nav x-data="{ open: false, notifOpen: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center gap-2">
                    <a href="{{ auth()->user()->hasRole('admin') ? route('dashboard') : route('beranda') }}"
                       class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-amber-500 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 16l-2 2m0 0l-2-2m2 2V9a2 2 0 012-2h8a2 2 0 012 2v9m-12 0h12m-2 0l2 2m-2-2l2-2" />
                            </svg>
                        </div>
                        <span class="font-semibold text-neutral-900 text-[15px]">RentWheel</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    @if (auth()->user()->hasRole('admin'))
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                    @else
                        <x-nav-link :href="route('beranda')" :active="request()->routeIs('beranda')">
                            {{ __('Beranda') }}
                        </x-nav-link>
                        <x-nav-link :href="route('mobil.index')" :active="request()->routeIs('mobil.*')">
                            {{ __('Cari Mobil') }}
                        </x-nav-link>
                        <x-nav-link :href="route('booking.index')" :active="request()->routeIs('booking.*')">
                            {{ __('Booking Saya') }}
                        </x-nav-link>
                        <x-nav-link :href="route('pembayaran.index')" :active="request()->routeIs('pembayaran.*')">
                            {{ __('Pembayaran') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Right side: Notifikasi + Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-2">

                {{-- Dropdown Notifikasi --}}
                @php
                    $notifikasiList = auth()->user()->unreadNotifications;
                    $jumlahNotif = $notifikasiList->count();
                @endphp

                <div class="relative">
                    <button @click="notifOpen = !notifOpen" @click.away="notifOpen = false"
                            class="relative p-2 rounded-md text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
                        </svg>
                        @if($jumlahNotif > 0)
                            <span class="absolute top-0.5 right-0.5 min-w-[16px] h-4 px-1 bg-red-500 rounded-full text-[10px] font-bold text-white flex items-center justify-center">
                                {{ $jumlahNotif }}
                            </span>
                        @endif
                    </button>

                    <div x-show="notifOpen" x-cloak
                         class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg border border-gray-100 z-50 max-h-96 overflow-y-auto">
                        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
                            <p class="text-sm font-semibold text-gray-800">Notifikasi</p>
                            @if($jumlahNotif > 0)
                                <form method="POST" action="{{ route('notifikasi.bacaSemua') }}">
                                    @csrf
                                    <button type="submit" class="text-xs text-amber-600 hover:underline">
                                        Tandai semua dibaca
                                    </button>
                                </form>
                            @endif
                        </div>

                        @forelse($notifikasiList as $notif)
                            <a href="{{ route('notifikasi.baca', $notif->id) }}"
                               class="block px-4 py-3 border-b border-gray-50 hover:bg-gray-50 transition-colors">
                                <p class="text-sm text-gray-700">{{ $notif->data['message'] ?? 'Notifikasi baru' }}</p>
                                <p class="text-xs text-gray-400 mt-1">{{ $notif->created_at->diffForHumans() }}</p>
                            </a>
                        @empty
                            <p class="px-4 py-6 text-sm text-gray-400 text-center">Tidak ada notifikasi baru</p>
                        @endforelse
                    </div>
                </div>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @if (auth()->user()->hasRole('admin'))
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>
            @else
                <x-responsive-nav-link :href="route('beranda')" :active="request()->routeIs('beranda')">
                    {{ __('Beranda') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('mobil.index')" :active="request()->routeIs('mobil.*')">
                    {{ __('Cari Mobil') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('booking.index')" :active="request()->routeIs('booking.*')">
                    {{ __('Booking Saya') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('pembayaran.index')" :active="request()->routeIs('pembayaran.*')">
                    {{ __('Pembayaran') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Notifikasi -->
        <div class="pt-2 pb-2 border-t border-gray-200">
            <div class="px-4 flex items-center justify-between">
                <span class="font-medium text-sm text-gray-600">Notifikasi</span>
                @if($jumlahNotif > 0)
                    <span class="text-xs bg-red-500 text-white rounded-full px-2 py-0.5">{{ $jumlahNotif }}</span>
                @endif
            </div>
            <div class="mt-2 space-y-1">
                @forelse($notifikasiList as $notif)
                    <a href="{{ route('notifikasi.baca', $notif->id) }}"
                       class="block px-4 py-2 text-sm text-gray-600 hover:bg-gray-50">
                        {{ $notif->data['message'] ?? 'Notifikasi baru' }}
                        <span class="block text-xs text-gray-400">{{ $notif->created_at->diffForHumans() }}</span>
                    </a>
                @empty
                    <p class="px-4 text-sm text-gray-400">Tidak ada notifikasi baru</p>
                @endforelse
            </div>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
