<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\HistoriController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\API\ReservasiController;
use App\Http\Controllers\API\Pelayan\mejaPelayan;
use App\Http\Controllers\API\Pelayan\HistoryPelayan;
use App\Http\Controllers\API\Pelayan\KehadiranReservasi;
use App\Http\Controllers\Api\Pelayan\PelayanReservasiController;




// use App\Http\Controllers\AuthController;



Route::middleware('throttle:10,1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/auth/google/redirect', [AuthController::class, 'redirectToGoogle']);
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);
});

Route::get('/menu', [MenuController::class, 'index']);
Route::get('/menu-terlaris', [MenuController::class, 'terlaris']);


// Histori Api nih route nya
Route::middleware('auth:sanctum')->get('/histori', [HistoriController::class, 'index']);

// reservasi
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/reservasi', [ReservasiController::class, 'buatReservasi']);
});



/* 
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will 
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::middleware('auth:sanctum')->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);

    // Pelayan - Reservasi
    Route::get('/pelayan/reservasi', [PelayanReservasiController::class, 'index']);
    Route::get('/pelayan/reservasi/{reservasiId}', [PelayanReservasiController::class, 'show']);   
    Route::post('/pelayan/reservasi/{reservasiId}/konfirmasi-meja', [PelayanReservasiController::class, 'konfirmasiMeja']);

    // Pelayan - Kehadiran
    Route::get('/pelayan/kehadiran-reservasi', [KehadiranReservasi::class, 'index']);
    Route::put('/pelayan/reservasi/{id}/konfirmasi', [KehadiranReservasi::class, 'konfirmasi']);

    // Histtory - buat pelayan
    Route::get('/pelayan/history-reservasi', [HistoryPelayan::class, 'index']);

    // meja di halaman pelayan
    Route::get('/pelayan/meja', [mejaPelayan::class, 'index']);
});
