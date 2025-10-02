<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\NasabahController;
use App\Http\Controllers\PetugasController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JanjiBayarController;
use App\Http\Controllers\PenangananController;
use App\Http\Controllers\KolektibilitasHistoryController;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/', [DashboardController::class, 'index']);

    Route::get('/nasabah', [NasabahController::class, 'index'])->name('nasabah.index');
    Route::get('/nasabah/{id}', [NasabahController::class, 'show'])->name('nasabah.show');

    Route::resource('petugas', PetugasController::class);

    Route::post('/nasabah/{id}/assign-penanganan', [PenangananController::class, 'assignPenanganan'])->name('penanganan.assign');
    Route::post('/janji-bayar', [JanjiBayarController::class, 'store'])->name('janji-bayar.store');
    Route::post('/janji-bayar/{id}/status', [JanjiBayarController::class, 'updateStatus'])->name('janji-bayar.update-status');

    Route::middleware('admin')->group(function () {
        Route::get('/import', [ImportController::class, 'showImport'])->name('import.show');
        Route::post('/import', [ImportController::class, 'importExcel'])->name('import.excel');
        Route::get('/import/history', [ImportController::class, 'importHistory'])->name('import.history');
        Route::get('/import/stats', [ImportController::class, 'importStats'])->name('import.stats');
        Route::get('/import/template', [ImportController::class, 'downloadTemplate'])->name('import.download-template');
        Route::post('/import/clear-history', [ImportController::class, 'clearHistory'])->name('import.clear-history');
        Route::get('/kolektibilitas/history', [KolektibilitasHistoryController::class, 'index'])->name('kolektibilitas.history');
        Route::get('/kolektibilitas/history/{id}', [KolektibilitasHistoryController::class, 'show'])->name('kolektibilitas.history.show');
            });
});