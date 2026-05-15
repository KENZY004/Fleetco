<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceRecord;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    public function store(Request $request)
    {
        // Security check
        if (!auth()->check() || auth()->user()->role !== 'driver') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'issue_type' => 'required|string',
            'description' => 'required|string',
        ]);

        $user = auth()->user();
        $vehicle = $user->driver ? $user->driver->vehicle : null;

        if (!$vehicle) {
            return response()->json(['error' => 'No assigned vehicle'], 422);
        }

        $record = MaintenanceRecord::create([
            'vehicle_id' => $vehicle->id,
            'driver_id'  => $user->driver->id,
            'service_type' => $request->issue_type,
            'odometer_reading' => $vehicle->odometer ?? 0,
            'cost' => 0,
            'notes' => $request->description,
            'service_date' => now(),
        ]);

        // Dispatch high-priority alert to Fleet Command
        try {
            \Illuminate\Support\Facades\Mail::to('fleetcosupport@gmail.com')
                ->send(new \App\Mail\MaintenanceReport($record->load(['vehicle', 'driver'])));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Maintenance Alert Dispatch Failure: " . $e->getMessage());
        }

        return response()->json(['success' => true]);
    }
}
