<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Koki\DapurController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ReservasiController;
use App\Http\Controllers\Admin\PelangganController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will 
| be assigned to the "web" middleware group. Make something great!
|
*/

// Default route
Route::get('/', function () {
    return view('admin_pelanggan');
});

// Auth routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Admin routes
Route::get('/admin', [AdminController::class, 'dashboard']);

Route::prefix('admin')->group(function () {
    // Dashboard Admin
    Route::get('/', [AdminController::class, 'dashboard']);

    // ✅ Pelanggan
    Route::get('/pelanggan', [PelangganController::class, 'index'])->name('admin.pelanggan.index');
    Route::post('/pelanggan/search', [PelangganController::class, 'search'])->name('admin.pelanggan.search');
    Route::put('/pelanggan/{id}', [PelangganController::class, 'update'])->name('admin.pelanggan.update');
    Route::delete('/pelanggan/{id}', [PelangganController::class, 'destroy'])->name('admin.pelanggan.destroy');

    // ✅ Reservasi
    Route::get('/reservasi', [ReservasiController::class, 'index'])->name('reservasi.index'); // dari teman
    Route::put('/reservasi/{id}', [ReservasiController::class, 'update'])->name('reservasi.update'); // dari teman
    Route::delete('/reservasi/{id}', [ReservasiController::class, 'destroy'])->name('reservasi.destroy'); // dari teman

    Route::put('/reservasi/{id}/edit', [AdminController::class, 'editReservasi'])->name('admin.reservasi.edit'); // dari kamu
    Route::delete('/reservasi/{id}', [AdminController::class, 'hapusReservasi'])->name('admin.reservasi.hapus'); // dari kamu
});

// Koki routes
Route::middleware(['auth', 'role:Koki'])->prefix('koki')->group(function () {
    Route::get('/', [DapurController::class, 'index'])->name('koki.dashboard');
    Route::get('/pesanan', [DapurController::class, 'pesananMasuk'])->name('koki.pesanan'); // dari teman
    Route::put('/pesanan/{id}/status', [DapurController::class, 'updateStatus']);
});
