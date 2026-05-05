<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OnboardingController extends Controller
{
    public function index()
    {
        // If already completed, redirect to dashboard
        if (Auth::user()->onboarding_completed) {
            return redirect()->route('dashboard');
        }

        return view('onboarding.index');
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        
        $user->update([
            'settings' => $request->all(),
            'onboarding_completed' => true
        ]);

        return response()->json(['success' => true]);
    }
}
