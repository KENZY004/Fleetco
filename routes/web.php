<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\GeofenceController;
use App\Http\Controllers\AlertHistoryController;
use App\Http\Controllers\TripController;
use App\Http\Controllers\VehicleMaintenanceController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing');
})->name('landing');

Route::middleware(['auth'])->group(function () {
    // Admin Only Routes
    Route::middleware(['admin'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/api/vehicles', [DashboardController::class, 'getVehicleStatus']);
        Route::get('/api/alerts', [DashboardController::class, 'getSecurityAlerts']);
        Route::post('/api/alerts/{alert}/resolve', [DashboardController::class, 'resolveAlert']);

        // Driver Management
        Route::resource('drivers', DriverController::class)->except(['create', 'edit']);
        Route::patch('/drivers/{driver}/reset-score', [DriverController::class, 'resetScore'])->name('drivers.reset-score');

        // Vehicle Management
        Route::resource('vehicles', VehicleController::class)->except(['create', 'edit']);
        Route::patch('/vehicles/{vehicle}/regenerate-token', [VehicleController::class, 'regenerateToken'])->name('vehicles.regenerate-token');
        Route::get('/vehicles/{vehicle}/maintenance', [VehicleMaintenanceController::class, 'index'])->name('vehicles.maintenance');
        Route::post('/vehicles/{vehicle}/maintenance', [VehicleMaintenanceController::class, 'store'])->name('vehicles.maintenance.store');

        // Geofence Management
        Route::get('/geofences', [GeofenceController::class, 'index'])->name('geofences.index');
        Route::post('/geofences', [GeofenceController::class, 'store'])->name('geofences.store');
        Route::delete('/geofences/{geofence}', [GeofenceController::class, 'destroy'])->name('geofences.destroy');

        Route::get('/alerts', [AlertHistoryController::class, 'index'])->name('alerts.index');
        Route::delete('/alerts/{alert}', [AlertHistoryController::class, 'destroy'])->name('alerts.destroy');
        Route::patch('/alerts/{alert}/resolve', [AlertHistoryController::class, 'resolve'])->name('alerts.resolve');
        Route::delete('/alerts-clear', [AlertHistoryController::class, 'clearAll'])->name('alerts.clear');

        // Trip Analytics
        Route::get('/trips', [TripController::class, 'index'])->name('trips.index');
        Route::get('/trips/{trip}', [TripController::class, 'show'])->name('trips.show');

        // Global Settings
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');
    });

    // All Authenticated Users
    Route::get('/track-me', function() {
        return view('track-me');
    })->name('track-me');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
