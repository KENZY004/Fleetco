<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\TelematicsController;

use App\Http\Controllers\Api\PlaybackController;

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;

RateLimiter::for('telematics', function (Request $request) {
    return Limit::perMinute(60)->by($request->token);
});

Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
});

Route::post('/telematics', [TelematicsController::class, 'store'])->middleware('throttle:telematics');
Route::post('/telematics/stop', [TelematicsController::class, 'stop'])->middleware('throttle:telematics');
Route::get('/vehicles/{vehicle}/playback', [PlaybackController::class, 'getHistory']);
