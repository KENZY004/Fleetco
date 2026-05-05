<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\Vehicle;
use App\Repositories\DriverRepository;
use App\Repositories\VehicleRepository;
use App\Http\Requests\StoreDriverRequest;
use App\Http\Requests\UpdateDriverRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DriverController extends Controller
{
    protected $driverRepo;
    protected $vehicleRepo;

    public function __construct(DriverRepository $driverRepo, VehicleRepository $vehicleRepo)
    {
        $this->driverRepo = $driverRepo;
        $this->vehicleRepo = $vehicleRepo;
    }

    public function index(): View
    {
        $drivers = $this->driverRepo->all();
        $vehicles = $this->vehicleRepo->getAllWithStatus();
        $unlinkedUsers = $this->driverRepo->getUnlinkedUsers();

        return view('drivers.index', compact('drivers', 'vehicles', 'unlinkedUsers'));
    }

    public function show(Driver $driver): View
    {
        $driver->load(['vehicle', 'user']);
        
        $trips = \App\Models\Trip::with('vehicle')
            ->where('driver_id', $driver->id)
            ->latest('start_time')
            ->limit(10)
            ->get();

        $alerts = \App\Models\RiskEvent::with('vehicle')
            ->where('driver_id', $driver->id)
            ->latest('occurred_at')
            ->limit(10)
            ->get();

        return view('drivers.show', compact('driver', 'trips', 'alerts'));
    }

    public function store(StoreDriverRequest $request): RedirectResponse
    {
        $this->driverRepo->create($request->validated());

        return redirect()->route('drivers.index')->with('success', 'Driver registered successfully.');
    }

    public function update(UpdateDriverRequest $request, Driver $driver): RedirectResponse
    {
        $this->driverRepo->update($driver, $request->validated());

        if ($request->has('vehicle_id')) {
            $this->vehicleRepo->unassignDriverFromOthers($driver->id, $request->vehicle_id ?? 0);
            
            if ($request->vehicle_id) {
                $vehicle = Vehicle::find($request->vehicle_id);
                $this->vehicleRepo->update($vehicle, ['current_driver_id' => $driver->id]);
            }
        }

        return redirect()->route('drivers.index')->with('success', 'Driver updated successfully.');
    }

    public function destroy(Driver $driver): RedirectResponse
    {
        $this->vehicleRepo->unassignDriverFromOthers($driver->id, 0);
        $this->driverRepo->delete($driver);

        return redirect()->route('drivers.index')->with('success', 'Driver removed from system.');
    }

    public function resetScore(Driver $driver): RedirectResponse
    {
        $this->driverRepo->update($driver, ['risk_score' => 100.00]);
        return redirect()->route('drivers.index')->with('success', 'Risk score reset to 100.');
    }
}
