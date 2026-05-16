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

// Temporary debug route for Render Free Tier - DELETE AFTER USE
Route::get('/debug-db-fix', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => 'FleetSeeder']);
        $users = \App\Models\User::all(['name', 'email', 'role']);
        return response()->json([
            'status' => 'success',
            'message' => 'Database seeded and users retrieved',
            'users' => $users
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
});

Route::middleware(['auth'])->group(function () {
    // Shared Replay Access
    Route::get('/trips/{trip}', [App\Http\Controllers\TripController::class, 'show'])->name('trips.show');

    // Admin & Fleet Manager Routes
    Route::middleware(['admin'])->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/api/vehicles', [DashboardController::class, 'getVehicleStatus']);
        Route::get('/api/alerts', [DashboardController::class, 'getSecurityAlerts']);
        Route::post('/api/alerts/resolve-all', [DashboardController::class, 'resolveAll']);
        Route::post('/api/alerts/{alert}/resolve', [DashboardController::class, 'resolveAlert']);

        // Driver Management
        Route::resource('drivers', DriverController::class)->except(['create', 'edit']);
        Route::patch('/drivers/{driver}/reset-score', [DriverController::class, 'resetScore'])->name('drivers.reset-score');
        Route::post('/drivers/{driver}/resolve-alerts', [DriverController::class, 'resolveAlerts'])->name('drivers.resolve-alerts');

        // Vehicle Management
        Route::resource('vehicles', VehicleController::class)->except(['create', 'edit']);
        Route::patch('/vehicles/{vehicle}/regenerate-token', [VehicleController::class, 'regenerateToken'])->name('vehicles.regenerate-token');
        Route::get('/vehicles/{vehicle}/maintenance', [VehicleMaintenanceController::class, 'index'])->name('vehicles.maintenance');
        Route::post('/vehicles/{vehicle}/maintenance', [VehicleMaintenanceController::class, 'store'])->name('vehicles.maintenance.store');
        Route::post('/maintenance/{record}/resolve', [VehicleMaintenanceController::class, 'resolve'])->name('vehicles.maintenance.resolve');

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
        Route::delete('/trips/{trip}', [TripController::class, 'destroy'])->name('trips.destroy');
        Route::delete('/trips-clear', [TripController::class, 'clearAll'])->name('trips.clear');

        // Global Settings
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');


        // Fleet Manager invitation
        Route::post('/fleet/invite/send', [App\Http\Controllers\InviteController::class, 'send'])->name('fleet.invite.send');
        Route::delete('/fleet/invite/{id}', [App\Http\Controllers\InviteController::class, 'revoke'])->name('fleet.invite.revoke');
    });

    // Driver Co-Pilot Routes
    Route::middleware(['driver'])->prefix('driver')->name('driver.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Driver\DashboardController::class, 'index'])->name('dashboard');
    
        // Duty Status
        Route::post('/duty/on', [App\Http\Controllers\Driver\DutyController::class, 'goOnDuty'])->name('duty.on');
        Route::post('/duty/break', [App\Http\Controllers\Driver\DutyController::class, 'takeBreak'])->name('duty.break');
        Route::post('/duty/off', [App\Http\Controllers\Driver\DutyController::class, 'goOffDuty'])->name('duty.off');
        
        // Maintenance
        Route::get('/maintenance', [App\Http\Controllers\Driver\MaintenanceController::class, 'index'])->name('maintenance.index');
        Route::post('/maintenance', [App\Http\Controllers\Driver\MaintenanceController::class, 'store'])->name('maintenance.store');
        
        // Telemetry
        Route::post('/telemetry', [App\Http\Controllers\Driver\TelemetryController::class, 'store'])->name('telemetry.store');

        // Sidebar Pages
        Route::get('/vehicle', [App\Http\Controllers\Driver\VehicleController::class, 'index'])->name('vehicle');
        Route::get('/trips', [App\Http\Controllers\Driver\TripController::class, 'index'])->name('trips');
        Route::get('/risk', [App\Http\Controllers\Driver\RiskController::class, 'index'])->name('risk');
        
        // Profile
        Route::get('/profile', [App\Http\Controllers\Driver\ProfileController::class, 'index'])->name('profile');
        Route::put('/profile', [App\Http\Controllers\Driver\ProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [App\Http\Controllers\Driver\ProfileController::class, 'updatePassword'])->name('profile.password');
    });

    // All Authenticated Users
    // Unassigned Drivers
    Route::get('/unassigned', function() {
        return view('unassigned');
    })->name('unassigned');

    Route::get('/track-me', function() {
        return view('track-me');
    })->name('track-me');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    // Fleet Route Management (Admin/Manager)
    Route::prefix('fleet/routes')->name('fleet.routes.')->group(function() {
        Route::get('/', [App\Http\Controllers\Fleet\RouteController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Fleet\RouteController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Fleet\RouteController::class, 'store'])->name('store');
        Route::post('/{id}/assign', [App\Http\Controllers\Fleet\RouteController::class, 'assign'])->name('assign');
        Route::get('/{id}', [App\Http\Controllers\Fleet\RouteController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [App\Http\Controllers\Fleet\RouteController::class, 'edit'])->name('edit');
        Route::patch('/{id}', [App\Http\Controllers\Fleet\RouteController::class, 'update'])->name('update');
        Route::delete('/{id}', [App\Http\Controllers\Fleet\RouteController::class, 'destroy'])->name('destroy');
    });

    // Driver Route Actions
    Route::post('/driver/route/{routeId}/waypoint/{order}/reach', [App\Http\Controllers\Driver\RouteController::class, 'markWaypointReached'])->name('driver.route.waypoint.reach');
});

require __DIR__.'/auth.php';

// Driver Invitation Routes (Public — no guest/auth middleware, token validates itself)
Route::get('/register/invite/{token}', [App\Http\Controllers\InviteController::class, 'showInviteRegistration'])->name('register.invite');
Route::post('/register/invite', [App\Http\Controllers\InviteController::class, 'storeInviteRegistration'])->name('register.invite.store');

Route::get('/join', function() {
    return view('join');
})->name('join');
