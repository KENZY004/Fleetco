<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\TelematicsLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PlaybackController extends Controller
{
    public function getHistory(Vehicle $vehicle)
    {
        // Fetch logs for the last 24 hours
        $logs = TelematicsLog::where('vehicle_id', $vehicle->id)
            ->where('captured_at', '>=', Carbon::now()->subHours(24))
            ->orderBy('captured_at', 'asc')
            ->get();

        $path = $logs->map(function($log) {
            return [
                'lat' => $log->location->latitude,
                'lng' => $log->location->longitude,
                'speed' => $log->speed,
                'time' => $log->captured_at->format('H:i:s'),
                'raw_time' => $log->captured_at->toIso8601String()
            ];
        });

        return response()->json([
            'vehicle_name' => $vehicle->name,
            'path' => $path
        ]);
    }
}
