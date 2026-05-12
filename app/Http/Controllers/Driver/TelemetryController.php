<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\DutyLog;

class TelemetryController extends Controller
{
    public function store(Request $request)
    {
        // Security check
        if (!auth()->check() || auth()->user()->role !== 'driver') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $user = auth()->user();

        // Verify driver has an active ON DUTY log
        $activeDuty = DutyLog::where('driver_id', $user->id)
            ->where('status', 'on_duty')
            ->whereNull('ended_at')
            ->exists();

        if (!$activeDuty) {
            return response()->json(['error' => 'Driver must be ON DUTY to submit telemetry'], 403);
        }

        $validated = $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'speed_kmh' => ['required', 'numeric', 'between:0,300'],
            'heading' => ['nullable', 'numeric'],
            'captured_at' => ['required', 'date', 'before_or_equal:now'],
        ]);

        $vehicleId = null;
        if ($user->driver && $user->driver->vehicle) {
            $vehicleId = $user->driver->vehicle->id;
        }

        DB::table('driver_telemetry')->insert([
            'driver_id' => $user->id,
            'vehicle_id' => $vehicleId,
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'speed_kmh' => $validated['speed_kmh'],
            'heading' => $validated['heading'] ?? null,
            'captured_at' => Carbon::parse($validated['captured_at']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }
}
