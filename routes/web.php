<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ValidasiController;
use App\Http\Controllers\Petugas\DashboardController as PetugasDashboard;
use App\Http\Controllers\Petugas\LaporanController;
use App\Http\Controllers\Pimpinan\DashboardController as PimpinanDashboard;
use Illuminate\Support\Facades\Route;

// ─── Redirect root ────────────────────────────────────────────────────────────
Route::get('/', fn () => redirect()->route('login'));

// ─── Auth ─────────────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// ─── Admin ────────────────────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {

    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');
    Route::get('/dashboard/pdf', [AdminDashboard::class, 'exportPdf'])->name('dashboard.pdf');

    // Manajemen Pengguna
    Route::resource('users', UserController::class)->only(['index', 'create', 'store', 'edit', 'update']);
    Route::patch('users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
    Route::patch('users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');

    // Validasi Laporan
    Route::prefix('validasi')->name('validasi.')->group(function () {
        Route::get('/', [ValidasiController::class, 'index'])->name('index');
        Route::get('/{laporan}', [ValidasiController::class, 'show'])->name('show');
        Route::patch('/{laporan}/setujui', [ValidasiController::class, 'setujui'])->name('setujui');
        Route::patch('/{laporan}/tolak', [ValidasiController::class, 'tolak'])->name('tolak');
    });
});

// ─── Petugas ──────────────────────────────────────────────────────────────────
Route::prefix('petugas')->name('petugas.')->middleware(['auth', 'role:petugas'])->group(function () {

    Route::get('/dashboard', [PetugasDashboard::class, 'index'])->name('dashboard');

    Route::resource('laporan', LaporanController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update']);
});

// ─── Pimpinan ─────────────────────────────────────────────────────────────────
Route::prefix('pimpinan')->name('pimpinan.')->middleware(['auth', 'role:pimpinan'])->group(function () {

    Route::get('/dashboard', [PimpinanDashboard::class, 'index'])->name('dashboard');
    Route::get('/dashboard/pdf', [PimpinanDashboard::class, 'exportPdf'])->name('dashboard.pdf');
});
