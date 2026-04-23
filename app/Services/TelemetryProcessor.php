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
    /**
     * Process incoming telemetry data for a vehicle.
     */
    public function process(Vehicle $vehicle, array $data): TelematicsLog
    {
        // 1. Create the Telematics Log
        $location = Point::makeGeodetic($data['lat'], $data['lng']);
        
        $log = TelematicsLog::create([
            'vehicle_id' => $vehicle->id,
            'driver_id' => $vehicle->current_driver_id ?? null,
            'location' => DB::getDriverName() === 'sqlite' ? "POINT({$data['lng']} {$data['lat']})" : $location,
            'speed' => $data['speed'],
            'heading' => $data['heading'],
            'captured_at' => $data['captured_at'] ?? now(),
        ]);

        // 2. Update Vehicle Status
        $vehicle->updateStatusFromTelemetry($data['speed']);

        // 3. Handle Trip Logic
        $this->handleTrip($vehicle, $log);

        // 4. Check for Speeding Alert
        $this->checkSpeeding($vehicle, $log);

        // 5. Check for Geofence Alerts
        $this->checkGeofences($vehicle, $log);

        return $log;
    }

    /**
     * Manage trips for the vehicle.
     */
    protected function handleTrip(Vehicle $vehicle, TelematicsLog $log): void
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
                'start_time' => $log->captured_at,
            ]);
        }

        // 2. Update Trip Distance & Average Speed
        if ($currentTrip && $lastLog) {
            $distanceKm = 0;

            if (DB::getDriverName() === 'sqlite') {
                // Parse location from WKT if needed, or just use the log attributes
                // On SQLite, we saved it as WKT string.
                // For simplicity, we can assume the Point object can be reconstructed or we use lat/lng from the log if they were columns.
                // But we only have 'location' as text.
                
                // Let's assume we can get lat/lng from the Point object we just created if it's the current log,
                // and for the last log we parse the WKT.
                
                $lat2 = $log->location instanceof Point ? $log->location->getLatitude() : 0; // This might be tricky if it was saved as string
                // Actually, $log->location might be a string now.
                
                // Let's just use the $data lat/lng for the current log
                $lat2 = $data['lat'] ?? 0;
                $lng2 = $data['lng'] ?? 0;
                
                // For last log, we parse WKT: POINT(lng lat)
                $lastLocation = $lastLog->location;
                if (is_string($lastLocation) && preg_match('/POINT\((.+) (.+)\)/', $lastLocation, $matches)) {
                    $lng1 = $matches[1];
                    $lat1 = $matches[2];
                    $distanceKm = sqrt(pow($lat2 - $lat1, 2) + pow($lng2 - $lng1, 2)) * 111.32;
                }
            } else {
                // Calculate distance in meters using PostGIS (SRID 4326 is geodetic, distance is in meters)
                $distance = DB::selectOne("SELECT ST_Distance(
                    ?::geography, 
                    ?::geography
                ) as distance", [$lastLog->location, $log->location])->distance;

                $distanceKm = $distance / 1000;
            }

            $currentTrip->increment('distance', $distanceKm);
            
            // Simple average speed update (total distance / total time)
            $totalTimeHours = $currentTrip->start_time->diffInSeconds($log->captured_at) / 3600;
            if ($totalTimeHours > 0) {
                $currentTrip->update([
                    'average_speed' => $currentTrip->distance / $totalTimeHours
                ]);
            }
        }

        // 3. Detect Trip End (Idle for > 15 mins)
        if ($currentTrip && $lastLog) {
            $idleDuration = $lastLog->captured_at->diffInMinutes($log->captured_at);
            if ($idleDuration > 15 && $log->speed == 0) {
                $currentTrip->update(['end_time' => $log->captured_at]);
            }
        }
    }

    /**
     * Check if the vehicle is exceeding the speed limit.
     */
    protected function checkSpeeding(Vehicle $vehicle, TelematicsLog $log): void
    {
        $speedLimit = 100; // Hardcoded for now, can be dynamic later

        if ($log->speed > $speedLimit) {
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

    /**
     * Check if the vehicle has entered or exited any geofences.
     */
    protected function checkGeofences(Vehicle $vehicle, TelematicsLog $log): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return; // Skip spatial check on SQLite for now
        }

        // Use Magellan stWhere macro to find landmarks containing this point
        $landmarks = Landmark::stWhere(\Clickbar\Magellan\Database\PostgisFunctions\ST::contains('area', $log->location), true)->get();

        foreach ($landmarks as $landmark) {
            // Check if we already have a recent 'geofence_entry' for this landmark and vehicle
            // to avoid spamming alerts. This is a simplified version.
            RiskEvent::create([
                'vehicle_id' => $vehicle->id,
                'driver_id' => $log->driver_id,
                'telematics_log_id' => $log->id,
                'type' => 'geofence_entry',
                'impact_score' => 0, // Not necessarily negative
                'details' => ['landmark_id' => $landmark->id, 'landmark_name' => $landmark->name],
                'occurred_at' => $log->captured_at,
            ]);
        }
    }
}
