<?php

namespace App\Http\Controllers;

use App\Models\RiskEvent;
use Illuminate\Http\Request;

class AlertHistoryController extends Controller
{
    public function index()
    {
        $alerts = RiskEvent::with(['driver', 'vehicle'])
            ->latest('occurred_at')
            ->paginate(15);

        return view('alerts.index', compact('alerts'));
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
