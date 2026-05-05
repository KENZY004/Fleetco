<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Geofence;
use Illuminate\Http\Request;
use Clickbar\Magellan\Data\Geometries\Polygon;
use Clickbar\Magellan\Data\Geometries\LineString;
use Clickbar\Magellan\Data\Geometries\Point;

class GeofenceController extends Controller
{
    public function index()
    {
        return response()->json(Geofence::where('status', 'active')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'coordinates' => 'required|array',
        ]);

        $points = [];
        foreach ($validated['coordinates'] as $coord) {
            $points[] = Point::makeGeodetic($coord['lat'], $coord['lng']);
        }
        
        // Ensure the polygon is closed (last point = first point)
        if ($points[0] != end($points)) {
            $points[] = $points[0];
        }

        $geofence = Geofence::create([
            'name' => $validated['name'],
            'area' => Polygon::makeGeodetic([LineString::makeGeodetic($points)]),
        ]);

        return response()->json($geofence);
    }
}
