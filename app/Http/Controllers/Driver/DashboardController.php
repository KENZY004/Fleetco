<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $driver = $user->driver;
        $riskScore = $driver ? $driver->risk_score : 100;
        
        $currentLog = \App\Models\DutyLog::where('driver_id', $user->id)
            ->whereNull('ended_at')
            ->first();
            
        $vehicle = $driver ? $driver->vehicle : null;
        
        $speedLimit = \App\Models\Setting::get('global_speed_limit', 100);
            
        // Distance KM calculation
        $distanceKM = 0;
        if ($currentLog) {
            $distanceKM = \Illuminate\Support\Facades\DB::table('driver_telemetry')
                ->where('driver_id', $user->id)
                ->where('captured_at', '>=', today())
                ->sum('speed_kmh') * (4 / 3600); // Rough approximation: sum of speeds * 4s ping interval
            $distanceKM = round($distanceKM, 1);
        }
        
        $incidents = 0; // Set to 0 for now

        $activeRoute = \App\Models\FleetRoute::where('driver_id', $user->id)
            ->where('status', 'active')
            ->first();

        // Calculate total accumulated ON DUTY seconds for today (across all segments, including breaks)
        $todayLogs = \App\Models\DutyLog::where('driver_id', $user->id)
            ->where('status', 'on_duty')
            ->whereDate('started_at', today())
            ->get();

        $accumulatedSeconds = 0;
        foreach ($todayLogs as $log) {
            $end = $log->ended_at ?? now();
            $accumulatedSeconds += $log->started_at->diffInSeconds($end);
        }

        // If currently on duty, the current segment's start time is needed for the live ticker offset
        $currentSegmentStart = ($currentLog && $currentLog->status === 'on_duty')
            ? $currentLog->started_at->toIso8601String()
            : null;

        // Total seconds before current segment (so JS can add live seconds on top)
        $previousSeconds = $accumulatedSeconds;
        if ($currentSegmentStart) {
            // Subtract current segment from accumulated (JS will add it back live)
            $previousSeconds -= $currentLog->started_at->diffInSeconds(now());
        }

        return view('driver.dashboard', compact(
            'riskScore', 'currentLog', 'vehicle', 'speedLimit',
            'distanceKM', 'incidents', 'activeRoute',
            'accumulatedSeconds', 'currentSegmentStart', 'previousSeconds'
        ));
    }
}
