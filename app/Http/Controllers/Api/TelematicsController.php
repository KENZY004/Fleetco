<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\TelematicsLog;
use App\Services\RiskEngineService;
use Illuminate\Http\Request;
use Clickbar\Magellan\Data\Geometries\Point;
use Carbon\Carbon;

class TelematicsController extends Controller
{
    protected $riskEngine;

    public function __construct(RiskEngineService $riskEngine)
    {
        $this->riskEngine = $riskEngine;
    }

    /**
     * Store a newly created telematics ping from a mobile device.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'license_plate' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'speed' => 'nullable|numeric',
            'heading' => 'nullable|numeric',
        ]);

        // 1. Authenticate (Sanctum)
        $vehicle = $request->user();

        if (!$vehicle || !($vehicle instanceof Vehicle)) {
            \Log::error('Telematics failed: Invalid context');
            return response()->json(['error' => 'INVALID_DEVICE_CONTEXT'], 403);
        }

        // 2. Validate Vehicle Identity matches token (Case-insensitive)
        if (strtolower($vehicle->license_plate) !== strtolower($validated['license_plate'])) {
            \Log::error('Telematics identity mismatch', [
                'expected' => $vehicle->license_plate,
                'received' => $validated['license_plate']
            ]);
            return response()->json(['error' => 'IDENTITY_MISMATCH'], 403);
        }

        // 3. Create Log with Magellan Point
        $log = TelematicsLog::create([
            'vehicle_id' => $vehicle->id,
            'driver_id' => $vehicle->current_driver_id ?? 1, // Defaulting to 1 for now
            'location' => Point::makeGeodetic($validated['latitude'], $validated['longitude']),
            'speed' => $validated['speed'] ?? 0,
            'heading' => $validated['heading'] ?? 0,
            'captured_at' => Carbon::now(),
        ]);

        // Broadcast the real-time update
        event(new \App\Events\TelematicsReceived($log));

        // 4. Update Vehicle State & Odometer
        $lastLog = TelematicsLog::where('vehicle_id', $vehicle->id)
                                ->orderBy('captured_at', 'desc')
                                ->skip(1) // Skip the one we just inserted
                                ->first();

        $distanceKm = 0;
        if ($lastLog && $lastLog->location) {
            $lat1 = $lastLog->location->getLatitude();
            $lon1 = $lastLog->location->getLongitude();
            $lat2 = (float)$validated['latitude'];
            $lon2 = (float)$validated['longitude'];

            if ($lat1 != $lat2 || $lon1 != $lon2) {
                $theta = $lon1 - $lon2;
                $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
                $dist = acos(min(max($dist, -1.0), 1.0)); // Clamp value for acos
                $dist = rad2deg($dist);
                $miles = $dist * 60 * 1.1515;
                $distanceKm = $miles * 1.609344;
            }
        }

        $distanceKm = is_nan($distanceKm) ? 0 : $distanceKm;
        $newOdometer = (float)$vehicle->current_odometer + $distanceKm;
        
        $vehicle->update([
            'status' => ($validated['speed'] > 0 ? 'active' : 'idle'),
            'current_odometer' => $newOdometer
        ]);

        // Check if service is required
        if ($vehicle->next_service_at && $newOdometer >= $vehicle->next_service_at) {
            // Check if an open maintenance issue already exists to prevent spam
            $existingIssue = \App\Models\Issue::where('vehicle_id', $vehicle->id)
                ->where('title', 'like', 'Maintenance Required%')
                ->where('status', 'open')
                ->first();

            if (!$existingIssue) {
                $issue = \App\Models\Issue::create([
                    'vehicle_id' => $vehicle->id,
                    'title' => 'Maintenance Required: Odometer Threshold Reached',
                    'description' => "Vehicle has reached " . round($newOdometer) . " km. Scheduled service was at " . $vehicle->next_service_at . " km.",
                    'status' => 'open',
                    'priority' => 'high'
                ]);

                // Reset the service threshold to prevent immediate triggering again
                $vehicle->update(['next_service_at' => $newOdometer + 5000]);

                event(new \App\Events\IssueCreated($issue));
            }
        }

        // 5. Run Intelligence Analysis (Risk Engine)
        $this->riskEngine->analyze($log);

        return response()->json([
            'status' => 'TELEMETRY_INGESTED',
            'log_id' => $log->id,
            'risk_processed' => true
        ]);
    }
}
