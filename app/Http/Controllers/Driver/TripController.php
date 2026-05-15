<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\Trip;
use Illuminate\Http\Request;

class TripController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $driverId = $user->driver?->id;
        
        if (!$driverId) {
             return view('driver.trips.index', [
                'trips' => collect(),
                'totalTrips' => 0,
                'totalDistance' => 0,
                'avgSafetyScore' => 100,
                'lastTrip' => 'Never'
             ]);
        }

        // Fetch last 20 trips
        $trips = Trip::where('driver_id', $driverId)
            ->orderBy('start_time', 'desc')
            ->take(20)
            ->get();
            
        // Calculate stats
        $totalTrips = Trip::where('driver_id', $driverId)->count();
        $totalDistance = Trip::where('driver_id', $driverId)->sum('distance');
        
        // Driver's current risk score used as Avg Safety Score
        $avgSafetyScore = $user->driver ? $user->driver->risk_score : 100;
        
        $lastTrip = $trips->first() ? $trips->first()->start_time->diffForHumans() : 'Never';

        return view('driver.trips.index', compact('trips', 'totalTrips', 'totalDistance', 'avgSafetyScore', 'lastTrip'));
    }
}
