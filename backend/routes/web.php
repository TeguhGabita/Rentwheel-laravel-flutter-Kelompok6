<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MobilController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PembayaranController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('admin.dashboard');
})->middleware(['auth', 'verified', 'role:admin'])->name('dashboard');

Route::get('/beranda', function () {
    return view('beranda.index');
})->middleware(['auth', 'verified', 'role:user'])->name('beranda');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/mobil', [MobilController::class, 'index'])->name('mobil.index');
    Route::get('/mobil/{mobil}', [MobilController::class, 'show'])->name('mobil.show');

    // Admin CRUD untuk mobil
    Route::middleware('role:admin')->group(function () {
        Route::get('/mobil/create', [MobilController::class, 'create'])->name('mobil.create');
        Route::post('/mobil', [MobilController::class, 'store'])->name('mobil.store');
        Route::get('/mobil/{mobil}/edit', [MobilController::class, 'edit'])->name('mobil.edit');
        Route::put('/mobil/{mobil}', [MobilController::class, 'update'])->name('mobil.update');
        Route::delete('/mobil/{mobil}', [MobilController::class, 'destroy'])->name('mobil.destroy');
    });

    Route::get('/booking', [BookingController::class, 'index'])->name('booking.index');
    Route::get('/booking/create', [BookingController::class, 'create'])->name('booking.create');
    Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
    Route::get('/booking/{booking}', [BookingController::class, 'show'])->name('booking.show');
    Route::delete('/booking/{booking}', [BookingController::class, 'destroy'])->name('booking.destroy');
    Route::post('/booking/{booking}/cancel', [BookingController::class, 'cancel'])->name('booking.cancel');

    Route::get('/pembayaran', [PembayaranController::class, 'index'])->name('pembayaran.index');
    Route::get('/pembayaran/create', [PembayaranController::class, 'create'])->name('pembayaran.create');
    Route::post('/pembayaran', [PembayaranController::class, 'store'])->name('pembayaran.store');
});

require __DIR__.'/auth.php';
