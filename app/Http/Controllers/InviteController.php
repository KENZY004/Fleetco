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
            'email'          => ['required', 'string', 'email', 'max:255'],
            'name'           => ['nullable', 'string', 'max:255'],
            'license_number' => ['nullable', 'string', 'max:255'],
            'plate_number'   => ['nullable', 'string', 'max:255'],
        ]);

        $manager = $request->user();

        // Generate a secure 64-char hex token
        $token = bin2hex(random_bytes(32)); // 32 bytes = 64 hex chars

        // If name is missing (Resend case), pull it from the existing invitation
        $name = $request->name;
        if (!$name) {
            $existing = DriverInvitation::where('email', $request->email)
                ->where('fleet_id', $manager->fleet_id)
                ->first();
            $name = $existing ? $existing->name : 'Driver';
        }

        // Create invitation (or update if pending for same email+fleet)
        $invitation = DriverInvitation::updateOrCreate(
            [
                'fleet_id'       => $manager->fleet_id,
                'email'          => $request->email,
            ],
            [
                'invited_by'     => $manager->id,
                'name'           => $name,
                'license_number' => $request->license_number,
                'plate_number'   => $request->plate_number,
                'token'          => $token,
                'expires_at'     => now()->addHours(48),
                'accepted_at'    => null,
            ]
        );

        Mail::to($request->email)->send(new DriverInvitationMail($invitation, $manager));

        return redirect()->route('drivers.index')
            ->with('success', "✓ Invite sent to {$request->email}. Awaiting driver confirmation.");
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
            'name'              => $invitation->name,
            'email'             => $request->email,
            'password'          => Hash::make($request->password),
            'role'              => 'driver',
            'fleet_id'          => $invitation->fleet_id,
            // Pre-vouched — mark email as verified since the manager invited them
            'email_verified_at' => now(),
        ]);

        // Auto-create the Driver profile so they appear in the fleet's driver list
        $driver = Driver::create([
            'user_id'        => $user->id,
            'fleet_id'       => $invitation->fleet_id,
            'name'           => $user->name,
            'license_number' => $invitation->license_number,
            'risk_score'     => 100,
        ]);

        // Mark invitation as accepted
        $invitation->update(['accepted_at' => now()]);

        event(new Registered($user));
        Auth::login($user);

        return redirect()->route('driver.dashboard');
    }

    /**
     * DELETE /fleet/invite/{id}
     * Revoke/Delete a pending invitation.
     */
    public function revoke(int $id): RedirectResponse
    {
        $invitation = DriverInvitation::findOrFail($id);

        // Security check: Only the manager of that fleet can revoke
        if ($invitation->fleet_id !== Auth::user()->fleet_id) {
            abort(403);
        }

        $email = $invitation->email;
        $invitation->delete();

        return back()->with('success', "✓ Invitation for {$email} has been revoked and the link is now invalid.");
    }
}
