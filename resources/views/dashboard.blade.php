@extends('layouts.app')

@section('main-class', '')

@section('content')
<div 
    class="relative h-screen w-full overflow-hidden" 
    x-data="fleetDashboard({ alerts: @json($recentAlerts), geofences: @json($geofences) })"
>
    <!-- 1. FULL SCREEN BASE MAP -->
    <div id="map" class="absolute inset-0 z-0"></div>

    <!-- 2. TOP HUD: Real-time Stats -->
    <div class="absolute top-4 md:top-6 left-4 md:left-6 right-4 md:right-6 z-[1000] pointer-events-none">
        <div class="flex flex-col md:flex-row justify-between items-start gap-6 md:gap-6">
            <!-- Left Side: Map Info -->
            <div class="glass-obsidian p-4 md:p-6 rounded-2xl md:rounded-[2rem] border border-white/10 pointer-events-auto shadow-2xl">
                <div class="text-[8px] md:text-[10px] text-orange-500 uppercase font-bold tracking-wider mb-1 md:mb-2">Ops Intelligence</div>
                <div class="font-heading text-sm md:text-xl font-bold text-white tracking-tight mb-1" x-text="viewportCoords || '0.0000° N, 0.0000° E'"></div>
                <div class="flex items-center gap-2">
                    <div class="h-1 w-1 md:h-1.5 md:w-1.5 bg-emerald-500 rounded-full animate-pulse"></div>
                    <span class="text-[8px] md:text-[9px] font-bold text-zinc-500 uppercase tracking-widest" x-text="'System: ' + systemStatus"></span>
                </div>
            </div>

            <!-- Center/Right: Live Stats Matrix -->
            <div class="flex-1 pointer-events-auto">
                <x-stats-overview :stats="$stats ?? []" />
            </div>
        </div>
    </div>

    <!-- 3. LEFT SIDEBAR: Fleet List -->
    <div class="absolute left-4 md:left-6 top-32 bottom-4 md:bottom-10 w-full md:w-80 z-[1000] pointer-events-none hidden md:block">
        <div class="h-full flex flex-col gap-4 pointer-events-auto">
            <div class="glass-obsidian rounded-[2rem] border border-white/10 flex flex-col overflow-hidden shadow-2xl backdrop-blur-xl">
                <div class="p-6 border-b border-white/5">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xs font-black uppercase tracking-[0.2em] text-white">Live Fleet</h2>
                        <div class="px-2 py-1 rounded-full bg-emerald-500/10 text-emerald-500 text-[8px] font-black uppercase tracking-widest border border-emerald-500/20" x-text="vehicles.length + ' Units'"></div>
                    </div>
                    <div class="relative">
                        <input type="text" x-model="searchQuery" placeholder="Search vehicle ID..." class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-xs text-white placeholder-zinc-600 focus:outline-none focus:border-orange-500/50 transition-all">
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto custom-scrollbar p-2 space-y-1 max-h-[60vh]">
                    <template x-for="vehicle in vehicles" :key="vehicle.id">
                        <div @click="selectVehicle(vehicle)" 
                             :class="selectedVehicle?.id === vehicle.id ? 'bg-orange-500/10 border-orange-500/30' : 'hover:bg-white/5 border-transparent'"
                             class="group p-4 rounded-2xl border transition-all cursor-pointer">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <svg class="w-5 h-5 text-zinc-500" :class="vehicle.status === 'active' ? 'text-orange-500' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-xs font-bold text-white truncate" x-text="vehicle.license_plate"></div>
                                    <div class="text-[9px] text-zinc-500 font-bold uppercase tracking-widest mt-0.5" x-text="vehicle.status"></div>
                                </div>
                                <div class="h-1.5 w-1.5 rounded-full" :class="vehicle.status === 'active' ? 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]' : 'bg-zinc-700'"></div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. RIGHT SIDEBAR: Live Alerts -->
    <div class="absolute right-4 md:right-6 top-32 bottom-4 md:bottom-10 w-full md:w-96 z-[1000] pointer-events-none hidden md:block">
        <x-anomaly-feed />
    </div>
</div>
@endsection
