<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Unassigned — FleetCo</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { background: #0a0a0a; font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="text-white antialiased flex items-center justify-center min-h-screen p-8">

    <div class="w-full max-w-md text-center">

        {{-- Icon --}}
        <div class="flex justify-center mb-8">
            <div class="w-24 h-24 rounded-full border border-white/5 bg-white/3 flex items-center justify-center">
                <svg class="w-10 h-10 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
        </div>

        {{-- Label --}}
        <div class="text-[10px] font-black uppercase tracking-[0.3em] text-zinc-600 mb-3">Account Status</div>
        <h1 class="text-2xl font-bold text-white mb-4">No Fleet Assigned</h1>
        <p class="text-sm text-zinc-500 leading-relaxed mb-8">
            Your account is not yet linked to a fleet. This usually means your fleet manager hasn't sent you an invitation yet, or your invitation is still pending.
        </p>

        {{-- Info box --}}
        <div class="p-5 rounded-2xl bg-white/3 border border-white/5 text-left mb-8 space-y-3">
            <div class="text-[10px] font-bold uppercase tracking-widest text-zinc-500 mb-3">What to do next</div>
            <div class="flex items-start gap-3">
                <div class="w-5 h-5 rounded-full bg-orange-500/10 border border-orange-500/20 flex items-center justify-center flex-shrink-0 mt-0.5">
                    <span class="text-orange-500 text-[9px] font-bold">1</span>
                </div>
                <p class="text-xs text-zinc-400">Contact your fleet manager and ask them to send you an invitation from their FleetCo dashboard.</p>
            </div>
            <div class="flex items-start gap-3">
                <div class="w-5 h-5 rounded-full bg-orange-500/10 border border-orange-500/20 flex items-center justify-center flex-shrink-0 mt-0.5">
                    <span class="text-orange-500 text-[9px] font-bold">2</span>
                </div>
                <p class="text-xs text-zinc-400">Check your email inbox for an invitation link. Click it to join your fleet automatically.</p>
            </div>
            <div class="flex items-start gap-3">
                <div class="w-5 h-5 rounded-full bg-orange-500/10 border border-orange-500/20 flex items-center justify-center flex-shrink-0 mt-0.5">
                    <span class="text-orange-500 text-[9px] font-bold">3</span>
                </div>
                <p class="text-xs text-zinc-400">If you have an invite code, <a href="{{ route('join') }}" class="text-orange-500 hover:underline">enter it here</a>.</p>
            </div>
        </div>

        {{-- Account info --}}
        <div class="p-4 rounded-xl bg-white/3 border border-white/5 text-left mb-8">
            <div class="text-[9px] font-bold uppercase tracking-widest text-zinc-600 mb-2">Your Account</div>
            <div class="text-sm text-white font-medium">{{ auth()->user()->name }}</div>
            <div class="text-xs text-zinc-500">{{ auth()->user()->email }}</div>
        </div>

        {{-- Logout --}}
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-xs text-zinc-600 hover:text-white transition-colors uppercase tracking-widest font-bold">
                Log Out
            </button>
        </form>
    </div>

</body>
</html>
