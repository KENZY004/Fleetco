<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OnboardingController;

Route::get('/', function () {
    return view('welcome');
});

// Onboarding Initialization
Route::middleware(['auth'])->group(function () {
    Route::get('/initialize', [OnboardingController::class, 'index'])->name('onboarding.index');
    Route::post('/initialize', [OnboardingController::class, 'store'])->name('onboarding.store');
});

Route::middleware(['auth', 'verified', 'onboarding'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Fleet Asset Pages
    Route::get('/vehicles', [VehicleController::class, 'index'])->name('vehicles.index');
    Route::get('/issues', [\App\Http\Controllers\IssueController::class, 'index'])->name('issues.index');
    Route::post('/issues/{issue}/resolve', [\App\Http\Controllers\IssueController::class, 'resolve'])->name('issues.resolve');

    // Admin Only Routes
    Route::middleware(['admin'])->group(function () {
        Route::get('/vehicles/create', [VehicleController::class, 'create'])->name('vehicles.create');
        Route::post('/vehicles', [VehicleController::class, 'store'])->name('vehicles.store');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/vehicles/{vehicle}/track', [VehicleController::class, 'track'])->name('vehicles.track');
});

// Public Secure Tracking Link
Route::get('/track/{hash}', [VehicleController::class, 'trackPublic'])->name('vehicles.track.public');

require __DIR__.'/auth.php';
