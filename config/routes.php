<?php

use App\Controllers\AuthenticationsController;
use App\Controllers\DriversController;
use App\Controllers\DriversProfileController;
use App\Controllers\FleetsController;
use App\Controllers\TrucksController;
use Core\Router\Route;

// Authentication
Route::get('/', [AuthenticationsController::class, 'checkLogin'])->name('auth.check');
Route::get('/login', [AuthenticationsController::class, 'new'])->name('users.login');
Route::post('/login', [AuthenticationsController::class, 'authenticate'])->name('users.authenticate');

Route::middleware('auth')->group(function () {
    Route::get('/logout', [AuthenticationsController::class, 'destroy'])->name('users.logout');

    Route::middleware('driver')->group(function () {
        Route::get('/driver', [DriversController::class, 'index'])->name('driver.index');

        // PROFILE
        Route::get('/driver/profile', [DriversProfileController::class, 'show'])->name('driver.profile.show');
        Route::put('/driver/profile/{driver_id}', [DriversProfileController::class, 'update'])->name('driver.profile.update');
        Route::post('/driver/profile/avatar', [DriversProfileController::class, 'updateAvatar'])->name('driver.profile.avatar');
    });

    Route::middleware('manager')->group(function () {
        // FLEETS
        Route::get('/manager/fleets/new', [FleetsController::class, 'new'])->name('fleets.new');
        Route::post('/manager/fleets', [FleetsController::class, 'create'])->name('fleets.create');

        Route::get('/manager/fleets', [FleetsController::class, 'index'])->name('fleets.index');
        Route::get('/manager/fleets/{fleet_id}', [FleetsController::class, 'show'])->name('fleets.show');

        Route::get('/manager/fleets/{fleet_id}/edit', [FleetsController::class, 'edit'])->name('fleets.edit');
        Route::put('/manager/fleets/{fleet_id}', [FleetsController::class, 'update'])->name('fleets.update');

        Route::delete('/manager/fleets/{fleet_id}', [FleetsController::class, 'destroy'])->name('fleets.destroy');


        // TRUCKS
        Route::get('/manager/fleets/{fleet_id}/trucks/new', [TrucksController::class, 'new'])->name('trucks.new');
        Route::post('/manager/fleets/{fleet_id}/trucks', [TrucksController::class, 'create'])->name('trucks.create');

        Route::get('/manager/fleets/{fleet_id}/trucks/{truck_id}', [TrucksController::class, 'show'])->name('trucks.show');

        Route::get('/manager/fleets/{fleet_id}/trucks/{truck_id}/edit', [TrucksController::class, 'edit'])->name('trucks.edit');
        Route::put('/manager/fleets/{fleet_id}/trucks/{truck_id}', [TrucksController::class, 'update'])->name('trucks.update');

        Route::delete('/manager/fleets/{fleet_id}/trucks/{truck_id}', [TrucksController::class, 'destroy'])
            ->name('trucks.destroy');
    });
});
