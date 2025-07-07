<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\notifController;
use App\Http\Controllers\Api\HistoriController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\MidtransController;
use App\Http\Controllers\Api\Pelayan\mejaPelayan;
use App\Http\Controllers\Api\ReservasiController;
use App\Http\Controllers\Pelayan\PesananController;
use App\Http\Controllers\Api\Pelayan\HistoryPelayan;
// use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\RatingPegawaiController;
use App\Http\Controllers\Api\Pelayan\KehadiranReservasi;
use App\Http\Controllers\Api\Pelayan\PelayanReservasiController;
use App\Http\Controllers\Api\Pelayan\PemesananLangsungController;

// use App\Http\Controllers\AuthController;




Route::get('/reservasi/sesi-tersedia', [ReservasiController::class, 'getSesiTersedia']);
Route::post('/rating-pegawai', [RatingPegawaiController::class, 'store']);


Route::middleware('throttle:10,1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/auth/google/redirect', [AuthController::class, 'redirectToGoogle']);
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);
    Route::post('/update-password', [AuthController::class, 'updatePassword']);
});
Route::get('/menu', [MenuController::class, 'index']);
Route::get('/menu-terlaris', [MenuController::class, 'terlaris']);
// Histori Api nih route nya
// Route::middleware('auth:sanctum')->get('/histori', [HistoriController::class, 'index']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/histori', [HistoriController::class, 'index']);
    Route::get('/histori/{id}', [HistoriController::class, 'show']); // untuk detail
});
// buat ambil data reservasi di halaman payment
// reservasi
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/reservasi', [ReservasiController::class, 'buatReservasi']);
    Route::get('/reservasi/{id}', [ReservasiController::class, 'show']);
    Route::get('/reservasi/{id}/detail-pembayaran', [ReservasiController::class, 'detailPembayaran']);
    Route::post('/verifikasi-kehadiran', [ReservasiController::class, 'verifikasiKehadiran']);
    Route::put('/absen/{kode_reservasi}', [ReservasiController::class, 'absen']);
    Route::get('/reservasi/{id}/status-pesanan', [ReservasiController::class, 'getStatusPesanan']);
});

// Route::get('/reservasi/jumlah-meja', [ReservasiController::class, 'getJumlahMeja']);



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
    Route::get('/profile', [ProfileController::class, 'show']);
    // Profile
    Route::put('/profile', [ProfileController::class, 'update']);
    // notif pesanan
    Route::get('/pesanan/status-terbaru', [PesananController::class, 'statusTerbaru']);
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
    // pemesanan langsung di pelayan
    Route::post('/pemesanan-langsung', [PemesananLangsungController::class, 'store']);
});
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/midtrans/token', [MidtransController::class, 'getSnapToken']);
    Route::post('/midtrans/notification', [MidtransController::class, 'handleNotification']);

    //untuk upload bukti pembayaran manual
    Route::post('/midtrans/upload-bukti', [MidtransController::class, 'uploadBuktiManual']);
});
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/reservasi/{id}/status', [notifController::class, 'updateReservasiStatus']);
    Route::post('/pesanan/{id}/status', [notifController::class, 'updatePesananStatus']);
    Route::get('/notifikasi', [notifController::class, 'getNotifikasi']);
    Route::post('/notifikasi/{id}/dibaca', [notifController::class, 'tandaiDibaca']);

    Route::get('/notifikasi', [notifController::class, 'getNotifikasiByToken']);
    Route::post('/notifikasi/{id}/baca', [notifController::class, 'tandaiDibaca']);
});
