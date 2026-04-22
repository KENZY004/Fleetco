<?php

namespace App\Services;

use App\Models\TelematicsLog;
use App\Models\RiskEvent;
use App\Models\Driver;
use App\Models\Landmark;
use App\Models\Vehicle;
use Illuminate\Support\Facades\DB;
use Clickbar\Magellan\Data\Geometries\Point;

class RiskEngineService
{
    /**
     * Entry point for analyzing a new telematics ping.
     */
    public function analyze(TelematicsLog $log): void
    {
        $driver = $log->driver;
        if (!$driver) return;

        // 1. Check for Speeding
        $this->detectSpeeding($log, $driver);

        // 2. Check for Geofence Breaches
        $this->detectGeofenceBreach($log, $driver);
    }

    /**
     * Logic for penalizing speed deltas.
     */
    protected function detectSpeeding(TelematicsLog $log, Driver $driver): void
    {
        $speedLimit = 100; // In a real app, this would be dynamic based on road data or meta
        $threshold = $speedLimit + 10;

        if ($log->speed > $threshold) {
            $impact = 5.00;
            
            RiskEvent::create([
                'driver_id' => $driver->id,
                'vehicle_id' => $log->vehicle_id,
                'telematics_log_id' => $log->id,
                'type' => 'speeding',
                'impact_score' => $impact,
                'details' => [
                    'speed' => $log->speed,
                    'limit' => $speedLimit,
                    'excess' => $log->speed - $speedLimit
                ],
                'occurred_at' => $log->captured_at,
            ]);

            $this->applyScorePenalty($driver, $impact);
        }
    }

    /**
     * Spatial Intelligence: Detecting unauthorized entry/exit.
     */
    protected function detectGeofenceBreach(TelematicsLog $log, Driver $driver): void
    {
        // Use Magellan to find if the current location intersects with any 'restricted' landmark
        $restrictedLandmarks = Landmark::query()
            ->where('type', 'restricted')
            ->whereContains('area', $log->location)
            ->get();

        foreach ($restrictedLandmarks as $landmark) {
            $impact = 10.00;

            RiskEvent::create([
                'driver_id' => $driver->id,
                'vehicle_id' => $log->vehicle_id,
                'telematics_log_id' => $log->id,
                'type' => 'geofence_breach',
                'impact_score' => $impact,
                'details' => [
                    'landmark_name' => $landmark->name,
                    'landmark_id' => $landmark->id,
                ],
                'occurred_at' => $log->captured_at,
            ]);

            $this->applyScorePenalty($driver, $impact);
        }
    }

    /**
     * Mutate the driver's risk score.
     */
    protected function applyScorePenalty(Driver $driver, float $impact): void
    {
        $driver->decrement('risk_score', $impact);
        
        // Ensure score doesn't drop below 0
        if ($driver->risk_score < 0) {
            $driver->update(['risk_score' => 0]);
        }
    }
}
