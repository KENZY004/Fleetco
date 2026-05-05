<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        $query = Vehicle::withCount('issues');

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('license_plate', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->get('status') !== 'all') {
            $query->where('status', $request->get('status'));
        }

        $vehicles = $query->latest()->paginate(10)->withQueryString();
        return view('vehicles.index', compact('vehicles'));
    }

    /**
     * Show the form for registering a new vehicle.
     */
    public function create()
    {
        return view('vehicles.create');
    }

    /**
     * Store a newly created vehicle in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'license_plate' => 'required|string|max:20|unique:vehicles',
            'model' => 'nullable|string|max:255',
            'type' => 'required|string|in:truck,van,car,bike',
        ]);

        Vehicle::create($validated);

        return redirect()->route('dashboard')
            ->with('success', 'Neural Unit Synchronized Successfully.');
    }

    /**
     * Show the mobile tracker interface for a vehicle.
     */
    public function track(Vehicle $vehicle)
    {
        // Create a temporary token for this tracking session
        $token = $vehicle->createToken('neural-link-session')->plainTextToken;

        return view('tracker', compact('vehicle', 'token'));
    }

    /**
     * Show the mobile tracker interface using a secret hash (Public).
     */
    public function trackPublic($hash)
    {
        $vehicle = Vehicle::where('tracking_hash', $hash)->firstOrFail();
        
        // Create a temporary token for this tracking session
        $token = $vehicle->createToken('neural-link-session')->plainTextToken;

        return view('tracker', compact('vehicle', 'token'));
    }
}
