<x-guest-layout>
    <div class="flex flex-col items-center justify-center min-h-screen p-8">
        <div class="w-full max-w-md">

            {{-- Logo / Brand --}}
            <div class="text-center mb-10">
                <div class="text-[9px] font-black uppercase tracking-[0.5em] text-orange-500 mb-4">FleetCo</div>
                <div class="w-10 h-[1px] bg-white/10 mx-auto"></div>
            </div>

            {{-- Envelope Icon --}}
            <div class="flex justify-center mb-8">
                <div class="relative">
                    <div class="w-24 h-24 rounded-full bg-orange-500/10 border border-orange-500/20 flex items-center justify-center">
                        <svg class="w-10 h-10 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="absolute -top-1 -right-1 w-6 h-6 rounded-full bg-orange-500 flex items-center justify-center shadow-[0_0_12px_rgba(255,138,0,0.6)]">
                        <svg class="w-3 h-3 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Content --}}
            <div class="text-center mb-8">
                <div class="text-[10px] font-black uppercase tracking-[0.3em] text-orange-500 mb-3">Verify Your Email</div>
                <h1 class="font-heading text-2xl font-bold text-white mb-3">Check your inbox</h1>
                <p class="text-sm text-zinc-500 leading-relaxed">
                    Thanks for registering! Before you access the dashboard, we need to verify your email address.
                    We've sent a verification link to <strong class="text-white">{{ auth()->user()->email }}</strong>.
                </p>
            </div>

            {{-- Status message --}}
            @if (session('status') == 'verification-link-sent')
                <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm text-center">
                    ✓ A new verification link has been sent to your email address.
                </div>
            @endif

            {{-- Actions --}}
            <div class="space-y-4">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit" class="w-full py-4 bg-white text-black rounded-xl font-bold text-sm hover:bg-zinc-200 transition-all active:scale-[0.98] shadow-lg shadow-white/5">
                        Resend Verification Email
                    </button>
                </form>

                <div class="flex items-center gap-4">
                    <div class="flex-1 h-[1px] bg-white/5"></div>
                    <span class="text-xs text-zinc-600 uppercase tracking-widest">or</span>
                    <div class="flex-1 h-[1px] bg-white/5"></div>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full py-3 border border-white/10 rounded-xl text-xs font-bold uppercase tracking-wider text-zinc-500 hover:text-white hover:border-white/20 transition-all">
                        Log Out
                    </button>
                </form>
            </div>

            <p class="text-center text-[10px] text-zinc-700 mt-8">
                Didn't receive the email? Check your spam folder or resend above.
            </p>
        </div>
    </div>
</x-guest-layout>
