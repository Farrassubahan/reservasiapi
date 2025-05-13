<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\MejaController;
// use App\Http\Controllers\AuthController;

// Route::post('/register', [AuthController::class, 'register']);

// Route::get('/auth/google/redirect', [AuthController::class, 'redirectToGoogle']);
// Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);

Route::middleware('throttle:10,1')->group(function () {

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/auth/google/redirect', [AuthController::class, 'redirectToGoogle']);
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);
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
// Route::post('/register', [AuthController::class, 'register']);
use App\Http\Controllers\Admin\MenuController;

Route::prefix('admin')->group(function () {
    Route::get('/menu', [MenuController::class, 'index']);
    Route::post('/menu', [MenuController::class, 'store']);
    Route::get('/menu/{id}', [MenuController::class, 'show']);
    Route::put('/menu/{id}', [MenuController::class, 'update']);
    Route::delete('/menu/{id}', [MenuController::class, 'destroy']);
});


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
// Route::prefix('admin')->middleware('auth:sanctum')->group(function () {
//     Route::get('/meja', [MejaController::class, 'index']);           // GET semua meja
//     Route::post('/meja', [MejaController::class, 'store']);          // POST tambah meja
//     Route::get('/meja/{id}', [MejaController::class, 'show']);       // GET detail meja
//     Route::put('/meja/{id}', [MejaController::class, 'update']);     // PUT edit meja
//     Route::delete('/meja/{id}', [MejaController::class, 'destroy']); // DELETE hapus meja
// });


Route::prefix('admin')->group(function () {
    Route::get('/meja', [MejaController::class, 'index']);
    Route::post('/meja', [MejaController::class, 'store']);
    Route::get('/meja/{id}', [MejaController::class, 'show']);
    Route::put('/meja/{id}', [MejaController::class, 'update']);
    Route::delete('/meja/{id}', [MejaController::class, 'destroy']);
});