<x-admin-layout>

<x-slot name="title">
Dashboard Admin
</x-slot>

<x-slot name="subtitle">
Selamat datang di Dashboard RentWheel
</x-slot>

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 relative overflow-hidden">
        <div class="absolute -right-4 -top-4 w-24 h-24 bg-amber-50 rounded-full"></div>
        <div class="relative">
            <div class="w-11 h-11 rounded-xl bg-amber-100 flex items-center justify-center mb-4">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 11l1.5-4.5A2 2 0 018.4 5h7.2a2 2 0 011.9 1.5L19 11m-14 0v6a1 1 0 001 1h1a1 1 0 001-1v-1h8v1a1 1 0 001 1h1a1 1 0 001-1v-6m-14 0h14M7 15h.01M17 15h.01" />
                </svg>
            </div>
            <h4 class="text-slate-500 text-sm font-medium">Total Mobil</h4>
            <h2 class="text-3xl font-bold mt-1 text-slate-900">{{ $totalMobil }}</h2>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 relative overflow-hidden">
        <div class="absolute -right-4 -top-4 w-24 h-24 bg-sky-50 rounded-full"></div>
        <div class="relative">
            <div class="w-11 h-11 rounded-xl bg-sky-100 flex items-center justify-center mb-4">
                <svg class="w-5 h-5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                </svg>
            </div>
            <h4 class="text-slate-500 text-sm font-medium">Total Pelanggan</h4>
            <h2 class="text-3xl font-bold mt-1 text-slate-900">{{ $totalUser }}</h2>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 relative overflow-hidden">
        <div class="absolute -right-4 -top-4 w-24 h-24 bg-violet-50 rounded-full"></div>
        <div class="relative">
            <div class="w-11 h-11 rounded-xl bg-violet-100 flex items-center justify-center mb-4">
                <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .984.75 1.75 1.972 1.75.23 0 .456-.044.782-.128A48.408 48.408 0 016.892 6.108V2.892m9.216 0v5.6"/>
                </svg>
            </div>
            <h4 class="text-slate-500 text-sm font-medium">Total Booking</h4>
            <h2 class="text-3xl font-bold mt-1 text-slate-900">{{ $totalBooking }}</h2>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 relative overflow-hidden">
        <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-50 rounded-full"></div>
        <div class="relative">
            <div class="w-11 h-11 rounded-xl bg-emerald-100 flex items-center justify-center mb-4">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h4 class="text-slate-500 text-sm font-medium">Total Pembayaran</h4>
            <h2 class="text-3xl font-bold mt-1 text-slate-900">{{ $totalPembayaran }}</h2>
        </div>
    </div>

</div>


<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">

        <h3 class="font-bold text-lg mb-5 text-slate-900">
            Status Booking
        </h3>

        <div class="flex items-center justify-between border-b border-slate-100 py-3.5">
            <div class="flex items-center gap-3">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span>
                <span class="text-slate-600">Booking Berjalan</span>
            </div>
            <span class="font-bold text-lg text-slate-900">{{ $bookingBerjalan }}</span>
        </div>

        <div class="flex items-center justify-between py-3.5">
            <div class="flex items-center gap-3">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                <span class="text-slate-600">Booking Selesai</span>
            </div>
            <span class="font-bold text-lg text-slate-900">{{ $bookingSelesai }}</span>
        </div>

    </div>

    <div class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-2xl shadow-sm p-6 text-white">

        <h3 class="font-bold text-lg mb-5">
            Pendapatan
        </h3>

        <div class="text-4xl font-bold text-emerald-400">
            Rp {{ number_format($pendapatan,0,',','.') }}
        </div>

        <p class="text-slate-400 mt-3 text-sm">
            Total pembayaran yang telah lunas.
        </p>

    </div>

</div>

{{-- Grafik Pendapatan 6 Bulan Terakhir --}}
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 mt-6">

    <h3 class="font-bold text-lg mb-5 text-slate-900">
        Grafik Pendapatan (6 Bulan Terakhir)
    </h3>

    <div class="relative" style="height: 320px;">
        <canvas id="chartPendapatan"></canvas>
    </div>

</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('chartPendapatan').getContext('2d');

        const labelBulan = @json($labelBulan);
        const dataPendapatan = @json($dataPendapatan);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labelBulan,
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: dataPendapatan,
                    backgroundColor: 'rgba(252, 211, 77, 0.6)', // amber-300, lebih terang
                    borderColor: 'rgba(245, 158, 11, 1)', // amber-500 tetap untuk outline
                    borderWidth: 1.5,
                    borderRadius: 8,
                    maxBarThickness: 48,
                    hoverBackgroundColor: 'rgba(252, 211, 77, 0.85)',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                const value = context.parsed.y;
                                return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function (value) {
                                return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush

</x-admin-layout>
