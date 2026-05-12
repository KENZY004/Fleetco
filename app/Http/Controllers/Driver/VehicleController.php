<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        $driver = $request->user()->driver;
        $vehicle = $driver ? $driver->vehicle : null;
        
        return view('driver.vehicle.index', compact('vehicle'));
    }
}
