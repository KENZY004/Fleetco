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
        
        // Fetch last 20 trips
        $trips = Trip::where('driver_id', $user->id)
            ->orderBy('start_time', 'desc')
            ->take(20)
            ->get();
            
        // Calculate stats
        $totalTrips = Trip::where('driver_id', $user->id)->count();
        $totalDistance = Trip::where('driver_id', $user->id)->sum('distance');
        
        // Driver's current risk score used as Avg Safety Score
        $avgSafetyScore = $user->driver ? $user->driver->risk_score : 100;
        
        $lastTrip = $trips->first() ? $trips->first()->start_time->diffForHumans() : 'Never';

        return view('driver.trips.index', compact('trips', 'totalTrips', 'totalDistance', 'avgSafetyScore', 'lastTrip'));
    }
}
