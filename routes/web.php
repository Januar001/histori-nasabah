<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\NasabahController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JanjiBayarController;

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

// Protected Routes
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/', [DashboardController::class, 'index']);

    // Nasabah Routes
    Route::get('/nasabah', [NasabahController::class, 'index'])->name('nasabah.index');
    Route::get('/nasabah/{id}', [NasabahController::class, 'show'])->name('nasabah.show');

    // Import Routes (Admin Only)
    Route::middleware('admin')->group(function () {
        Route::get('/import', [ImportController::class, 'showImport'])->name('import.show');
        Route::post('/import', [ImportController::class, 'importExcel'])->name('import.excel');
    });

    // Janji Bayar Routes
    Route::post('/janji-bayar', [JanjiBayarController::class, 'store'])->name('janji-bayar.store');
    Route::post('/janji-bayar/{id}/status', [JanjiBayarController::class, 'updateStatus'])->name('janji-bayar.update-status');
});

// TEST ROUTES - Hapus nanti setelah fix
Route::get('/test-session', function() {
    session()->put('test_message', 'Hello Session!');
    return redirect('/test-session-result');
});

Route::get('/test-session-result', function() {
    $message = session('test_message', 'No session message');
    return "Session Test: " . $message;
});
