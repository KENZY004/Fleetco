<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use Illuminate\Http\Request;

class IssueController extends Controller
{
    public function index(Request $request)
    {
        $query = Issue::with('vehicle');

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('vehicle', function($vq) use ($search) {
                      $vq->where('name', 'like', "%{$search}%")
                         ->orWhere('license_plate', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->has('status') && $request->get('status') !== 'all') {
            $query->where('status', $request->get('status'));
        }

        $issues = $query->latest()->paginate(15)->withQueryString();
        return view('issues.index', compact('issues'));
    }

    /**
     * Resolve a maintenance issue.
     */
    public function resolve(Issue $issue)
    {
        $issue->update([
            'status' => 'resolved',
            'resolved_at' => now()
        ]);

        // If this was a maintenance threshold issue, increment the next service target
        if (str_contains($issue->title, 'Maintenance Required')) {
            $vehicle = $issue->vehicle;
            if ($vehicle) {
                // Set next service 5000km from current odometer
                $vehicle->update([
                    'next_service_at' => round($vehicle->current_odometer + 5000)
                ]);
            }
        }

        return redirect()->back()->with('success', 'Issue Resolved & Logged.');
    }
}
