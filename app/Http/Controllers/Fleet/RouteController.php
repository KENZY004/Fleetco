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
        $geofences = \App\Models\Landmark::all();

        return view('fleet.routes.create', compact('drivers', 'vehicles', 'geofences'));
    }

    public function show($id)
    {
        $route = FleetRoute::with(['driver', 'vehicle'])->findOrFail($id);
        return view('fleet.routes.show', compact('route'));
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

        // Conflict Check: Is this vehicle already busy?
        if ($validated['vehicle_id']) {
            $busy = FleetRoute::where('vehicle_id', $validated['vehicle_id'])
                ->where('status', 'active')
                ->exists();
            
            if ($busy) {
                return back()->withErrors(['vehicle_id' => 'Conflict: This vehicle is already assigned to an active mission.'])->withInput();
            }
        }

        $route = FleetRoute::create([
            'fleet_id' => Auth::user()->fleet_id,
            'name' => $validated['name'],
            'driver_id' => $validated['driver_id'],
            'vehicle_id' => $validated['vehicle_id'],
            'scheduled_for' => $validated['scheduled_for'],
            'waypoints' => json_decode($validated['waypoints'], true),
            'created_by' => Auth::id(),
            'status' => ($validated['driver_id'] && $validated['vehicle_id']) ? 'active' : 'draft',
        ]);

        $message = $route->status === 'active' ? 'Route created and activated.' : 'Route saved as template (Draft).';
        return redirect()->route('fleet.routes.index')->with('success', $message);
    }

    public function assign(Request $request, $id)
    {
        $route = FleetRoute::findOrFail($id);
        $validated = $request->validate([
            'driver_id' => 'required|exists:users,id',
            'vehicle_id' => 'required|exists:vehicles,id',
        ]);

        // Conflict Check
        $busy = FleetRoute::where('vehicle_id', $validated['vehicle_id'])
            ->where('status', 'active')
            ->where('id', '!=', $id)
            ->exists();
        
        if ($busy) {
            return back()->withErrors(['vehicle_id' => 'This vehicle is currently assigned to another active mission.']);
        }

        $route->update([
            'driver_id' => $validated['driver_id'],
            'vehicle_id' => $validated['vehicle_id'],
            'status' => 'active',
        ]);

        return back()->with('success', 'Route activated and assigned successfully.');
    }

    public function destroy($id)
    {
        $route = FleetRoute::findOrFail($id);
        
        // Security: Ensure it belongs to the same fleet
        if ($route->fleet_id !== Auth::user()->fleet_id) {
            abort(403);
        }

        $route->delete();

        return redirect()->route('fleet.routes.index')->with('success', 'Route deleted successfully.');
    }
}
