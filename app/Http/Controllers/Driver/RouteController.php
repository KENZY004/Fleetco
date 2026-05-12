<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\FleetRoute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RouteController extends Controller
{
    public function markWaypointReached(Request $request, $routeId, $order)
    {
        $route = FleetRoute::where('id', $routeId)
            ->where('driver_id', Auth::id())
            ->where('status', 'active')
            ->firstOrFail();

        $waypoints = $route->waypoints;
        $found = false;
        $allReached = true;

        foreach ($waypoints as &$waypoint) {
            if ($waypoint['order'] == $order) {
                if (isset($waypoint['reached_at']) && $waypoint['reached_at'] !== null) {
                    return response()->json(['error' => 'Waypoint already reached.'], 422);
                }
                $waypoint['reached_at'] = now()->toDateTimeString();
                $found = true;
            }
            if (!isset($waypoint['reached_at']) || $waypoint['reached_at'] === null) {
                $allReached = false;
            }
        }

        if (!$found) {
            return response()->json(['error' => 'Waypoint not found.'], 404);
        }

        $route->waypoints = $waypoints;
        
        if ($allReached) {
            $route->status = 'completed';
        }

        $route->save();

        return response()->json([
            'success' => true,
            'next_waypoint' => $this->getNextWaypoint($waypoints),
            'status' => $route->status
        ]);
    }

    private function getNextWaypoint($waypoints)
    {
        foreach ($waypoints as $waypoint) {
            if (!isset($waypoint['reached_at']) || $waypoint['reached_at'] === null) {
                return $waypoint;
            }
        }
        return null;
    }
}
