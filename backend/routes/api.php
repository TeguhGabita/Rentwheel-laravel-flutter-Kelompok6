<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\KategoriMobilController;
use App\Http\Controllers\Api\MobilController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\PembayaranController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\LaporanController;
use App\Http\Controllers\Api\NotifikasiController;


// ======================================================
// AUTH
// ======================================================

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


// ======================================================
// LOGIN REQUIRED
// ======================================================

Route::middleware('auth:sanctum')->group(function () {

    // ===========================
    // AUTH
    // ===========================

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::put('/profile/password', [AuthController::class, 'changePassword']);
    // ===========================
    // DASHBOARD
    // ===========================

    Route::get('/dashboard', [DashboardController::class, 'index']);

    // ===========================
    // KATEGORI
    // ===========================

    Route::get('/kategori-mobil', [KategoriMobilController::class, 'index']);
    Route::post('/kategori-mobil', [KategoriMobilController::class, 'store']);
    Route::get('/kategori-mobil/{id}', [KategoriMobilController::class, 'show']);
    Route::put('/kategori-mobil/{id}', [KategoriMobilController::class, 'update']);
    Route::delete('/kategori-mobil/{id}', [KategoriMobilController::class, 'destroy']);

    // ===========================
    // MOBIL
    // ===========================

    Route::get('/mobil', [MobilController::class, 'index']);
    Route::get('/mobil/{id}', [MobilController::class, 'show']);
    Route::post('/mobil', [MobilController::class, 'store']);
    Route::put('/mobil/{id}', [MobilController::class, 'update']);
    Route::delete('/mobil/{id}', [MobilController::class, 'destroy']);

    // ===========================
    // BOOKING
    // ===========================

    Route::get('/booking', [BookingController::class, 'index']);
    Route::post('/booking', [BookingController::class, 'store']);
    Route::get('/booking/{id}', [BookingController::class, 'show']);
    Route::put('/booking/{id}/status', [BookingController::class, 'updateStatus']);

    // ===========================
    // PEMBAYARAN
    // ===========================

    Route::get('/pembayaran', [PembayaranController::class, 'index']);
    Route::post('/pembayaran', [PembayaranController::class, 'store']);
    Route::get('/pembayaran/{id}', [PembayaranController::class, 'show']);
    Route::put('/pembayaran/{id}/status', [PembayaranController::class, 'updateStatus']);

    // ===========================
    // USER (Manajemen User)
    // ===========================

    Route::get('/user', [UserController::class, 'index']);
    Route::post('/user', [UserController::class, 'store']);
    Route::get('/user/{id}', [UserController::class, 'show']);
    Route::put('/user/{id}', [UserController::class, 'update']);
    Route::delete('/user/{id}', [UserController::class, 'destroy']);

    // ===========================
    // LAPORAN
    // ===========================

    Route::get('/laporan', [LaporanController::class, 'index']);

    // ===========================
    // NOTIFIKASI
    // ===========================

    Route::get('/notifikasi', [NotifikasiController::class, 'index']);
    Route::post('/notifikasi/{id}/baca', [NotifikasiController::class, 'markAsRead']);
    Route::post('/notifikasi/baca-semua', [NotifikasiController::class, 'markAllAsRead']);

});
