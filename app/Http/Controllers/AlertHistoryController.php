<?php

namespace App\Http\Controllers;

use App\Models\RiskEvent;
use Illuminate\Http\Request;

class AlertHistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = RiskEvent::with(['driver', 'vehicle'])
            ->latest('occurred_at');

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by resolution status
        if ($request->status === 'resolved') {
            $query->whereNotNull('resolved_at');
        } elseif ($request->status === 'unresolved') {
            $query->whereNull('resolved_at');
        }

        $alerts         = $query->paginate(15);
        $speedingCount  = RiskEvent::where('type', 'speeding')->count();
        $geofenceCount  = RiskEvent::whereIn('type', ['geofence_breach', 'geofence_entry'])->count();
        $unresolvedCount = RiskEvent::whereNull('resolved_at')->count();

        return view('alerts.index', compact('alerts', 'speedingCount', 'geofenceCount', 'unresolvedCount'));
    }

    public function resolve(Request $request, RiskEvent $alert)
    {
        $alert->update([
            'resolved_at'     => now(),
            'resolution_note' => $request->note ?? 'Resolved by operator.',
        ]);

        return back()->with('success', 'Incident marked as resolved.');
    }

    public function destroy(RiskEvent $alert)
    {
        $alert->delete();
        return back()->with('success', 'Alert removed from history.');
    }

    public function clearAll()
    {
        RiskEvent::truncate();
        return back()->with('success', 'Alert history cleared.');
    }
}
