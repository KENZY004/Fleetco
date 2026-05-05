<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\TelematicsLog;
use Illuminate\Http\Request;

class TripController extends Controller
{
    /**
     * Show the detailed replay for a specific trip.
     */
    public function show(Trip $trip)
    {
        // Get all logs between the trip start and end
        $logs = TelematicsLog::where('vehicle_id', $trip->vehicle_id)
            ->where('captured_at', '>=', $trip->start_time)
            ->when($trip->end_time, function($query) use ($trip) {
                return $query->where('captured_at', '<=', $trip->end_time);
            })
            ->orderBy('captured_at', 'asc')
            ->get();

        // Get any risk events during this trip
        $alerts = \App\Models\RiskEvent::where('vehicle_id', $trip->vehicle_id)
            ->where('occurred_at', '>=', $trip->start_time)
            ->when($trip->end_time, function($query) use ($trip) {
                return $query->where('occurred_at', '<=', $trip->end_time);
            })
            ->get();

        return view('trips.show', compact('trip', 'logs', 'alerts'));
    }
}
