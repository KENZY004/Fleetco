<section class="space-y-6">
    <header>
        <div class="flex items-center gap-4 mb-2">
            <div class="h-8 w-8 rounded-lg bg-red-500/10 flex items-center justify-center text-red-500">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <h2 class="text-sm font-bold text-white uppercase tracking-wider">
                {{ __('Danger Zone') }}
            </h2>
        </div>

        <p class="text-[11px] font-bold text-zinc-500 uppercase tracking-widest leading-relaxed">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted.') }}
        </p>
    </header>

    <button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="px-8 py-4 border border-red-500/20 text-red-500 rounded-full text-[10px] font-black uppercase tracking-[0.2em] hover:bg-red-500/10 transition-all active:scale-95"
    >
        {{ __('Terminate Account') }}
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-12 bg-zinc-950 border border-white/10 rounded-[3rem]">
            @csrf
            @method('delete')

            <h2 class="text-2xl font-heading font-bold text-white tracking-tight mb-4">
                {{ __('Confirm Termination?') }}
            </h2>

            <p class="text-sm text-zinc-500 leading-relaxed mb-8">
                {{ __('This action is irreversible. Please enter your password to confirm you would like to permanently delete your account.') }}
            </p>

            <div class="space-y-4">
                <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="block w-full"
                    placeholder="{{ __('Confirm with Password') }}"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-10 flex gap-4">
                <button type="button" x-on:click="$dispatch('close')" class="flex-1 py-4 border border-white/10 rounded-full text-[10px] font-bold uppercase tracking-wider text-zinc-400 hover:text-white transition-all">
                    {{ __('Cancel') }}
                </button>

                <button type="submit" class="flex-1 py-4 bg-red-500 text-black rounded-full text-[10px] font-bold uppercase tracking-wider hover:bg-red-400 transition-all shadow-xl shadow-red-500/20">
                    {{ __('Delete Permanently') }}
                </button>
            </div>
        </form>
    </x-modal>
</section>

