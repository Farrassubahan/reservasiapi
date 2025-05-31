<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UsmController;
use App\Http\Controllers\Admin\MejaController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Koki\DapurController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\PelangganController;
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

// Default route
Route::get('/', function () {
    return view('welcome');
});

// Auth routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');



Route::middleware(['auth', 'role:Admin'])->prefix('admin')->group(function () {
    // dashboard admin
    Route::get('/', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    // admin pelanggan
    Route::get('/pelanggan', [PelangganController::class, 'index'])->name('admin.pelanggan.index');
    Route::post('/pelanggan/search', [PelangganController::class, 'search'])->name('admin.pelanggan.search');
    Route::put('/pelanggan/{id}', [PelangganController::class, 'update'])->name('admin.pelanggan.update');
    Route::delete('/pelanggan/{id}', [PelangganController::class, 'destroy'])->name('admin.pelanggan.destroy');

    // admin reservasi
    Route::get('/reservasi', [ReservasiController::class, 'index'])->name('reservasi.index');
    Route::put('/reservasi/{id}', [ReservasiController::class, 'update'])->name('reservasi.update');
    Route::delete('/reservasi/{id}', [ReservasiController::class, 'destroy'])->name('reservasi.destroy');
    Route::put('/reservasi/{id}/edit', [AdminController::class, 'editReservasi'])->name('admin.reservasi.edit');

    // admin crud menu
    Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');
    Route::post('/menu', [MenuController::class, 'store'])->name('menu.store');
    Route::get('/menu/{id}/edit', [MenuController::class, 'edit'])->name('menu.edit');
    Route::put('/menu/{id}', [MenuController::class, 'update'])->name('menu.update');
    Route::delete('/menu/{id}', [MenuController::class, 'destroy'])->name('menu.destroy');
    Route::put('/admin/menu/{id}/ubah-stok', [MenuController::class, 'ubahStok'])->name('menu.ubah-stok');

    // admin crud meja
    Route::get('/meja', [MejaController::class, 'index'])->name('meja.index');
    Route::post('/meja', [MejaController::class, 'store'])->name('meja.store');
    Route::get('/meja/{id}', [MejaController::class, 'show'])->name('meja.show');
    Route::put('/meja/{id}', [MejaController::class, 'update'])->name('meja.update');
    Route::delete('/meja/{id}', [MejaController::class, 'destroy'])->name('meja.destroy');

    // admin user management
    Route::get('/user-management', [UsmController::class, 'index'])->name('admin.usm.index');
    Route::post('/user-management/store', [UsmController::class, 'store'])->name('admin.usm.store');
    Route::post('/user-management/update/{id}', [UsmController::class, 'update'])->name('admin.usm.update');
    Route::delete('/user-management/delete/{id}', [UsmController::class, 'destroy'])->name('admin.usm.destroy');
    Route::get('/user-management/{id}', [UsmController::class, 'show'])->name('admin.usm.show');
    // admin laporan
    Route::get('/laporan', [LaporanController::class, 'index'])->name('admin.laporan');
});


// Koki routes
Route::middleware(['auth', 'role:Koki'])->prefix('koki')->group(function () {
    Route::get('/', [DapurController::class, 'index'])->name('koki.dashboard');
    Route::get('/pesanan', [DapurController::class, 'pesananMasuk'])->name('koki.pesanan'); // dari teman
    Route::put('/pesanan/{id}/status', [DapurController::class, 'updateStatus']);
});
