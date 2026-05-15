<section>
    <header class="mb-6">
        <div class="flex items-center gap-4 mb-2">
            <div class="h-8 w-8 rounded-lg bg-white/5 flex items-center justify-center text-orange-500">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            </div>
            <h2 class="text-sm font-bold text-white uppercase tracking-wider">
                {{ __('Update Password') }}
            </h2>
        </div>

        <p class="text-[11px] font-bold text-zinc-500 uppercase tracking-widest">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="space-y-6">
        @csrf
        @method('put')

        <div>
            <x-input-label for="update_password_current_password" :value="__('Current Password')" class="text-[10px] font-bold uppercase tracking-wider text-zinc-500 mb-2" />
            <x-text-input id="update_password_current_password" name="current_password" type="password" class="block w-full" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password" :value="__('New Password')" class="text-[10px] font-bold uppercase tracking-wider text-zinc-500 mb-2" />
            <x-text-input id="update_password_password" name="password" type="password" class="block w-full" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" :value="__('Confirm Password')" class="text-[10px] font-bold uppercase tracking-wider text-zinc-500 mb-2" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="block w-full" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-6">
            <button type="submit" class="px-8 py-4 bg-white text-black rounded-full text-[10px] font-black uppercase tracking-[0.2em] hover:bg-white/90 transition-all shadow-xl active:scale-95">
                {{ __('Save Password') }}
            </button>

            @if (session('status') === 'password-updated')
                <div
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-x-4"
                    x-transition:enter-end="opacity-100 translate-x-0"
                    x-transition:leave="transition ease-in duration-300"
                    x-transition:leave-start="opacity-100 translate-x-0"
                    x-transition:leave-end="opacity-0 translate-x-4"
                    x-init="setTimeout(() => show = false, 3000)"
                    class="flex items-center gap-3 px-5 py-3 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl"
                >
                    <div class="h-5 w-5 rounded-full bg-emerald-500 flex items-center justify-center text-black">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <span class="text-[10px] font-black text-emerald-500 uppercase tracking-widest">{{ __('Security Credentials Updated') }}</span>
                </div>
            @endif
        </div>
    </form>
</section>

