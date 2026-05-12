@extends('driver.layouts.app')

@section('content')
<div class="space-y-8 pb-12">
    <!-- 1. VEHICLE QUICK STATS -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
        <div class="fleetco-card p-5 md:p-6 rounded-2xl flex flex-col gap-1">
            <div class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest">Plate Number</div>
            <div class="font-heading text-2xl md:text-3xl font-bold text-white tracking-tight">{{ $vehicle->license_plate ?? 'N/A' }}</div>
        </div>

        <div class="fleetco-card p-5 md:p-6 rounded-2xl flex flex-col gap-1">
            <div class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest flex items-center gap-2">
                <div class="h-1.5 w-1.5 {{ ($vehicle && $vehicle->status === 'active') ? 'bg-emerald-500' : 'bg-zinc-500' }} rounded-full"></div>
                Asset Status
            </div>
            <div class="font-heading text-2xl md:text-3xl font-bold text-white tracking-tight uppercase">{{ $vehicle->status ?? 'UNASSIGNED' }}</div>
        </div>

        <div class="fleetco-card p-5 md:p-6 rounded-2xl flex flex-col gap-1">
            <div class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest">Vehicle Type</div>
            <div class="font-heading text-2xl md:text-3xl font-bold text-white tracking-tight">{{ $vehicle->type ?? '—' }}</div>
        </div>

        <div class="fleetco-card p-5 md:p-6 rounded-2xl flex flex-col gap-1">
            <div class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest">Assigned Since</div>
            <div class="font-heading text-xl md:text-2xl font-bold text-white tracking-tight">{{ $vehicle && $vehicle->pivot ? $vehicle->pivot->created_at->format('M d, Y') : '—' }}</div>
        </div>
    </div>

    <!-- 2. ASSET DETAILS & DIAGNOSTICS -->
    <div class="glass-obsidian rounded-[2rem] border border-white/10 overflow-hidden shadow-2xl">
        <div class="py-5 px-8 border-b border-white/5 bg-white/[0.02] flex justify-between items-center">
            <div>
                <span class="text-[9px] text-orange-500 font-bold uppercase tracking-[0.3em] mb-0.5 block">Fleet Operations</span>
                <h2 class="text-base font-bold text-white tracking-tight font-heading">Asset Profile</h2>
            </div>
        </div>

        <div class="p-6 md:p-12">
            @if($vehicle)
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    {{-- Visual Profile --}}
                    <div class="flex flex-col items-center justify-center p-12 rounded-[2rem] bg-white/[0.02] border border-white/5 relative group">
                        <div class="absolute inset-0 bg-orange-500/5 blur-3xl rounded-full opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        <svg class="w-32 h-32 text-zinc-800 mb-8 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                        <div class="text-center relative z-10">
                            <h3 class="text-2xl font-bold text-white mb-2">{{ $vehicle->name }}</h3>
                            <p class="text-xs font-bold text-zinc-500 uppercase tracking-[0.4em]">{{ $vehicle->license_plate }}</p>
                        </div>
                    </div>

                    {{-- Data Matrix --}}
                    <div class="space-y-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-5 rounded-2xl bg-white/5 border border-white/5">
                                <div class="text-[9px] text-zinc-500 font-bold uppercase tracking-widest mb-1">Make / Model</div>
                                <div class="text-sm font-bold text-white">{{ $vehicle->make ?? 'Unknown' }} {{ $vehicle->model ?? '' }}</div>
                            </div>
                            <div class="p-5 rounded-2xl bg-white/5 border border-white/5">
                                <div class="text-[9px] text-zinc-500 font-bold uppercase tracking-widest mb-1">Fuel Type</div>
                                <div class="text-sm font-bold text-white">{{ $vehicle->fuel_type ?? 'Diesel' }}</div>
                            </div>
                            <div class="p-5 rounded-2xl bg-white/5 border border-white/5">
                                <div class="text-[9px] text-zinc-500 font-bold uppercase tracking-widest mb-1">Odometer</div>
                                <div class="text-sm font-bold text-white">{{ number_format($vehicle->odometer ?? 0) }} KM</div>
                            </div>
                            <div class="p-5 rounded-2xl bg-white/5 border border-white/5">
                                <div class="text-[9px] text-zinc-500 font-bold uppercase tracking-widest mb-1">Payload Cap.</div>
                                <div class="text-sm font-bold text-white">4.5 Tons</div>
                            </div>
                        </div>

                        <div class="p-6 rounded-2xl border border-dashed border-white/10 bg-white/[0.01]">
                            <div class="flex items-center gap-4">
                                <div class="h-10 w-10 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-500">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                </div>
                                <div>
                                    <div class="text-[10px] font-bold text-white uppercase tracking-widest">Inspection Status</div>
                                    <div class="text-[9px] font-bold text-emerald-400 uppercase tracking-widest mt-0.5">Asset Cleared for Service</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="py-20 flex flex-col items-center justify-center opacity-40">
                    <div class="w-16 h-16 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-zinc-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                    </div>
                    <h4 class="text-[10px] font-black uppercase tracking-[0.4em] text-zinc-500 mb-2">No Vehicle Assigned</h4>
                    <p class="text-[9px] text-zinc-600 font-medium text-center max-w-[240px] leading-relaxed uppercase tracking-wider">Contact your fleet manager to link an asset to your profile.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
