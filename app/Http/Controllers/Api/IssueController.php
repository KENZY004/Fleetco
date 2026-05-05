<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Issue;
use Illuminate\Http\Request;

class IssueController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Issue::with('vehicle')->orderBy('created_at', 'desc')->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'in:low,medium,high,critical'
        ]);

        $issue = Issue::create($validated);
        
        event(new \App\Events\IssueCreated($issue));

        return response()->json($issue, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Issue::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $issue = Issue::findOrFail($id);

        $validated = $request->validate([
            'status' => 'in:open,in_progress,resolved',
        ]);

        $issue->update($validated);

        return response()->json($issue);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $issue = Issue::findOrFail($id);
        $issue->delete();
        return response()->json(null, 204);
    }
}
