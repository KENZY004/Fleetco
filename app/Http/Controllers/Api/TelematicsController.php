<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\TelematicsLog;
use App\Services\TelemetryProcessor;
use Illuminate\Http\Request;
use Clickbar\Magellan\Data\Geometries\Point;
use Carbon\Carbon;

class TelematicsController extends Controller
{
    protected $processor;

    public function __construct(TelemetryProcessor $processor)
    {
        $this->processor = $processor;
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
            'captured_at' => 'nullable|date',
            'secret' => 'required|string',
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

        // 3. Process via Service
        try {
            $log = $this->processor->process($vehicle, [
                'lat' => $validated['latitude'],
                'lng' => $validated['longitude'],
                'speed' => $validated['speed'] ?? 0,
                'heading' => $validated['heading'] ?? 0,
                'captured_at' => isset($validated['captured_at']) ? Carbon::parse($validated['captured_at']) : Carbon::now(),
            ]);

            return response()->json([
                'status' => 'TELEMETRY_INGESTED',
                'log_id' => $log->id,
                'vehicle_status' => $vehicle->status,
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'PROCESSING_FAILED',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
