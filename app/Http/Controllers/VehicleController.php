<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\Driver;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::with(['driver', 'latestTelematics'])->withCount('telematicsLogs')->get();
        $unassignedDrivers = Driver::whereDoesntHave('vehicle')->get();

        return view('vehicles.index', compact('vehicles', 'unassignedDrivers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'license_plate' => 'required|string|max:50|unique:vehicles,license_plate',
            'status'        => 'required|in:active,idle,maintenance,offline',
        ]);

        Vehicle::create([
            'name'          => $request->name,
            'license_plate' => strtoupper($request->license_plate),
            'status'        => $request->status,
        ]);

        return redirect()->route('vehicles.index')->with('success', 'Vehicle added to fleet.');
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $request->validate([
            'name'              => 'required|string|max:255',
            'license_plate'     => 'required|string|max:50|unique:vehicles,license_plate,' . $vehicle->id,
            'status'            => 'required|in:active,idle,maintenance,offline',
            'current_driver_id' => 'nullable|exists:drivers,id',
        ]);

        // Unassign driver from any other vehicle first
        if ($request->current_driver_id) {
            Vehicle::where('current_driver_id', $request->current_driver_id)
                ->where('id', '!=', $vehicle->id)
                ->update(['current_driver_id' => null]);
        }

        $vehicle->update([
            'name'              => $request->name,
            'license_plate'     => strtoupper($request->license_plate),
            'status'            => $request->status,
            'current_driver_id' => $request->current_driver_id ?? null,
        ]);

        return redirect()->route('vehicles.index')->with('success', 'Vehicle updated successfully.');
    }

    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();
        return redirect()->route('vehicles.index')->with('success', 'Vehicle removed from fleet.');
    }

    public function regenerateToken(Vehicle $vehicle)
    {
        $vehicle->update([
            'telemetry_token' => 'FLT-' . strtoupper(bin2hex(random_bytes(4)))
        ]);
        return redirect()->route('vehicles.index')->with('success', 'Telemetry token regenerated for ' . $vehicle->name . '. Update your tracking device.');
    }
}
