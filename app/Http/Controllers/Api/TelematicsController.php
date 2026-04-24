<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\TelematicsLog;
use App\Services\TelemetryProcessor;
use Illuminate\Http\Request;
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
        $request->validate([
            'license_plate' => 'required|string',
            'vehicle_name' => 'nullable|string', // Dynamic name
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'speed' => 'nullable|numeric',
            'heading' => 'nullable|numeric',
            'secret' => 'required|string'
        ]);

        if ($request->secret !== 'fleetco_secret_2024') {
            return response()->json(['error' => 'Invalid Uplink Secret'], 401);
        }

        // 1. Find or Create the vehicle dynamically
        $vehicle = Vehicle::firstOrCreate(
            ['license_plate' => strtoupper($request->license_plate)],
            [
                'name' => $request->vehicle_name ?? ('Unit ' . $request->license_plate), 
                'status' => 'active'
            ]
        );

        // Update name if it's provided and different
        if ($request->vehicle_name && $vehicle->name !== $request->vehicle_name) {
            $vehicle->update(['name' => $request->vehicle_name]);
        }

        // 2. Process Telemetry using Service
        try {
            $log = $this->processor->process($vehicle, [
                'lat' => $request->latitude,
                'lng' => $request->longitude,
                'speed' => $request->speed ?? 0,
                'heading' => $request->heading ?? 0,
                'captured_at' => now(),
            ]);

            return response()->json([
                'status' => 'success',
                'vehicle_id' => $vehicle->id,
                'log_id' => $log->id
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'PROCESSING_FAILED',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
