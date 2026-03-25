<?php

use App\Http\Controllers\Admin\SoftwareAdminController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\SoftwareController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/software-download', [SoftwareController::class, 'index'])->name('software.index');
    Route::post('/api/check-version', [SoftwareController::class, 'checkSoftware']);
});



// Admin Protected Routes
Route::middleware(['auth', 'verified'])->prefix('admin')->group(function () {

    Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
    Route::post('/users', [UserController::class, 'store']);
    Route::put('/users/{user}', [UserController::class, 'update']);
    Route::delete('/users/{user}', [UserController::class, 'destroy']);
});

Route::middleware(['auth', 'verified', 'can:admin-only'])->prefix('admin')->group(function () {
    Route::get('/software-versions', [SoftwareAdminController::class, 'index'])->name('admin.software.index');
    Route::post('/software-versions', [SoftwareAdminController::class, 'store']);
    Route::put('/software-versions/{softwareVersion}', [SoftwareAdminController::class, 'update']);
    Route::delete('/software-versions/{softwareVersion}', [SoftwareAdminController::class, 'destroy']);
});


require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
