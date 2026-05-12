<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\RiskEvent;
use Illuminate\Http\Request;

class RiskController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        $query = RiskEvent::where('driver_id', $user->id);
        
        // Filtering
        if ($request->has('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }
        
        if ($request->has('status') && $request->status !== 'all') {
            if ($request->status === 'resolved') {
                $query->whereNotNull('resolved_at');
            } else {
                $query->whereNull('resolved_at');
            }
        }
        
        $incidents = $query->orderBy('occurred_at', 'desc')->paginate(15);
        
        // Stats
        $totalIncidents = RiskEvent::where('driver_id', $user->id)->count();
        $speedingEvents = RiskEvent::where('driver_id', $user->id)->where('type', 'speeding')->count();
        $geofenceBreaches = RiskEvent::where('driver_id', $user->id)->where('type', 'geofence_breach')->count();
        $unresolvedCount = RiskEvent::where('driver_id', $user->id)->whereNull('resolved_at')->count();

        return view('driver.risk.index', compact('incidents', 'totalIncidents', 'speedingEvents', 'geofenceBreaches', 'unresolvedCount'));
    }
}
