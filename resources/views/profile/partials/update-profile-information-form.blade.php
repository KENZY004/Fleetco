<section>
    <header class="mb-6">
        <div class="flex items-center gap-4 mb-2">
            <div class="h-8 w-8 rounded-lg bg-white/5 flex items-center justify-center text-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <h2 class="text-sm font-bold text-white uppercase tracking-wider">
                {{ __('Profile Information') }}
            </h2>
        </div>

        <p class="text-[11px] font-bold text-zinc-500 uppercase tracking-widest">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Name')" class="text-[10px] font-bold uppercase tracking-wider text-zinc-500 mb-2" />
            <x-text-input id="name" name="name" type="text" class="block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" class="text-[10px] font-bold uppercase tracking-wider text-zinc-500 mb-2" />
            <x-text-input id="email" name="email" type="email" class="block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-4 p-4 rounded-xl bg-orange-500/10 border border-orange-500/20">
                    <p class="text-xs text-orange-400 font-bold uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        {{ __('Your email address is unverified.') }}
                    </p>

                    <button form="send-verification" class="mt-2 text-[10px] font-black uppercase tracking-widest text-white hover:text-primary transition-colors underline decoration-primary/30">
                        {{ __('Click here to re-send the verification email.') }}
                    </button>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 text-[10px] font-bold text-emerald-400 uppercase tracking-widest">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-6">
            <button type="submit" class="px-8 py-4 bg-white text-black rounded-full text-[10px] font-black uppercase tracking-[0.2em] hover:bg-white/90 transition-all shadow-xl active:scale-95">
                {{ __('Update Details') }}
            </button>

            @if (session('status') === 'profile-updated')
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
                    <span class="text-[10px] font-black text-emerald-500 uppercase tracking-widest">{{ __('Profile Sync Successful') }}</span>
                </div>
            @endif
        </div>
    </form>
</section>

