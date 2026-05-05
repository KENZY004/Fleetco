<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\Vehicle;
use App\Models\User;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    public function index()
    {
        $drivers = Driver::with(['user', 'riskEvents'])->withCount('telematicsLogs')->get();
        $vehicles = Vehicle::with('driver')->get();
        $unlinkedUsers = User::whereDoesntHave('driver')->where('role', '!=', 'admin')->get();

        return view('drivers.index', compact('drivers', 'vehicles', 'unlinkedUsers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'license_number' => 'nullable|string|max:50',
            'user_id' => 'nullable|exists:users,id|unique:drivers,user_id',
        ]);

        Driver::create([
            'name' => $request->name,
            'phone_number' => $request->phone_number,
            'license_number' => $request->license_number,
            'user_id' => $request->user_id ?? null,
            'risk_score' => 100.00,
        ]);

        return redirect()->route('drivers.index')->with('success', 'Driver registered successfully.');
    }

    public function update(Request $request, Driver $driver)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'license_number' => 'nullable|string|max:50',
            'user_id' => 'nullable|exists:users,id|unique:drivers,user_id,'. $driver->id,
            'vehicle_id' => 'nullable|exists:vehicles,id',
        ]);

        $driver->update([
            'name' => $request->name,
            'phone_number' => $request->phone_number,
            'license_number' => $request->license_number,
            'user_id' => $request->user_id,
        ]);

        // Handle vehicle assignment
        if ($request->has('vehicle_id')) {
            // Unassign from previous vehicle
            Vehicle::where('current_driver_id', $driver->id)->update(['current_driver_id' => null]);

            if ($request->vehicle_id) {
                Vehicle::find($request->vehicle_id)->update([
                    'current_driver_id' => $driver->id
                ]);
            }
        }

        return redirect()->route('drivers.index')->with('success', 'Driver updated successfully.');
    }

    public function destroy(Driver $driver)
    {
        // Unassign from any vehicle first
        Vehicle::where('current_driver_id', $driver->id)->update(['current_driver_id' => null]);
        $driver->delete();

        return redirect()->route('drivers.index')->with('success', 'Driver removed from system.');
    }

    public function resetScore(Driver $driver)
    {
        $driver->update(['risk_score' => 100.00]);
        return redirect()->route('drivers.index')->with('success', 'Risk score reset to 100.');
    }
}
