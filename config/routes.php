<?php

use App\Controllers\AuthenticationsController;
use App\Controllers\DriversController;
use App\Controllers\FleetsController;
use App\Controllers\HomeController;
use App\Controllers\ManagersController;
use Core\Router\Route;

// Authentication
Route::get('/', [AuthenticationsController::class, 'checkLogin'])->name('auth.check');
Route::get('/login', [AuthenticationsController::class, 'new'])->name('users.login');
Route::post('/login', [AuthenticationsController::class, 'authenticate'])->name('users.authenticate');

Route::middleware('auth')->group(function () {
    Route::get('/logout', [AuthenticationsController::class, 'destroy'])->name('users.logout');

    Route::middleware('driver')->group(function () {
        Route::get('/driver', [DriversController::class, 'index'])->name('driver.index');
    });

    Route::middleware('manager')->group(function () {
        // FLEETS
        // Create
        Route::get('/manager/fleets/new', [FleetsController::class, 'new'])->name('fleets.new');
        Route::post('/manager/fleets', [FleetsController::class, 'create'])->name('fleets.create');

        // Retrieve
        Route::get('/manager/fleets', [FleetsController::class, 'index'])->name('fleets.index');
        Route::get('/manager/fleets/{id}', [FleetsController::class, 'show'])->name('fleets.show');
        
        // Update
        Route::get('/manager/fleets/{id}/edit', [FleetsController::class, 'edit'])->name('fleets.edit');
        Route::put('/manager/fleets/{id}', [FleetsController::class, 'update'])->name('fleets.update');

        // Delete
        Route::delete('/manager/fleets/{id}', [FleetsController::class, 'destroy'])->name('fleets.destroy');
    });
});
