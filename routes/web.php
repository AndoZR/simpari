<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::view('/login', 'Auth.Login')->name('login');
Route::view('/register', 'Auth.Register')->name('register');
Route::post('/register', [AuthController::class, 'registerWeb'])->name('auth.register');
Route::post('/login', [AuthController::class, 'loginWeb'])->name('auth.login');
Route::get('/logout', [AuthController::class, 'logoutWeb'])->name('auth.logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/', function () {
        return;
    });

    // Protected routes can be defined here
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});