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
        
        $currentLog = $driver ? \App\Models\DutyLog::where('driver_id', $driver->id)
            ->whereNull('ended_at')
            ->first() : null;
            
        $vehicle = $driver ? $driver->vehicle : null;
        
        $speedLimit = \App\Models\Setting::get('global_speed_limit', 100);
            
        $isOffDuty = !$currentLog || $currentLog->status === 'off_duty';
            
        // Distance KM calculation
        $distanceKM = 0;
        if ($currentLog && $driver && !$isOffDuty) {
            $distanceKM = \Illuminate\Support\Facades\DB::table('driver_telemetry')
                ->where('driver_id', $driver->id)
                ->where('captured_at', '>=', $currentLog->started_at)
                ->sum('speed_kmh') * (4 / 3600); 
            $distanceKM = round($distanceKM, 1);
        }
        
        $incidents = 0; 

        $activeRoute = $driver ? \App\Models\FleetRoute::where('driver_id', $driver->id)
            ->where('status', 'active')
            ->first() : null;

        // Calculate accumulated time
        $accumulatedSeconds = 0;
        $previousSeconds = 0;
        $currentSegmentStart = null;

        if (!$isOffDuty && $driver) {
            $todayLogs = \App\Models\DutyLog::where('driver_id', $driver->id)
                ->where('status', 'on_duty')
                ->whereDate('started_at', today())
                ->get();

            foreach ($todayLogs as $log) {
                $end = $log->ended_at ?? now();
                $accumulatedSeconds += $log->started_at->diffInSeconds($end);
            }

            $currentSegmentStart = ($currentLog->status === 'on_duty')
                ? $currentLog->started_at->toIso8601String()
                : null;

            $previousSeconds = $accumulatedSeconds;
            if ($currentSegmentStart) {
                $previousSeconds -= $currentLog->started_at->diffInSeconds(now());
            }
        }

        return view('driver.dashboard', compact(
            'riskScore', 'currentLog', 'vehicle', 'speedLimit',
            'distanceKM', 'incidents', 'activeRoute',
            'accumulatedSeconds', 'currentSegmentStart', 'previousSeconds'
        ));
    }
}
