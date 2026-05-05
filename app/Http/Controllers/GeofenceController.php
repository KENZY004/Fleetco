<?php

namespace App\Http\Controllers;

use App\Models\Landmark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GeofenceController extends Controller
{
    public function index()
    {
        $geofences = Landmark::all()->map(function ($landmark) {
            return [
                'id'       => $landmark->id,
                'name'     => $landmark->name,
                'type'     => $landmark->type,
                'area'     => $this->decodeArea($landmark->area),
                'metadata' => $landmark->metadata,
            ];
        });

        return view('geofences.index', compact('geofences'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'type'        => 'required|in:depot,client,restricted,optimized_route',
            'coordinates' => 'required|string', // JSON array of [lat, lng] pairs
        ]);

        $coords = json_decode($request->coordinates, true);

        if (!$coords || count($coords) < 3) {
            return back()->withErrors(['coordinates' => 'A geofence needs at least 3 points.']);
        }

        // Build a proper WKT POLYGON for PostGIS
        $points = collect($coords)->map(fn($p) => "{$p[1]} {$p[0]}")->join(', ');
        $first  = "{$coords[0][1]} {$coords[0][0]}";
        $area   = DB::raw("ST_GeogFromText('POLYGON(({$points}, {$first}))')");

        Landmark::create([
            'name'     => $request->name,
            'type'     => $request->type,
            'area'     => $area,
            'metadata' => ['created_by' => auth()->user()->name],
        ]);

        return redirect()->route('geofences.index')->with('success', 'Geofence "' . $request->name . '" saved successfully.');
    }

    public function destroy(Landmark $geofence)
    {
        $geofence->delete();
        return redirect()->route('geofences.index')->with('success', 'Geofence removed.');
    }

    /**
     * Decode the stored area back to an array of [lat, lng] for the frontend.
     */
    private function decodeArea($area): array
    {
        if (is_string($area)) {
            // SQLite: stored as JSON
            $decoded = json_decode($area, true);
            return $decoded ?? [];
        }

        // PostGIS geometry object — extract coordinates
        return [];
    }
}
