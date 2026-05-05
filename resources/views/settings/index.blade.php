@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    
    {{-- Header --}}
    <div>
        <div class="text-[10px] font-bold tracking-widest text-primary uppercase mb-2">Command Center</div>
        <h1 class="font-heading text-3xl font-bold tracking-tight">Global Platform Settings</h1>
        <p class="text-zinc-500 text-sm mt-1">Configure fleet-wide thresholds, branding and system behavior.</p>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm font-medium flex items-center gap-3">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('success') }}
    </div>
    @endif

    <form action="{{ route('settings.update') }}" method="POST" class="space-y-6">
        @csrf

        {{-- Branding Section --}}
        <div class="p-8 rounded-[2.5rem] bg-zinc-950 border border-white/5 space-y-6">
            <div class="flex items-center gap-4 mb-2">
                <div class="h-10 w-10 rounded-xl bg-white/5 flex items-center justify-center text-primary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-7h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <h2 class="text-lg font-bold text-white uppercase tracking-wider">Company Identity</h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-wider text-zinc-500 block mb-2">Platform Name</label>
                    <input type="text" name="company_name" value="{{ $settings['company_name'] ?? '' }}" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm outline-none focus:border-primary/50 transition-colors">
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-wider text-zinc-500 block mb-2">Support Email</label>
                    <input type="email" name="alert_email" value="{{ $settings['alert_email'] ?? '' }}" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm outline-none focus:border-primary/50 transition-colors">
                </div>
            </div>
        </div>

        {{-- Telematics Thresholds --}}
        <div class="p-8 rounded-[2.5rem] bg-zinc-950 border border-white/5 space-y-6">
            <div class="flex items-center gap-4 mb-2">
                <div class="h-10 w-10 rounded-xl bg-white/5 flex items-center justify-center text-orange-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <h2 class="text-lg font-bold text-white uppercase tracking-wider">Safety Thresholds</h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-wider text-zinc-500 block mb-2">Global Speed Limit (KM/H)</label>
                    <input type="number" name="speed_limit" value="{{ $settings['speed_limit'] ?? '' }}" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm outline-none focus:border-primary/50 transition-colors">
                    <p class="text-[10px] text-zinc-600 mt-2 italic">Exceeding this value will trigger an automatic Speeding Incident.</p>
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-wider text-zinc-500 block mb-2">System Simulation</label>
                    <select name="simulation_enabled" class="w-full bg-zinc-900 border border-white/10 rounded-xl px-4 py-3 text-white text-sm outline-none focus:border-primary/50 transition-colors">
                        <option value="1" {{ ($settings['simulation_enabled'] ?? '') == '1' ? 'selected' : '' }}>Enabled (Test Mode)</option>
                        <option value="0" {{ ($settings['simulation_enabled'] ?? '') == '0' ? 'selected' : '' }}>Disabled (Production)</option>
                    </select>
                    <p class="text-[10px] text-zinc-600 mt-2 italic">When enabled, pings from the simulator are accepted.</p>
                </div>
            </div>
        </div>

        <div class="flex justify-end pt-4">
            <button type="submit" class="px-12 py-4 bg-primary text-black rounded-full text-xs font-black uppercase tracking-[0.2em] hover:bg-orange-400 transition-all shadow-xl shadow-primary/20">
                Save Command Configuration
            </button>
        </div>
    </form>
</div>
@endsection
