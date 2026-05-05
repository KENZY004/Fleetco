<?php

namespace App\Http\Controllers;

use App\Repositories\VehicleRepository;
use App\Repositories\TripRepository;
use App\Repositories\AnomalyRepository;
use App\Models\RiskEvent;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $vehicleRepo;
    protected $tripRepo;
    protected $anomalyRepo;
    protected $analyticsService;

    public function __construct(
        VehicleRepository $vehicleRepo,
        TripRepository $tripRepo,
        AnomalyRepository $anomalyRepo,
        \App\Services\AnalyticsService $analyticsService
    ) {
        $this->vehicleRepo = $vehicleRepo;
        $this->tripRepo = $tripRepo;
        $this->anomalyRepo = $anomalyRepo;
        $this->analyticsService = $analyticsService;
    }

    /**
     * Display the Bento Dashboard.
     */
    public function index()
    {
        $vehicles = $this->vehicleRepo->getAllWithStatus();
        $recentAlerts = $this->anomalyRepo->getRecent(5);
        $trips = $this->tripRepo->getRecent(10);
        $stats = $this->analyticsService->getDashboardStats();
        $geofences = \App\Models\Landmark::all();

        return view('dashboard', compact('vehicles', 'recentAlerts', 'trips', 'stats', 'geofences'));
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

    /**
     * API for live security alerts.
     */
    public function getSecurityAlerts()
    {
        $alerts = RiskEvent::with(['vehicle', 'driver', 'telematicsLog'])
            ->whereNull('resolved_at')
            ->latest('occurred_at')
            ->limit(10)
            ->get();

        return response()->json($alerts);
    }

    public function resolveAlert(Request $request, RiskEvent $alert)
    {
        $alert->update([
            'resolved_at' => now(),
            'resolution_note' => $request->note ?? 'No note provided'
        ]);
        return response()->json(['success' => true]);
    }
}
