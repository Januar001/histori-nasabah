<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PetugasController;
use App\Http\Controllers\NasabahController;
use App\Http\Controllers\KolektibilitasHistoryController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\AnalisisController;
use App\Http\Controllers\AssignmentController;
use App\Http\Middleware\AdminMiddleware;

Route::get('/', [AuthController::class, 'ShowLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware([AdminMiddleware::class])->group(function () {
        Route::resource('petugas', PetugasController::class);
        
        Route::get('/import', [ImportController::class, 'showForm'])->name('import.form');
        Route::post('/import', [ImportController::class, 'import'])->name('import.run');

        Route::get('/kolektibilitas/history', [KolektibilitasHistoryController::class, 'index'])->name('kolektibilitas.history');

        Route::get('/analisis/kolektibilitas-murni', [AnalisisController::class, 'kolektibilitasMurni'])->name('analisis.kolektibilitas-murni');
        
        Route::get('/assignment', [AssignmentController::class, 'index'])->name('assignment.index');
        Route::post('/assignment/bulk', [AssignmentController::class, 'assignBulk'])->name('assignment.bulk');
        Route::post('/assignment/individual/{nasabah}', [AssignmentController::class, 'assignIndividual'])->name('assignment.individual');
    });

    Route::get('/nasabah', [NasabahController::class, 'index'])->name('nasabah.index');
    Route::get('/nasabah/{nasabah}', [NasabahController::class, 'show'])->name('nasabah.show');
});