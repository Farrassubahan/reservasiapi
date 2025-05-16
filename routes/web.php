<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Koki\DapurController;

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
Route::middleware(['auth', 'role:koki'])->group(function () {
    Route::get('/koki/dapur', [DapurController::class, 'index'])->name('koki.dapur');
    Route::patch('/koki/dapur/{id}', [DapurController::class, 'updateStatus'])->name('koki.dapur.updateStatus');
});
Route::prefix('koki')->group(function () {
    Route::get('/', [DapurController::class, 'index'])->name('koki.dashboard');
    Route::put('/pesanan/{id}/status', [DapurController::class, 'updateStatus']);
});
