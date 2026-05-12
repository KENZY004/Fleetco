<?php

namespace App\Services;

use App\Models\Vehicle;
use App\Models\TelematicsLog;
use App\Models\RiskEvent;
use App\Models\Landmark;
use App\Models\Trip;
use Clickbar\Magellan\Data\Geometries\Point;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TelemetryProcessor
{
    protected $riskEngine;

    public function __construct(RiskEngineService $riskEngine)
    {
        $this->riskEngine = $riskEngine;
    }

    /**
     * Process incoming telemetry data for a vehicle.
     */
    public function process(Vehicle $vehicle, array $data): TelematicsLog
    {
        \Illuminate\Support\Facades\Log::info("Telemetry: Processing ping for Vehicle {$vehicle->license_plate}");
        // 1. Create the Telematics Log
        $location = Point::makeGeodetic($data['lat'], $data['lng']);
        
        $log = TelematicsLog::create([
            'vehicle_id' => $vehicle->id,
            'driver_id' => $vehicle->current_driver_id ?? null,
            'location' => $location,
            'speed' => $data['speed'],
            'heading' => $data['heading'],
            'captured_at' => $data['captured_at'] ?? now(),
        ]);

        // 2. Update Vehicle Status
        $vehicle->updateStatusFromTelemetry($data['speed']);

        // 3. Handle Trip Logic
        $this->handleTrip($vehicle, $log, $data);

        // 4. Analyze Behavioral Risk (Speeding, Geofencing, etc.)
        $this->riskEngine->analyze($log);

        // 5. Broadcast for Real-Time Dashboard
        event(new \App\Events\VehicleLocationUpdated($log));

        return $log;
    }

    /**
     * Manage trips for the vehicle.
     */
    protected function handleTrip(Vehicle $vehicle, TelematicsLog $log, array $data): void
    {
        $lastLog = $vehicle->telematicsLogs()
            ->where('id', '!=', $log->id)
            ->latest('captured_at')
            ->first();

        $currentTrip = Trip::where('vehicle_id', $vehicle->id)
            ->whereNull('end_time')
            ->first();

        // 1. Detect Trip Start
        if (!$currentTrip && $log->speed > 0) {
            $currentTrip = Trip::create([
                'vehicle_id' => $vehicle->id,
                'driver_id'  => $vehicle->current_driver_id,
                'start_time' => $log->captured_at,
            ]);
        }

        // 2. Update Trip Distance & Average Speed
        if ($currentTrip && $lastLog) {
            $distanceKm = 0;

            $distance = DB::selectOne("SELECT ST_Distance(?::geography, ?::geography) as distance", [$lastLog->location, $log->location])->distance;
            $distanceKm = $distance / 1000;

            // GPS Sanity Check: A single ping should never cover more than 0.5km
            // (that would mean traveling 360 km/h between pings). Discard GPS glitches.
            if ($distanceKm < 0.5) {
                $currentTrip->increment('distance', $distanceKm);
                $vehicle->increment('odometer', $distanceKm);
            }
            
            $totalTimeHours = $currentTrip->start_time->diffInSeconds($log->captured_at) / 3600;
            if ($totalTimeHours > 0) {
                $currentTrip->update([
                    'average_speed' => $currentTrip->distance / $totalTimeHours
                ]);
            }
        }

        // 3. Detect Trip End (Idle for > 2 mins for testing)
        if ($currentTrip && $lastLog) {
            $idleDuration = $lastLog->captured_at->diffInMinutes($log->captured_at);
            if ($idleDuration > 2 && $log->speed == 0) {
                $currentTrip->update(['end_time' => $log->captured_at]);
            }
        }
    }

    protected function checkSpeeding(Vehicle $vehicle, TelematicsLog $log): void
    {
        $speedLimit = \App\Models\Setting::get('speed_limit', 80);
        $threshold = $speedLimit;

        if ($log->speed > $threshold) {
            RiskEvent::create([
                'vehicle_id' => $vehicle->id,
                'driver_id' => $log->driver_id,
                'telematics_log_id' => $log->id,
                'type' => 'speeding',
                'impact_score' => 10.0,
                'details' => ['speed' => $log->speed, 'limit' => $speedLimit],
                'occurred_at' => $log->captured_at,
            ]);
        }
    }

    protected function checkGeofences(Vehicle $vehicle, TelematicsLog $log): void
    {
        $landmarks = Landmark::stWhere(\Clickbar\Magellan\Database\PostgisFunctions\ST::contains('area', $log->location), true)->get();

        foreach ($landmarks as $landmark) {
            RiskEvent::create([
                'vehicle_id' => $vehicle->id,
                'driver_id' => $log->driver_id,
                'telematics_log_id' => $log->id,
                'type' => 'geofence_entry',
                'impact_score' => 0,
                'details' => ['landmark_id' => $landmark->id, 'landmark_name' => $landmark->name],
                'occurred_at' => $log->captured_at,
            ]);
        }
    }

    public function stopSession(Vehicle $vehicle): void
    {
        $currentTrip = Trip::where('vehicle_id', $vehicle->id)
            ->whereNull('end_time')
            ->first();

        if ($currentTrip) {
            $currentTrip->update(['end_time' => now()]);
        }
    }
}
