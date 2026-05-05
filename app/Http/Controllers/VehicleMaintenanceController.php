<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\MaintenanceRecord;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class VehicleMaintenanceController extends Controller
{
    public function index(Vehicle $vehicle): View
    {
        $records = $vehicle->maintenanceRecords()->latest('service_date')->get();
        
        // Simple logic for service due
        // For demonstration, let's say every 5000km
        $lastServiceOdo = $records->first()?->odometer_reading ?? 0;
        $kmSinceService = $vehicle->odometer - $lastServiceOdo;
        $serviceDue = $kmSinceService >= 5000;

        return view('maintenance.index', compact('vehicle', 'records', 'kmSinceService', 'serviceDue'));
    }

    public function store(Request $request, Vehicle $vehicle): RedirectResponse
    {
        $validated = $request->validate([
            'service_type' => 'required|string|max:255',
            'odometer_reading' => 'required|numeric',
            'cost' => 'required|numeric',
            'service_date' => 'required|date',
            'notes' => 'nullable|string',
            'next_service_at_km' => 'nullable|numeric',
        ]);

        $vehicle->maintenanceRecords()->create($validated);

        return redirect()->back()->with('success', 'Maintenance record added successfully.');
    }
}
