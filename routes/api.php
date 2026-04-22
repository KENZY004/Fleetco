<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\TelematicsController;

use App\Http\Controllers\Api\PlaybackController;

Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
});

Route::post('/telematics', [TelematicsController::class, 'store']);
Route::get('/vehicles/{vehicle}/playback', [PlaybackController::class, 'getHistory']);
