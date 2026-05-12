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
            
        return view('driver.dashboard', compact('riskScore', 'currentLog', 'vehicle', 'speedLimit', 'distanceKM', 'incidents', 'activeRoute'));
    }
}
