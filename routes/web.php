<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\Desa\TagihanController;
use App\Http\Controllers\KecamatanManageAkunDesaController;
use App\Http\Controllers\Admin\Desa\ManagePemungutController;
use App\Http\Controllers\Admin\Kecamatan\KecamatanTagihanController;

Route::get('/', function () {
    return view('Auth.Login');
})->name('default');

Route::view('/login', 'Auth.Login')->name('login');
Route::view('/register', 'Auth.Register')->name('register');
Route::post('/register', [AuthController::class, 'registerWeb'])->name('auth.register');
Route::post('/login', [AuthController::class, 'loginWeb'])->name('auth.login');
Route::get('/logout', [AuthController::class, 'logoutWeb'])->name('auth.logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('Admin.Dashboard.Index');
    })->name('dashboard');
});

Route::middleware(['auth', 'role:admin_desa'])->group(function () {
    Route::group(['prefix' => 'desa', 'as' => 'desa.'], function () {
        Route::get('/manage-pemungut', [ManagePemungutController::class, 'index'])->name('managePemungut.index');
        Route::post('/manage-pemungut/tambah', [ManagePemungutController::class, 'tambahPemungut'])->name('managePemungut.tambah');
        Route::post('/manage-pemungut/update/{id}', [ManagePemungutController::class, 'updatePemungut'])->name('managePemungut.update');
        Route::get('/manage-pemungut/hapus/{id}', [ManagePemungutController::class, 'hapusPemungut'])->name('managePemungut.hapus');
        
        Route::get('/manage-pemungut/plotting/{idPemungut}', [ManagePemungutController::class, 'getPlotting'])->name('managePemungut.plotting.index');
        Route::post('/manage-pemungut/plotting/toggle', [ManagePemungutController::class, 'toggle'])->name('managePemungut.toggle');
        Route::post('/manage-pemungut/plotting/toggle-all', [ManagePemungutController::class, 'toggleAll'])->name('managePemungut.toggleAll');
    
        Route::get('/tagihan', [TagihanController::class, 'index'])->name('tagihan.index');
        Route::post('/tagihan/update-status', [TagihanController::class, 'updateStatus'])->name('tagihan.updateStatus');
        Route::get('tagihan/export', [TagihanController::class, 'export'])->name('tagihan.export');
        Route::post('tagihan/import', [TagihanController::class, 'import'])->name('tagihan.import');
    });
});

Route::middleware(['auth', 'role:admin_kecamatan'])->group(function () {
    Route::group(['prefix' => 'kecamatan', 'as' => 'kecamatan.'], function () {
        Route::get('/manage-akun-desa', [KecamatanManageAkunDesaController::class, 'index'])->name('manageAkunDesa.index');
        Route::post('/manage-akun-desa/tambah', [KecamatanManageAkunDesaController::class, 'tambahAkunDesa'])->name('manageAkunDesa.tambah');
        Route::post('/manage-akun-desa/update/{id}', [KecamatanManageAkunDesaController::class, 'updateAkunDesa'])->name('manageAkunDesa.update');
        Route::get('/manage-akun-desa/hapus/{id}', [KecamatanManageAkunDesaController::class, 'hapusAkunDesa'])->name('manageAkunDesa.hapus');
        
        Route::get('/tagihan', [KecamatanTagihanController::class, 'index'])->name('tagihan.index');
        Route::post('/tagihan/update-status', [KecamatanTagihanController::class, 'updateStatus'])->name('tagihan.updateStatus');
    });
});


