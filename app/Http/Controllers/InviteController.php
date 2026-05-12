<?php

namespace App\Http\Controllers;

use App\Mail\DriverInvitationMail;
use App\Models\Driver;
use App\Models\DriverInvitation;
use App\Models\Fleet;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class InviteController extends Controller
{
    /**
     * POST /fleet/invite/send
     * Fleet Manager sends a driver invitation.
     */
    public function send(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
        ]);

        $manager = $request->user();

        // Generate a secure 64-char hex token
        $token = bin2hex(random_bytes(32)); // 32 bytes = 64 hex chars

        // Create invitation (or update if pending for same email+fleet)
        $invitation = DriverInvitation::updateOrCreate(
            [
                'fleet_id'   => $manager->fleet_id,
                'email'      => $request->email,
            ],
            [
                'invited_by' => $manager->id,
                'token'      => $token,
                'expires_at' => now()->addHours(48),
                'accepted_at'=> null,
            ]
        );

        Mail::to($request->email)->send(new DriverInvitationMail($invitation, $manager));

        return back()->with('success', "Invitation sent to {$request->email}. Link expires in 48 hours.");
    }

    /**
     * GET /register/invite/{token}
     * Show invite registration form with email pre-filled and locked.
     */
    public function showInviteRegistration(string $token): View|RedirectResponse
    {
        $invitation = DriverInvitation::where('token', $token)->first();

        if (!$invitation || $invitation->isExpired() || $invitation->isUsed()) {
            return redirect()->route('register')
                ->withErrors(['token' => 'This invitation link is invalid or has expired.']);
        }

        return view('auth.register-invite', compact('invitation'));
    }

    /**
     * POST /register/invite
     * Register driver via secure token — pre-vouched, no email verification needed.
     * Rate-limited to 5 attempts per minute.
     */
    public function storeInviteRegistration(Request $request): RedirectResponse
    {
        // Rate limit: 5 per minute per IP
        $key = 'invite-register:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors(['email' => "Too many attempts. Please wait {$seconds} seconds."]);
        }
        RateLimiter::hit($key, 60);

        $request->validate([
            'token'      => ['required', 'string'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name'  => ['required', 'string', 'max:255'],
            'email'      => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'   => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $invitation = DriverInvitation::where('token', $request->token)
            ->where('email', $request->email)
            ->first();

        if (!$invitation || $invitation->isExpired() || $invitation->isUsed()) {
            return back()->withErrors(['email' => 'This invitation is invalid, expired, or has already been used.']);
        }

        $user = User::create([
            'first_name'        => $request->first_name,
            'last_name'         => $request->last_name,
            'name'              => $request->first_name . ' ' . $request->last_name,
            'email'             => $request->email,
            'password'          => Hash::make($request->password),
            'role'              => 'driver',
            'fleet_id'          => $invitation->fleet_id,
            // Pre-vouched — mark email as verified since the manager invited them
            'email_verified_at' => now(),
        ]);

        // Auto-create the Driver profile so they appear in the fleet's driver list
        Driver::create([
            'user_id'    => $user->id,
            'fleet_id'   => $invitation->fleet_id,
            'name'       => $user->name,
            'risk_score' => 100, // Start with a perfect safety score
        ]);

        // Mark invitation as accepted
        $invitation->update(['accepted_at' => now()]);

        event(new Registered($user));
        Auth::login($user);

        return redirect()->route('driver.dashboard');
    }
}
