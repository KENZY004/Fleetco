<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Repositories\VehicleRepository;
use App\Repositories\DriverRepository;
use App\Http\Requests\StoreVehicleRequest;
use App\Http\Requests\UpdateVehicleRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class VehicleController extends Controller
{
    protected $vehicleRepo;
    protected $driverRepo;

    public function __construct(VehicleRepository $vehicleRepo, DriverRepository $driverRepo)
    {
        $this->vehicleRepo = $vehicleRepo;
        $this->driverRepo = $driverRepo;
    }

    public function index(): View
    {
        $vehicles = $this->vehicleRepo->getAllWithStatus();
        $unassignedDrivers = $this->driverRepo->getUnassigned();

        return view('vehicles.index', compact('vehicles', 'unassignedDrivers'));
    }

    public function store(StoreVehicleRequest $request): RedirectResponse
    {
        $this->vehicleRepo->create($request->validated());

        return redirect()->route('vehicles.index')->with('success', 'Vehicle added to fleet.');
    }

    public function update(UpdateVehicleRequest $request, Vehicle $vehicle): RedirectResponse
    {
        if ($request->current_driver_id) {
            $this->vehicleRepo->unassignDriverFromOthers($request->current_driver_id, $vehicle->id);
        }

        $this->vehicleRepo->update($vehicle, $request->validated());

        return redirect()->route('vehicles.index')->with('success', 'Vehicle updated successfully.');
    }

    public function destroy(Vehicle $vehicle): RedirectResponse
    {
        $this->vehicleRepo->delete($vehicle);
        return redirect()->route('vehicles.index')->with('success', 'Vehicle removed from fleet.');
    }

    public function regenerateToken(Vehicle $vehicle): RedirectResponse
    {
        $this->vehicleRepo->update($vehicle, [
            'telemetry_token' => 'FLT-' . strtoupper(bin2hex(random_bytes(4)))
        ]);
        
        return redirect()->route('vehicles.index')->with('success', 'Telemetry token regenerated for ' . $vehicle->name . '.');
    }
}
