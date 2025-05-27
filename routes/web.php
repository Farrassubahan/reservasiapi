<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Koki\DapurController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ReservasiController;

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



Route::get('/', function () {
    return view('admin_pelanggan');
});

// Admin reservasi
Route::prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard']);
    
    // Reservasi
    Route::get('/reservasi', [ReservasiController::class, 'index'])->name('reservasi.index');
    Route::put('/reservasi/{id}', [ReservasiController::class, 'update'])->name('reservasi.update');
    Route::delete('/reservasi/{id}', [ReservasiController::class, 'destroy'])->name('reservasi.destroy');
});

Route::middleware(['auth', 'role:Koki'])->prefix('koki')->group(function () {
    Route::get('/', [DapurController::class, 'index'])->name('koki.dashboard');
    Route::get('/pesanan', [DapurController::class, 'pesananMasuk'])->name('koki.pesanan');
    Route::put('/pesanan/{id}/status', [DapurController::class, 'updateStatus']);
});

Route::get('/admin', [AdminController::class, 'dashboard']);
