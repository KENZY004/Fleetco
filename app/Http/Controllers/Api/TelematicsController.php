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
            'token' => 'required|string',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'speed' => 'nullable|numeric',
            'heading' => 'nullable|numeric',
        ]);

        // 1. Find the vehicle by its telemetry token
        $vehicle = Vehicle::where('telemetry_token', $request->token)->first();

        if (!$vehicle) {
            return response()->json(['error' => 'UNAUTHORIZED_UPLINK', 'message' => 'Invalid Telemetry Token'], 401);
        }

        // 2. Process Telemetry using Service
        try {
            $log = $this->processor->process($vehicle, [
                'lat' => $request->lat,
                'lng' => $request->lng,
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
            \Log::error('Telematics Processing Failed: ' . $e->getMessage(), [
                'exception' => $e,
                'payload' => $request->all()
            ]);
            
            return response()->json([
                'error' => 'PROCESSING_FAILED',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function stop(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        $vehicle = Vehicle::where('telemetry_token', $request->token)->first();

        if (!$vehicle) {
            return response()->json(['error' => 'UNAUTHORIZED_UPLINK'], 401);
        }

        // Set status to offline and close active trips
        $vehicle->update(['status' => 'offline']);
        $this->processor->stopSession($vehicle);

        // Broadcast a final "offline" status so the dashboard updates instantly
        // We create a dummy log just for the broadcast event if needed, 
        // but the easiest is just to broadcast a generic event.
        event(new \App\Events\TelematicsReceived(
            $vehicle->telematicsLogs()->latest('captured_at')->first()
        ));

        return response()->json(['status' => 'disconnected']);
    }
}
