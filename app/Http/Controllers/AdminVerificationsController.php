<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminVerificationsController extends Controller
{
    /**
     * GET /admin/verifications
     * List all unverified users (fleet managers awaiting email verification).
     */
    public function index(): View
    {
        $pendingUsers = User::whereNull('email_verified_at')
            ->where('role', 'fleet_manager')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.verifications', compact('pendingUsers'));
    }

    /**
     * POST /admin/verifications/{user}/force-verify
     * Super Admin manually force-verifies a user's email.
     */
    public function forceVerify(User $user): RedirectResponse
    {
        $user->update(['email_verified_at' => now()]);

        return back()->with('success', "Email force-verified for {$user->email}.");
    }
}
