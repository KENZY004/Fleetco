<?php

namespace App\Services;

use App\Models\TelematicsLog;
use App\Models\RiskEvent;
use App\Models\Driver;
use App\Models\Landmark;
use App\Models\Vehicle;
use Illuminate\Support\Facades\DB;
use Clickbar\Magellan\Data\Geometries\Point;
use Illuminate\Support\Facades\Mail;
use App\Mail\SecurityAlert;

class RiskEngineService
{
    /**
     * Entry point for analyzing a new telematics ping.
     */
    public function analyze(TelematicsLog $log): void
    {
        $driver = $log->driver;
        if (!$driver) {
            \Illuminate\Support\Facades\Log::warning("RiskEngine: No driver for log {$log->id}");
            return;
        }

        $violationFound = false;

        // 1. Check for Speeding
        if ($this->detectSpeeding($log, $driver)) {
            $violationFound = true;
        }

        // 2. Check for Geofence Breaches
        if ($this->detectGeofenceBreach($log, $driver)) {
            $violationFound = true;
        }

        // 3. REPUTATION RECOVERY LOGIC
        if (!$violationFound) {
            $driver->increment('clean_pings_count');
            
            // Every 20 clean pings, reward +1 point (up to 100)
            if ($driver->clean_pings_count >= 20) {
                if ($driver->risk_score < 100) {
                    $driver->increment('risk_score', 1.0);
                }
                $driver->update(['clean_pings_count' => 0]);
                \Illuminate\Support\Facades\Log::info("RiskEngine: Driver {$driver->name} earned +1 Reward Point for good behavior!");
            }
        } else {
            // Reset streak on any violation
            $driver->update(['clean_pings_count' => 0]);
        }
    }

    /**
     * Logic for penalizing speed deltas.
     */
    protected function detectSpeeding(TelematicsLog $log, Driver $driver): bool
    {
        $speedLimit = 80;  // Standard urban speed limit in km/h
        $threshold = $speedLimit + 10; // Allow 10km/h buffer before flagging

        if ($log->speed > $threshold) {
            $impact = 5.00;
            
            $event = RiskEvent::create([
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

            // Broadcast real-time alert
            event(new \App\Events\AlertGenerated($event));

            $this->applyScorePenalty($driver, $impact);
            return true;
        }
        return false;
    }

    /**
     * Spatial Intelligence: Detecting unauthorized entry/exit.
     */
    protected function detectGeofenceBreach(TelematicsLog $log, Driver $driver): bool
    {
        $landmarks = Landmark::all();
        $lat = 0; $lng = 0;

        $location = $log->location;
        if (!($location instanceof \Clickbar\Magellan\Data\Geometries\Point)) {
            return false;
        }
        $lat = $location->getLatitude();
        $lng = $location->getLongitude();

        if ($lat === 0 || $lng === 0) return false;

        $point = [$lat, $lng]; // [lat, lng]
        $cleanDrive = true;

        foreach ($landmarks as $landmark) {
            $coords = [];
            $area = $landmark->area;
            
            if (is_array($area)) {
                $coords = $area;
            } elseif (is_string($area)) {
                $coords = json_decode($area, true);
            } elseif ($area instanceof \Clickbar\Magellan\Data\Geometries\Polygon) {
                $lineStrings = $area->getLineStrings();
                if (!empty($lineStrings)) {
                    $ring = $lineStrings[0];
                    $coords = array_map(fn($p) => [$p->getLatitude(), $p->getLongitude()], $ring->getPoints());
                }
            } elseif ($area && isset($area->coordinates)) {
                $coords = $area->coordinates[0];
                $coords = array_map(fn($p) => [$p[1], $p[0]], $coords);
            }

            if (empty($coords)) continue;

            $isInside = $this->isPointInPolygon($point, $coords);

            // Logic: 
            // 1. Restricted -> Alert if INSIDE
            // 2. Optimized Route -> Alert if OUTSIDE (Route Deviation)
            
            $breach = false;
            $type = '';

            // Depot / Home Base Logic: If inside a depot, they are "Safe"
            $isInDepot = Landmark::where('type', 'depot')->get()->contains(fn($l) => $this->isPointInPolygon($point, is_string($l->area) ? json_decode($l->area, true) : $l->area));

            if ($landmark->type === 'restricted' && $isInside) {
                $breach = true;
                $type = 'unauthorized_entry';
            } elseif ($landmark->type === 'optimized_route' && !$isInside && !$isInDepot) {
                // Only alert for route deviation if they are NOT in a safe depot
                $breach = true;
                $type = 'route_deviation';
            }

            if ($breach) {
                \Illuminate\Support\Facades\Log::info("RiskEngine: BREACH DETECTED: {$type}");
                $impact = 10.00;
                
                // Simplified duplicate check for SQLite compatibility
                $exists = RiskEvent::where('driver_id', $driver->id)
                    ->where('type', 'geofence_breach')
                    ->where('occurred_at', '>', now()->subMinutes(2))
                    ->latest()
                    ->first();

                // Only create if it's a different landmark or enough time has passed
                $isDuplicate = $exists && ($exists->details['landmark_id'] ?? null) == $landmark->id;

                if (!$isDuplicate) {
                    $alert = RiskEvent::create([
                        'driver_id' => $driver->id,
                        'vehicle_id' => $log->vehicle_id,
                        'telematics_log_id' => $log->id,
                        'type' => 'geofence_breach',
                        'impact_score' => $impact,
                        'details' => [
                            'landmark_name' => $landmark->name,
                            'landmark_id' => $landmark->id,
                            'breach_type' => $type,
                        ],
                        'occurred_at' => $log->captured_at,
                    ]);

                    // Broadcast real-time alert
                    event(new \App\Events\AlertGenerated($alert));

                    // DISPATCH REAL-TIME SECURITY ALERT EMAIL
                    try {
                        $adminEmail = config('mail.from.address'); // Sending to the support email for review
                        $emailData = [
                            'vehicleName' => $log->vehicle->name ?? 'Unknown Vehicle',
                            'driverName' => $driver->name,
                            'incidentType' => 'Geofence Breach (' . str_replace('_', ' ', $type) . ')',
                            'deviation' => 'Zone: ' . $landmark->name,
                        ];
                        
                        Mail::to($adminEmail)->send(new SecurityAlert($emailData));
                        \Illuminate\Support\Facades\Log::info("RiskEngine: Security Alert Email dispatched to {$adminEmail}");
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error("RiskEngine: Failed to send security alert: " . $e->getMessage());
                    }

                    $this->applyScorePenalty($driver, $impact);
                }
                return true;
            }
        }
        return false;
    }

    /**
     * Ray-casting algorithm for Point-in-Polygon check (SQLite Fallback)
     */
    protected function isPointInPolygon($point, $polygon): bool
    {
        $x = $point[0]; $y = $point[1];
        $inside = false;
        for ($i = 0, $j = count($polygon) - 1; $i < count($polygon); $j = $i++) {
            $xi = $polygon[$i][0]; $yi = $polygon[$i][1];
            $xj = $polygon[$j][0]; $yj = $polygon[$j][1];
            
            $intersect = (($yi > $y) != ($yj > $y))
                && ($x < ($xj - $xi) * ($y - $yi) / ($yj - $yi) + $xi);
            if ($intersect) $inside = !$inside;
        }
        return $inside;
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
