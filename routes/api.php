<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PemungutController;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('profile', [PemungutController::class, 'showTagihan']);
    // Route::post('tagihan/cicilan', [PemungutController::class, 'showCicilan']);
    // Route::post('tagihan/cicilan/store', [PemungutController::class, 'storeCicilan']);
    // Route::post('tagihan/cicilan/update', [PemungutController::class, 'updateCicilan']);
    Route::post('tagihan/update-status', [PemungutController::class, 'updateTagihan']);

    // Route::get('profile', [AuthController::class, 'profile']);
    Route::get('tagihan/capaian', [PemungutController::class, 'getCapaian']);
});