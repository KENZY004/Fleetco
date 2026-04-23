<x-guest-layout>
    <div class="flex flex-col lg:flex-row">
        <!-- Sidebar Image -->
        <div class="hidden lg:block w-5/12 relative">
            <img src="/auth_sidebar.png" alt="Fleetco" class="absolute inset-0 w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-r from-transparent to-[#08080c]"></div>
            <div class="absolute bottom-12 left-12 right-12">
                <h3 class="font-heading text-2xl font-bold text-white mb-3">The Future of Fleet Management</h3>
                <p class="text-sm text-zinc-400 leading-relaxed">
                    Access your real-time tracking dashboard and manage your global assets with precision.
                </p>
            </div>
        </div>

        <!-- Form Content -->
        <div class="w-full lg:w-7/12 p-8 lg:p-20 flex flex-col justify-center">
            <div class="mb-10">
                <h2 class="font-heading text-3xl font-bold text-white mb-2">Sign in</h2>
                <p class="text-sm text-zinc-500">
                    New to Fleetco? 
                    <a href="{{ route('register') }}" class="text-white hover:text-orange-500 transition-colors font-medium">Create an account</a>
                </p>
            </div>

            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-semibold text-zinc-400 uppercase tracking-wider mb-2">Email Address</label>
                    <x-text-input id="email" class="block w-full px-4 py-3" type="email" name="email" :value="old('email')" required autofocus placeholder="Email" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div>
                    <div class="flex justify-between mb-2">
                        <label for="password" class="block text-xs font-semibold text-zinc-400 uppercase tracking-wider">Password</label>
                        @if (Route::has('password.request'))
                            <a class="text-xs font-medium text-zinc-500 hover:text-white transition-colors" href="{{ route('password.request') }}">
                                Forgot password?
                            </a>
                        @endif
                    </div>
                    <x-text-input id="password" class="block w-full px-4 py-3" type="password" name="password" required placeholder="Password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div class="flex items-center">
                    <input id="remember_me" type="checkbox" name="remember" class="rounded bg-white/5 border-white/10 text-orange-500 shadow-sm focus:ring-orange-500 focus:ring-offset-0">
                    <span class="ms-3 text-sm text-zinc-400 font-medium">Stay signed in</span>
                </div>

                <button type="submit" class="w-full py-4 bg-white text-black rounded-xl font-bold text-sm hover:bg-zinc-200 transition-all active:scale-[0.98] shadow-lg shadow-white/5">
                    Sign in to Dashboard
                </button>

                <div class="relative py-4">
                    <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-white/5"></div></div>
                    <div class="relative flex justify-center text-xs text-zinc-500 bg-[#08080c] px-4 font-medium uppercase tracking-widest">Or continue with</div>
                </div>

                <button type="button" class="w-full flex items-center justify-center gap-3 py-3 px-6 bg-white/5 border border-white/10 rounded-xl hover:bg-white/10 transition-all active:scale-[0.98] group">
                    <svg class="h-5 w-5" viewBox="0 0 24 24">
                        <path fill="#EA4335" d="M12 5.04c1.94 0 3.51.68 4.79 1.94l3.51-3.51C18.17 1.41 15.35 0 12 0 7.28 0 3.25 2.73 1.34 6.74l4.05 3.15C6.34 7.03 8.95 5.04 12 5.04z"/>
                        <path fill="#4285F4" d="M23.49 12.27c0-.79-.07-1.54-.19-2.27H12v4.51h6.47c-.29 1.48-1.14 2.73-2.4 3.58l3.76 2.91c2.2-2.02 3.46-5 3.46-8.73z"/>
                        <path fill="#FBBC05" d="M5.39 14.89c-.25-.74-.4-1.53-.4-2.39s.15-1.65.4-2.39l-4.05-3.15C.5 8.7 0 10.29 0 12c0 1.71.5 3.3 1.34 4.74l4.05-3.15z"/>
                        <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.76-2.91c-1.08.72-2.45 1.16-4.17 1.16-3.19 0-5.89-2.15-6.85-5.06l-4.15 3.22C2.61 21.03 7 24 12 24z"/>
                    </svg>
                    <span class="text-sm font-semibold text-white">Google</span>
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>
