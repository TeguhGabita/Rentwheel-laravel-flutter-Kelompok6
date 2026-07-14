<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MobilController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\MobilController as AdminMobilController;
use App\Http\Controllers\Admin\KategoriMobilController as AdminKategoriMobilController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| Beranda User
|--------------------------------------------------------------------------
*/

Route::get('/beranda', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'role:user'])
    ->name('beranda');

/*
|--------------------------------------------------------------------------
| Profile
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Notifikasi (Admin & User)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/notifikasi/{id}/baca', function (\Illuminate\Http\Request $request, $id) {
        $notif = $request->user()->notifications()->findOrFail($id);
        $notif->markAsRead();

        return redirect($notif->data['url'] ?? '/');
    })->name('notifikasi.baca');

    Route::post('/notifikasi/baca-semua', function (\Illuminate\Http\Request $request) {
        $request->user()->unreadNotifications->markAsRead();

        return back();
    })->name('notifikasi.bacaSemua');
});

/*
|--------------------------------------------------------------------------
| User
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/mobil', [MobilController::class, 'index'])->name('mobil.index');
    Route::get('/mobil/{mobil}', [MobilController::class, 'show'])->name('mobil.show');

    Route::get('/booking', [BookingController::class, 'index'])->name('booking.index');
    Route::get('/booking/create', [BookingController::class, 'create'])->name('booking.create');
    Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
    Route::get('/booking/{booking}', [BookingController::class, 'show'])->name('booking.show');

    Route::get('/pembayaran', [PembayaranController::class, 'index'])->name('pembayaran.index');
    Route::get('/pembayaran/create', [PembayaranController::class, 'create'])->name('pembayaran.create');
    Route::post('/pembayaran', [PembayaranController::class, 'store'])->name('pembayaran.store');
});

/*
|--------------------------------------------------------------------------
| Admin
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Dashboard Admin
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        // User
        Route::resource('users', UserController::class);

        // Mobil
        Route::resource('mobil', AdminMobilController::class)->except('show');

        // Kategori
        Route::resource('kategori', AdminKategoriMobilController::class)->except('show');

        // Laporan Booking
        Route::get('laporan', [LaporanController::class, 'index'])
            ->name('laporan.index');

        Route::post('laporan/cetak', [LaporanController::class, 'cetak'])
            ->name('laporan.cetak');

        // Update status booking (dari halaman Laporan)
        Route::patch('laporan/{booking}/status', [LaporanController::class, 'updateStatus'])
            ->name('laporan.updateStatus');

        // Update status pembayaran (dari halaman Laporan) - baris baru
        Route::patch('laporan/{booking}/status-bayar', [LaporanController::class, 'updateStatusBayar'])
            ->name('laporan.updateStatusBayar');

        // Data Pembayaran Admin
        Route::resource('pembayaran', \App\Http\Controllers\Admin\PembayaranController::class);
    });

require __DIR__.'/auth.php';
