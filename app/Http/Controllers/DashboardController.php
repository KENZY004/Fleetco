<?php

namespace App\Http\Controllers;

use App\Repositories\VehicleRepository;
use App\Repositories\TripRepository;
use App\Repositories\AnomalyRepository;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $vehicleRepo;
    protected $tripRepo;
    protected $anomalyRepo;

    public function __construct(
        VehicleRepository $vehicleRepo,
        TripRepository $tripRepo,
        AnomalyRepository $anomalyRepo
    ) {
        $this->vehicleRepo = $vehicleRepo;
        $this->tripRepo = $tripRepo;
        $this->anomalyRepo = $anomalyRepo;
    }

    /**
     * Display the Bento Dashboard.
     */
    public function index()
    {
        $vehicles = $this->vehicleRepo->getAllWithStatus();
        $recentAlerts = $this->anomalyRepo->getRecent(5);
        $trips = $this->tripRepo->getRecent(10);

        return view('dashboard', compact('vehicles', 'recentAlerts', 'trips'));
    }

    /**
     * API for high-frequency polling.
     */
    public function getVehicleStatus()
    {
        return response()->json(
            $this->vehicleRepo->getAllWithStatus()
        );
    }
}
