<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ManagePemungutController;

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
        return view('Admin.Dashboard.Index');
    })->name('dashboard');

    Route::get('/manage-pemungut', [ManagePemungutController::class, 'index'])->name('managePemungut.index');
    Route::post('/manage-pemungut/tambah', [ManagePemungutController::class, 'tambahPemungut'])->name('managePemungut.tambah');
    Route::post('/manage-pemungut/update/{id}', [ManagePemungutController::class, 'updatePemungut'])->name('managePemungut.update');
    Route::get('/manage-pemungut/hapus/{id}', [ManagePemungutController::class, 'hapusPemungut'])->name('managePemungut.hapus');
    
    Route::get('/manage-pemungut/plotting/{idPemungut}', [ManagePemungutController::class, 'getPlotting'])->name('managePemungut.plotting.index');
    Route::get('/manage-pemungut/plotting-get-desa', [ManagePemungutController::class, 'plottingGetDesa'])->name('managePemungut.plotting.getDesa');
    Route::get('/manage-pemungut/plotting-get-masyarakat/{desaId}', [ManagePemungutController::class, 'getMasyarakatByDesa'])->name('managePemungut.plotting.getMasyarakat');
    Route::post('/manage-pemungut/send-plot/{idPemungut}', [ManagePemungutController::class, 'sendPlot'])->name('managePemungut.plotting.sendPlot');
    Route::get('/manage-pemungut/plotting/hapus/{idMasyarakat}', [ManagePemungutController::class, 'hapusPlotting'])->name('managePemungut.plotting.hapusPlotting');

});