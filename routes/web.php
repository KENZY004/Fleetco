<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing');
})->name('landing');

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/api/vehicles', [DashboardController::class, 'getVehicleStatus']);
