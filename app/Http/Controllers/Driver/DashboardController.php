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
            
        $isOffDuty = !$currentLog || $currentLog->status === 'off_duty';
            
        // Distance KM calculation
        $distanceKM = 0;
        if ($currentLog && $driver && !$isOffDuty) {
            $distanceKM = \Illuminate\Support\Facades\DB::table('driver_telemetry')
                ->where('driver_id', $user->id)
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
            // Find the most recent 'off_duty' log for this driver
            $lastOffDuty = \App\Models\DutyLog::where('driver_id', $user->id)
                ->where('status', 'off_duty')
                ->latest('started_at')
                ->first();

            // Get all segments since that last off_duty event
            $query = \App\Models\DutyLog::where('driver_id', $user->id)
                ->where('status', 'on_duty');
            
            if ($lastOffDuty) {
                $query->where('started_at', '>', $lastOffDuty->started_at);
            }

            $currentShiftLogs = $query->get();

            foreach ($currentShiftLogs as $log) {
                // Only count completed segments for previousSeconds
                if ($log->ended_at) {
                    $previousSeconds += $log->started_at->diffInSeconds($log->ended_at);
                }
            }

            if ($currentLog->status === 'on_duty') {
                $currentSegmentStart = $currentLog->started_at->timestamp * 1000;
            }
            
            $accumulatedSeconds = $previousSeconds;
            if ($currentLog->status === 'on_duty') {
                $accumulatedSeconds += $currentLog->started_at->diffInSeconds(now());
            }
        }

        // Fetch Maintenance History
        $maintenanceHistory = $driver ? \App\Models\MaintenanceRecord::where('driver_id', $driver->id)
            ->latest()
            ->take(5)
            ->get() : collect();

        return view('driver.dashboard', compact(
            'riskScore', 'currentLog', 'vehicle', 'speedLimit',
            'distanceKM', 'incidents', 'activeRoute',
            'accumulatedSeconds', 'currentSegmentStart', 'previousSeconds',
            'maintenanceHistory'
        ));
    }
}
