<x-guest-layout>
    <div class="flex flex-col lg:flex-row">
        <!-- Sidebar Image -->
        <div class="hidden lg:block w-5/12 relative">
            <img src="/auth_sidebar.png" alt="Fleetco" class="absolute inset-0 w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-r from-transparent to-[#08080c]"></div>
            <div class="absolute bottom-12 left-12 right-12">
                <h3 class="font-heading text-2xl font-bold text-white mb-3">You're invited.</h3>
                <p class="text-sm text-zinc-400 leading-relaxed">
                    Your fleet manager has sent you a secure invitation. Set up your driver account to get started.
                </p>
            </div>
        </div>

        <!-- Form Content -->
        <div class="w-full lg:w-7/12 p-8 lg:p-20 flex flex-col justify-center">
            <div class="mb-10">
                {{-- DRIVER INVITE BADGE --}}
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-emerald-500/30 bg-emerald-500/10 mb-6">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="text-[10px] font-black uppercase tracking-[0.3em] text-emerald-400">Secure Driver Invite</span>
                </div>
                <h2 class="font-heading text-3xl font-bold text-white mb-2">Accept your invitation</h2>
                <p class="text-sm text-zinc-500">
                    You've been invited to join <strong class="text-white">{{ $invitation->fleet->name ?? 'a Fleet' }}</strong>.
                    Create your account below to get started.
                </p>
            </div>

            @if ($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register.invite.store') }}" class="space-y-6">
                @csrf
                <input type="hidden" name="token" value="{{ $invitation->token }}">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="first_name" class="block text-xs font-semibold text-zinc-400 uppercase tracking-wider mb-2">First Name</label>
                        <x-text-input id="first_name" class="block w-full px-4 py-3" type="text" name="first_name" :value="old('first_name')" required autofocus placeholder="First name" />
                        <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                    </div>
                    <div>
                        <label for="last_name" class="block text-xs font-semibold text-zinc-400 uppercase tracking-wider mb-2">Last Name</label>
                        <x-text-input id="last_name" class="block w-full px-4 py-3" type="text" name="last_name" :value="old('last_name')" required placeholder="Last name" />
                        <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                    </div>
                </div>

                {{-- Email: pre-filled and locked --}}
                <div>
                    <label for="email" class="block text-xs font-semibold text-zinc-400 uppercase tracking-wider mb-2">Email Address</label>
                    <div class="relative">
                        <x-text-input id="email" class="block w-full px-4 py-3 pr-12 bg-white/3 cursor-not-allowed opacity-70" type="email" name="email" :value="$invitation->email" readonly />
                        <div class="absolute right-3 top-1/2 -translate-y-1/2">
                            <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </div>
                    </div>
                    <p class="text-[10px] text-zinc-600 mt-1">● Pre-verified via your invitation. This email cannot be changed.</p>
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold text-zinc-400 uppercase tracking-wider mb-2">Password</label>
                    <x-text-input id="password" class="block w-full px-4 py-3" type="password" name="password" required placeholder="Choose a strong password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs font-semibold text-zinc-400 uppercase tracking-wider mb-2">Confirm Password</label>
                    <x-text-input id="password_confirmation" class="block w-full px-4 py-3" type="password" name="password_confirmation" required placeholder="Repeat password" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <button type="submit" class="w-full py-4 bg-white text-black rounded-xl font-bold text-sm hover:bg-zinc-200 transition-all active:scale-[0.98] shadow-lg shadow-white/5">
                    Activate Driver Account →
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>
