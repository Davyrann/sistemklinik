<?php

use App\Http\Controllers\Admin\LogController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Resepsionis\DashboardController;
use App\Http\Controllers\Resepsionis\KunjunganController;
use App\Http\Controllers\Resepsionis\ObatController;
use App\Http\Controllers\Resepsionis\PasienController;
use App\Http\Controllers\Resepsionis\TagihanController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    if (auth()->user()?->role === 'dokter') {
        return redirect()->route('dokter.dashboard');
    }

    return app(DashboardController::class)();
})->middleware(['auth'])->name('dashboard');

Route::middleware(['auth', 'role:resepsionis,admin'])->group(function () {
    Route::get('/resepsionis', DashboardController::class)->name('resepsionis.dashboard');
    Route::resource('pasien', PasienController::class);
    Route::resource('kunjungan', KunjunganController::class)->except(['edit', 'update']);
    Route::patch('kunjungan/{kunjungan}/status', [KunjunganController::class, 'updateStatus'])->name('kunjungan.status');
    Route::get('tagihan', [TagihanController::class, 'index'])->name('tagihan.index');
    Route::get('tagihan/{tagihan}', [TagihanController::class, 'show'])->name('tagihan.show');
    Route::post('tagihan/{tagihan}/bayar', [TagihanController::class, 'bayar'])->name('tagihan.bayar');
    Route::get('tagihan/{tagihan}/kuitansi', [TagihanController::class, 'kuitansi'])->name('tagihan.kuitansi');
    Route::get('obat', [ObatController::class, 'index'])->name('obat.index');
    Route::get('obat/{obat}', [ObatController::class, 'show'])->name('obat.show');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('user', [UserController::class, 'index'])->name('user.index');
    Route::get('user/create', [UserController::class, 'create'])->name('user.create');
    Route::post('user', [UserController::class, 'store'])->name('user.store');
    Route::get('user/{user}/edit', [UserController::class, 'edit'])->name('user.edit');
    Route::put('user/{user}', [UserController::class, 'update'])->name('user.update');
    Route::delete('user/{user}', [UserController::class, 'destroy'])->name('user.destroy');
    Route::get('log', LogController::class)->name('log.index');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
