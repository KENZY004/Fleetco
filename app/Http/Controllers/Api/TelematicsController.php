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
            'secret' => 'required|string', // Simple pre-shared secret for this phase
        ]);

        // 1. Authenticate (Simple Phase)
        if ($validated['secret'] !== config('app.telematics_secret', 'fleetco_secret_2024')) {
            return response()->json(['error' => 'UNAUTHORIZED_ACCESS'], 401);
        }

        // 2. Resolve Vehicle
        $vehicle = Vehicle::where('license_plate', $validated['license_plate'])->first();
        if (!$vehicle) {
            return response()->json(['error' => 'VEHICLE_NOT_FOUND'], 404);
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

        // 4. Update Vehicle State
        $vehicle->update(['status' => ($validated['speed'] > 0 ? 'active' : 'idle')]);

        // 5. Run Intelligence Analysis (Risk Engine)
        $this->riskEngine->analyze($log);

        return response()->json([
            'status' => 'TELEMETRY_INGESTED',
            'log_id' => $log->id,
            'risk_processed' => true
        ]);
    }
}
