<x-guest-layout>
    <div class="flex flex-col items-center justify-center min-h-screen p-8">
        <div class="w-full max-w-md">

            {{-- Brand --}}
            <div class="text-center mb-10">
                <div class="text-[9px] font-black uppercase tracking-[0.5em] text-orange-500 mb-4">FleetCo</div>
                <div class="w-10 h-[1px] bg-white/10 mx-auto"></div>
            </div>

            {{-- Icon --}}
            <div class="flex justify-center mb-8">
                <div class="w-20 h-20 rounded-full bg-white/5 border border-white/10 flex items-center justify-center">
                    <svg class="w-9 h-9 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
            </div>

            <div class="text-center mb-8">
                <div class="text-[10px] font-black uppercase tracking-[0.3em] text-zinc-500 mb-3">Join Your Fleet</div>
                <h1 class="font-heading text-2xl font-bold text-white mb-3">Enter Your Invite Code</h1>
                <p class="text-sm text-zinc-500 leading-relaxed">
                    Received an invite from your fleet manager? Enter the code below to register your driver account.
                    <br><br>
                    <span class="text-orange-500">Note:</span> Manual join requires email verification.
                </p>
            </div>

            @if ($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm">
                    @foreach ($errors->all() as $error)
                        <div>• {{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="GET" action="{{ route('register') }}" class="space-y-5">
                <div>
                    <label class="block text-xs font-semibold text-zinc-400 uppercase tracking-wider mb-2">Invite Code</label>
                    <input
                        type="text"
                        name="token"
                        placeholder="e.g. 4f2a8b..."
                        class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-zinc-600 text-sm outline-none focus:border-orange-500/50 transition-colors font-mono"
                        required
                    >
                </div>
                <div>
                    <label class="block text-xs font-semibold text-zinc-400 uppercase tracking-wider mb-2">Your Email</label>
                    <input
                        type="email"
                        name="email"
                        placeholder="driver@example.com"
                        class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-zinc-600 text-sm outline-none focus:border-orange-500/50 transition-colors"
                        required
                    >
                </div>

                <a href="{{ route('register.invite.store') }}"
                   onclick="this.closest('form').action='/register/invite/'+document.querySelector('[name=token]').value; this.closest('form').method='GET'; return true;"
                   class="hidden"></a>

                {{-- Redirect to invite page using token --}}
                <button
                    type="button"
                    onclick="window.location='/register/invite/'+document.querySelector('[name=token]').value"
                    class="w-full py-4 bg-white text-black rounded-xl font-bold text-sm hover:bg-zinc-200 transition-all active:scale-[0.98]"
                >
                    Look Up Invitation →
                </button>
            </form>

            <p class="text-center text-xs text-zinc-600 mt-8">
                Don't have an invite code?
                <a href="{{ route('login') }}" class="text-orange-500 hover:underline">Contact your fleet manager</a>
            </p>
        </div>
    </div>
</x-guest-layout>
