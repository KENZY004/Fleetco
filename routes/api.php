<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\TelematicsController;

use App\Http\Controllers\Api\PlaybackController;

Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/telematics', [TelematicsController::class, 'store']);
});

// Endpoints consumed by frontend (using session or open)
Route::get('/vehicles/{vehicle}/playback', [PlaybackController::class, 'getHistory']);
Route::apiResource('issues', \App\Http\Controllers\Api\IssueController::class);
Route::apiResource('landmarks', \App\Http\Controllers\Api\LandmarkController::class);
Route::get('/geofences', [\App\Http\Controllers\Api\GeofenceController::class, 'index']);
Route::post('/geofences', [\App\Http\Controllers\Api\GeofenceController::class, 'store']);
