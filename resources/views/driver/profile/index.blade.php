@extends('driver.layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">

    {{-- Page Header --}}
    <div>
        <div class="text-[10px] font-bold tracking-widest text-[#ff8a00] uppercase mb-2">Command Center</div>
        <h1 class="font-heading text-3xl font-bold tracking-tight text-white">Profile Settings</h1>
        <p class="text-[#555] text-sm mt-1">Manage your identity, security and system preferences.</p>
    </div>

    {{-- Forms Grid --}}
    <div class="space-y-8">
        <!-- Profile Information Card -->
        <div class="bg-[#111] border border-[#1a1a1a] rounded-xl p-8">
            <header class="mb-6">
                <div class="flex items-center gap-4 mb-2">
                    <div class="h-8 w-8 rounded-lg bg-white/5 flex items-center justify-center text-[#ff8a00]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <h2 class="text-sm font-bold text-white uppercase tracking-wider">
                        Profile Information
                    </h2>
                </div>
                <p class="text-[11px] font-bold text-[#555] uppercase tracking-widest">
                    Update your account's profile information and email address.
                </p>
            </header>

            <form method="post" action="{{ route('driver.profile.update') }}" class="space-y-6">
                @csrf
                @method('put')

                <div>
                    <label for="name" class="block text-[10px] font-bold uppercase tracking-wider text-[#555] mb-2">Name</label>
                    <input id="name" name="name" type="text" class="block w-full bg-[#1a1a1a] border border-[#222] text-white rounded-lg px-4 py-3 outline-none focus:border-[#ff8a00] transition-colors" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" />
                    @error('name')
                        <p class="text-[10px] font-bold text-[#ef4444] uppercase tracking-widest mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-[10px] font-bold uppercase tracking-wider text-[#555] mb-2">Email</label>
                    <input id="email" name="email" type="email" class="block w-full bg-[#1a1a1a] border border-[#222] text-white rounded-lg px-4 py-3 outline-none focus:border-[#ff8a00] transition-colors" value="{{ old('email', $user->email) }}" required autocomplete="username" />
                    @error('email')
                        <p class="text-[10px] font-bold text-[#ef4444] uppercase tracking-widest mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone_number" class="block text-[10px] font-bold uppercase tracking-wider text-[#555] mb-2">Phone Number</label>
                    <input id="phone_number" name="phone_number" type="text" class="block w-full bg-[#1a1a1a] border border-[#222] text-white rounded-lg px-4 py-3 outline-none focus:border-[#ff8a00] transition-colors" value="{{ old('phone_number', $user->driver?->phone_number) }}" autocomplete="tel" placeholder="+91 98765 43210" />
                    @error('phone_number')
                        <p class="text-[10px] font-bold text-[#ef4444] uppercase tracking-widest mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center gap-6 pt-4 border-t border-[#1a1a1a]">
                    <button type="submit" class="px-8 py-4 bg-white text-black rounded-full text-[10px] font-black uppercase tracking-[0.2em] hover:bg-white/90 transition-all shadow-xl active:scale-95">
                        Update Details
                    </button>

                    @if (session('status') === 'profile-updated')
                        <p
                            x-data="{ show: true }"
                            x-show="show"
                            x-transition
                            x-init="setTimeout(() => show = false, 2000)"
                            class="text-[10px] font-bold text-[#10b981] uppercase tracking-widest animate-pulse"
                        >Changes Saved</p>
                    @endif
                </div>
            </form>
            
            <div class="mt-8">
                {{-- RBAC: role/fleet fields restricted --}}
                <p class="text-xs text-[#555] italic">System roles and fleet assignments are managed by the Fleet Administrator.</p>
            </div>
        </div>

        <!-- Update Password Card -->
        <div class="bg-[#111] border border-[#1a1a1a] rounded-xl p-8">
            <header class="mb-6">
                <div class="flex items-center gap-4 mb-2">
                    <div class="h-8 w-8 rounded-lg bg-white/5 flex items-center justify-center text-[#ff8a00]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                    <h2 class="text-sm font-bold text-white uppercase tracking-wider">
                        Update Password
                    </h2>
                </div>
                <p class="text-[11px] font-bold text-[#555] uppercase tracking-widest">
                    Ensure your account is using a long, random password to stay secure.
                </p>
            </header>

            <form method="post" action="{{ route('driver.profile.password') }}" class="space-y-6">
                @csrf
                @method('put')

                <div>
                    <label for="current_password" class="block text-[10px] font-bold uppercase tracking-wider text-[#555] mb-2">Current Password</label>
                    <input id="current_password" name="current_password" type="password" class="block w-full bg-[#1a1a1a] border border-[#222] text-white rounded-lg px-4 py-3 outline-none focus:border-[#ff8a00] transition-colors" autocomplete="current-password" />
                    @error('current_password', 'updatePassword')
                        <p class="text-[10px] font-bold text-[#ef4444] uppercase tracking-widest mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-[10px] font-bold uppercase tracking-wider text-[#555] mb-2">New Password</label>
                    <input id="password" name="password" type="password" class="block w-full bg-[#1a1a1a] border border-[#222] text-white rounded-lg px-4 py-3 outline-none focus:border-[#ff8a00] transition-colors" autocomplete="new-password" />
                    @error('password', 'updatePassword')
                        <p class="text-[10px] font-bold text-[#ef4444] uppercase tracking-widest mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-[10px] font-bold uppercase tracking-wider text-[#555] mb-2">Confirm Password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" class="block w-full bg-[#1a1a1a] border border-[#222] text-white rounded-lg px-4 py-3 outline-none focus:border-[#ff8a00] transition-colors" autocomplete="new-password" />
                    @error('password_confirmation', 'updatePassword')
                        <p class="text-[10px] font-bold text-[#ef4444] uppercase tracking-widest mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center gap-6 pt-4 border-t border-[#1a1a1a]">
                    <button type="submit" class="px-8 py-4 bg-[#ff8a00] text-black rounded-full text-[10px] font-black uppercase tracking-[0.2em] hover:bg-[#ff8a00]/90 transition-all shadow-[0_0_15px_rgba(255,138,0,0.3)] active:scale-95">
                        Save Password
                    </button>

                    @if (session('status') === 'password-updated')
                        <p
                            x-data="{ show: true }"
                            x-show="show"
                            x-transition
                            x-init="setTimeout(() => show = false, 2000)"
                            class="text-[10px] font-bold text-[#10b981] uppercase tracking-widest animate-pulse"
                        >Saved</p>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
