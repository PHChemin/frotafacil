<?php

use App\Controllers\AuthenticationsController;
use App\Controllers\DriversController;
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
        Route::get('/manager', [ManagersController::class, 'index'])->name('manager.index');
    });
});
