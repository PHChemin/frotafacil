<?php

use App\Controllers\AuthenticationsController;
use App\Controllers\HomeController;
use Core\Router\Route;

// Authentication
Route::get('/login', [AuthenticationsController::class, 'new'])->name('users.login');
Route::post('/login', [AuthenticationsController::class, 'authenticate'])->name('users.authenticate');

Route::middleware('auth')->group(function () {
    Route::get('/manager', [HomeController::class, 'managerUser'])->name('users.manager');
    Route::get('/logout', [AuthenticationsController::class, 'destroy'])->name('users.logout');
});