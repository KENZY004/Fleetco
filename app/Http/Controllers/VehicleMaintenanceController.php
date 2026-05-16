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
        $allRecords = $vehicle->maintenanceRecords()->latest('created_at')->get();
        
        // Finalized Services: Records with cost > 0 OR status is 'resolved'
        $records = $allRecords->filter(fn($r) => $r->cost > 0 || $r->status === 'resolved');
        
        // Pending Driver Reports: Records with cost = 0 AND status is 'reported'
        $pendingIssues = $allRecords->filter(fn($r) => $r->cost == 0 && $r->status === 'reported');
        
        $lastServiceOdo = $records->first()?->odometer_reading ?? 0;
        $kmSinceService = $vehicle->odometer - $lastServiceOdo;
        $serviceDue = $kmSinceService >= 5000;

        return view('maintenance.index', compact('vehicle', 'records', 'pendingIssues', 'kmSinceService', 'serviceDue'));
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

        $validated['status'] = 'resolved';

        $vehicle->maintenanceRecords()->create($validated);

        return redirect()->back()->with('success', 'Maintenance record added successfully.');
    }

    public function resolve(Request $request, MaintenanceRecord $record): RedirectResponse
    {
        $validated = $request->validate([
            'cost' => 'required|numeric',
            'notes' => 'nullable|string',
            'odometer_reading' => 'required|numeric',
        ]);

        $record->update([
            'cost' => $validated['cost'],
            'notes' => $record->notes . " | RESOLVED: " . $validated['notes'],
            'odometer_reading' => $validated['odometer_reading'],
            'status' => 'resolved',
            'service_date' => now(),
        ]);

        return redirect()->back()->with('success', 'Driver report resolved and moved to history.');
    }
}
