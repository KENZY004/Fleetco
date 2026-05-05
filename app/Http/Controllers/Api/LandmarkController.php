<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Landmark;
use Illuminate\Http\Request;
use Clickbar\Magellan\Data\Geometries\Point;
use Clickbar\Magellan\Data\Geometries\LineString;
use Clickbar\Magellan\Data\Geometries\Polygon;

class LandmarkController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Magellan geometries can be cast to GeoJSON or standard arrays, 
        // but for simplicity let's just return the GeoJSON string directly.
        $landmarks = Landmark::select('id', 'name', 'type', \DB::raw('ST_AsGeoJSON(area) as area_geojson'))->get();
        
        $landmarks->transform(function ($landmark) {
            $landmark->area_geojson = json_decode($landmark->area_geojson);
            return $landmark;
        });

        return response()->json($landmarks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:depot,client,restricted,optimized_route',
            'coordinates' => 'required|array|min:3',
            'coordinates.*.lat' => 'required|numeric',
            'coordinates.*.lng' => 'required|numeric',
        ]);

        $points = collect($validated['coordinates'])->map(function ($coord) {
            // Magellan uses (longitude, latitude) internally for Point creation (X, Y)
            // Wait, makeGeodetic signature is makeGeodetic(float $latitude, float $longitude) in some versions
            // Clickbar/Magellan uses makeGeodetic(float $latitude, float $longitude, ...)
            return Point::makeGeodetic($coord['lat'], $coord['lng']);
        });

        // Ensure the polygon is closed (first and last point are identical)
        $first = $points->first();
        $last = $points->last();
        if ($first->getX() !== $last->getX() || $first->getY() !== $last->getY()) {
            $points->push($first);
        }

        $lineString = LineString::makeGeodetic($points->toArray());
        $polygon = Polygon::makeGeodetic([$lineString]);

        $landmark = Landmark::create([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'area' => $polygon,
        ]);

        return response()->json($landmark, 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $landmark = Landmark::findOrFail($id);
        $landmark->delete();
        return response()->json(null, 204);
    }
}
