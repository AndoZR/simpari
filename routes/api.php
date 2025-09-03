<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PemungutController;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout']);

Route::post('get-tagihan', [PemungutController::class, 'getTagihanByNop']);


Route::middleware('auth:sanctum','role:pemungut')->group(function () {
    Route::get('profile', [PemungutController::class, 'showTagihan']);
    Route::post('tagihan/bayar-tagihan', [PemungutController::class, 'bayarTagihan']);

    // Route::get('profile', [AuthController::class, 'profile']);
    Route::get('tagihan/capaian', [PemungutController::class, 'getCapaian']);
});