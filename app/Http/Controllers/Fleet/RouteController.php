<?php

namespace App\Http\Controllers\Fleet;

use App\Http\Controllers\Controller;
use App\Models\FleetRoute;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RouteController extends Controller
{
    public function index()
    {
        $fleetId = Auth::user()->fleet_id;
        $routes = FleetRoute::where('fleet_id', $fleetId)
            ->with(['driver', 'vehicle'])
            ->latest()
            ->paginate(10);

        return view('fleet.routes.index', compact('routes'));
    }

    public function create()
    {
        $fleetId = Auth::user()->fleet_id;
        $drivers = User::where('fleet_id', $fleetId)->where('role', 'driver')->get();
        $vehicles = Vehicle::where('fleet_id', $fleetId)->get();

        return view('fleet.routes.create', compact('drivers', 'vehicles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'driver_id' => 'nullable|exists:users,id',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'scheduled_for' => 'nullable|date',
            'waypoints' => 'required|json',
        ]);

        $route = FleetRoute::create([
            'fleet_id' => Auth::user()->fleet_id,
            'name' => $validated['name'],
            'driver_id' => $validated['driver_id'],
            'vehicle_id' => $validated['vehicle_id'],
            'scheduled_for' => $validated['scheduled_for'],
            'waypoints' => json_decode($validated['waypoints'], true),
            'created_by' => Auth::id(),
            'status' => 'active', // default to active if assigned
        ]);

        return redirect()->route('fleet.routes.index')->with('success', 'Route created and assigned successfully.');
    }

    public function assign(Request $request, $id)
    {
        $route = FleetRoute::findOrFail($id);
        $validated = $request->validate([
            'driver_id' => 'required|exists:users,id',
            'vehicle_id' => 'required|exists:vehicles,id',
        ]);

        $route->update([
            'driver_id' => $validated['driver_id'],
            'vehicle_id' => $validated['vehicle_id'],
            'status' => 'active',
        ]);

        return back()->with('success', 'Route assigned successfully.');
    }
}
