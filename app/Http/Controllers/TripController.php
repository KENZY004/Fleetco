<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\TelematicsLog;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class TripController extends Controller
{
    /**
     * List all recorded trips.
     */
    public function index(Request $request)
    {
        $query = Trip::with('vehicle')->latest('start_time');

        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        $trips    = $query->paginate(20);
        $vehicles = Vehicle::orderBy('name')->get();

        return view('trips.index', compact('trips', 'vehicles'));
    }

    /**
     * Show the detailed replay for a specific trip.
     */
    public function show(Trip $trip)
    {
        $trip->load('vehicle');

        // Get all logs between the trip start and end, shaped for JS
        $logs = TelematicsLog::where('vehicle_id', $trip->vehicle_id)
            ->where('captured_at', '>=', $trip->start_time)
            ->when($trip->end_time, fn($q) => $q->where('captured_at', '<=', $trip->end_time))
            ->orderBy('captured_at', 'asc')
            ->get()
            ->map(fn($log) => [
                'lat'         => $log->location?->getLatitude(),
                'lng'         => $log->location?->getLongitude(),
                'speed'       => $log->speed,
                'captured_at' => $log->captured_at->toIso8601String(),
                'time'        => $log->captured_at->format('H:i:s'),
            ])
            ->filter(fn($p) => $p['lat'] && $p['lng'])  // drop any null-location logs
            ->values();

        // Get risk events during the trip, with their location
        $alerts = \App\Models\RiskEvent::where('vehicle_id', $trip->vehicle_id)
            ->where('occurred_at', '>=', $trip->start_time)
            ->when($trip->end_time, fn($q) => $q->where('occurred_at', '<=', $trip->end_time))
            ->with('telematicsLog')
            ->get()
            ->map(function($alert) {
                // Determine lat/lng from log or fallback to details
                $lat = $alert->telematicsLog?->location?->getLatitude();
                $lng = $alert->telematicsLog?->location?->getLongitude();
                
                // Fallback for SQLite or missing log links
                if (!$lat && isset($alert->details['lat'])) $lat = $alert->details['lat'];
                if (!$lng && isset($alert->details['lng'])) $lng = $alert->details['lng'];

                return [
                    'id'           => $alert->id,
                    'type'         => $alert->type,
                    'details'      => $alert->details ?? [],
                    'occurred_at'  => $alert->occurred_at->toIso8601String(),
                    'time'         => $alert->occurred_at->format('H:i:s'),
                    'lat'          => $lat,
                    'lng'          => $lng,
                ];
            })
            ->values();

        return view('trips.show', compact('trip', 'logs', 'alerts'));
    }

    /**
     * Delete a trip from history.
     */
    public function destroy(Trip $trip)
    {
        $trip->delete();
        return redirect()->back()->with('success', 'Trip record successfully removed from history.');
    }

    /**
     * Wipe all trip history and telemetry breadcrumbs.
     */
    public function clearAll()
    {
        // Use delete() instead of truncate() to avoid Foreign Key constraint issues in SQLite/Postgres
        \App\Models\TelematicsLog::query()->delete();
        \App\Models\Trip::query()->delete();
        
        return redirect()->route('trips.index')->with('success', 'All mission history and telemetry data has been permanently cleared.');
    }
}
