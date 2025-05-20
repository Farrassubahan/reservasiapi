<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Koki\DapurController;
use App\Http\Controllers\Auth\LoginController;

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
    return view('welcome');
});
use App\Http\Controllers\Admin\AdminController;

Route::get('/admin', [AdminController::class, 'dashboard']);

// Admin reservasi
Route::prefix('admin')->group(function () {
    Route::put('/reservasi/{id}/edit', [AdminController::class, 'editReservasi'])->name('admin.reservasi.edit');
    Route::delete('/reservasi/{id}', [AdminController::class, 'hapusReservasi'])->name('admin.reservasi.hapus');
});

// Route::middleware(['auth', 'role:koki'])->group(function () {
//     Route::get('/koki/dapur', [DapurController::class, 'index'])->name('koki.dapur');
//     Route::patch('/koki/dapur/{id}', [DapurController::class, 'updateStatus'])->name('koki.dapur.updateStatus');
// });
// Route::prefix('koki')->group(function () {
//     Route::get('/', [DapurController::class, 'index'])->name('koki.dashboard');
//     Route::put('/pesanan/{id}/status', [DapurController::class, 'updateStatus']);
// });
Route::middleware(['auth', 'role:Koki'])->prefix('koki')->group(function () {
    Route::get('/', [DapurController::class, 'index'])->name('koki.dashboard');
    Route::put('/pesanan/{id}/status', [DapurController::class, 'updateStatus']);
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');