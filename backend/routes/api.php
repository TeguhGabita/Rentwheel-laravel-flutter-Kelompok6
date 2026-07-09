<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\KategoriMobilController;
use App\Http\Controllers\Api\MobilController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\PembayaranController;
use Illuminate\Support\Facades\Route;

// Auth (tidak butuh login)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Butuh login (token Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Kategori & Mobil (read-only untuk user biasa)
    Route::get('/kategori-mobil', [KategoriMobilController::class, 'index']);
    Route::get('/mobil', [MobilController::class, 'index']);
    Route::get('/mobil/{id}', [MobilController::class, 'show']);

    // Booking
    Route::get('/booking', [BookingController::class, 'index']);
    Route::post('/booking', [BookingController::class, 'store']);
    Route::get('/booking/{id}', [BookingController::class, 'show']);

    // Pembayaran
    Route::post('/pembayaran', [PembayaranController::class, 'store']);
});
