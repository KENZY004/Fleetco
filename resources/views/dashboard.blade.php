@extends('layouts.app')

@section('main-class', '')

@section('content')
<div 
    class="relative h-screen w-full overflow-hidden" 
    x-data="fleetDashboard()"
    x-init="init()"
>
    <!-- 1. FULL SCREEN BASE MAP -->
    <div id="map" class="absolute inset-0 z-0"></div>
    <!-- 2. TOP HUD: Real-time Stats -->
    <div class="absolute top-4 md:top-6 left-4 md:left-6 right-4 md:right-6 z-[1000] pointer-events-none">
        <div class="flex flex-col lg:flex-row justify-between items-start gap-4 md:gap-6">
            <!-- Left Side: Map Info -->
            <div class="glass-obsidian p-3 md:p-6 rounded-2xl md:rounded-[2rem] border border-white/10 pointer-events-auto shadow-2xl w-full lg:w-auto">
                <div class="text-[8px] md:text-[10px] text-orange-500 uppercase font-bold tracking-wider mb-1 md:mb-2">Ops Intelligence</div>
                <div class="font-heading text-xs md:text-xl font-bold text-white tracking-tight mb-1" x-text="viewportCoords"></div>
                <div class="flex items-center gap-2">
                    <div class="h-1 w-1 md:h-1.5 md:w-1.5 bg-emerald-500 rounded-full animate-pulse"></div>
                    <span class="text-[7px] md:text-[9px] font-bold text-zinc-500 uppercase tracking-widest" x-text="'System: ' + systemStatus"></span>
                </div>
            </div>

            <!-- Center/Right: Live Stats Matrix -->
            <div class="w-full lg:flex-1 pointer-events-auto overflow-x-auto no-scrollbar">
                <x-stats-overview :stats="$stats ?? []" />
            </div>
        </div>
    </div>

    <!-- 3. LEFT HUD: Fleet Matrix (Desktop Only - Hidden on small screens) -->
    <div class="hidden xl:flex absolute top-44 left-6 bottom-10 w-72 z-[1000] pointer-events-auto flex-col gap-4">
        <div class="glass-obsidian rounded-[2rem] border border-white/10 overflow-hidden flex flex-col flex-1 shadow-2xl">
            <div class="py-5 px-8 border-b border-white/5 bg-white/[0.02] flex justify-between items-center">
                <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-500">Live Fleet</span>
                <span class="text-[9px] font-bold px-2 py-1 bg-primary/20 text-primary uppercase rounded" x-text="vehicles.length"></span>
            </div>
            
            <div class="flex-1 overflow-y-auto custom-scrollbar">
                <div class="divide-y divide-white/5">
                    <template x-for="vehicle in vehicles" :key="vehicle.id">
                        <div 
                            @click="selectVehicle(vehicle)"
                            class="p-6 hover:bg-white/5 cursor-pointer transition-all flex items-center gap-5 group/item"
                            :class="selectedVehicle?.id == vehicle.id ? 'bg-primary/10' : ''"
                        >
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-bold text-white tracking-tight truncate group-hover/item:text-primary transition-colors" x-text="vehicle.name"></h4>
                                <div class="flex items-center gap-2">
                                    <span class="text-[10px] font-medium text-zinc-600 tracking-tight" x-text="vehicle.license_plate"></span>
                                    <template x-if="vehicle.active_route">
                                        <div class="flex items-center gap-1">
                                            <div class="h-1 w-1 bg-primary rounded-full"></div>
                                            <span class="text-[8px] font-bold text-primary uppercase tracking-widest" 
                                                  x-text="vehicle.active_route.name + ' (' + reachedCount(vehicle.active_route.waypoints) + '/' + vehicle.active_route.waypoints.length + ')'"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <div 
                                class="h-2 w-2 rounded-full transition-all duration-500"
                                :class="(vehicle.status === 'active' || vehicle.status === 'moving') ? 'bg-primary shadow-[0_0_12px_#ff8a00] scale-110' : 'bg-zinc-800'"
                            ></div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- MOBILE FLEET DRAWER -->
    <div 
        x-show="isFleetListOpen"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="translate-y-full"
        x-transition:enter-end="translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="translate-y-0"
        x-transition:leave-end="translate-y-full"
        class="xl:hidden fixed inset-0 z-[5000] bg-black/95 backdrop-blur-2xl p-4 flex flex-col"
        style="display: none;"
    >
        <div class="flex justify-between items-center mb-6 px-4 pt-4">
            <div>
                <div class="text-[8px] text-primary font-black uppercase tracking-[0.4em] mb-1">Fleet Intelligence</div>
                <h3 class="text-xl font-bold text-white tracking-tight">Active Units</h3>
            </div>
            <button @click="isFleetListOpen = false" class="h-12 w-12 rounded-full bg-white/5 flex items-center justify-center text-zinc-500 hover:text-white border border-white/10">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        
        <div class="flex-1 overflow-y-auto custom-scrollbar px-2">
            <div class="space-y-3 pb-20">
                <template x-for="vehicle in vehicles" :key="vehicle.id">
                    <div 
                        @click="selectVehicle(vehicle); isFleetListOpen = false;"
                        class="p-5 rounded-2xl border border-white/5 bg-white/[0.03] flex items-center gap-4 active:scale-[0.98] transition-all"
                        :class="selectedVehicle?.id == vehicle.id ? 'border-primary/50 bg-primary/5 shadow-[0_0_20px_rgba(255,138,0,0.15)]' : ''"
                    >
                        <div class="h-12 w-12 rounded-xl bg-white/5 flex items-center justify-center text-zinc-500" :class="selectedVehicle?.id == vehicle.id ? 'text-primary' : ''">
                             <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-base font-bold text-white tracking-tight truncate" x-text="vehicle.name"></h4>
                            <span class="text-[10px] font-bold text-zinc-600 uppercase tracking-widest" x-text="vehicle.license_plate"></span>
                        </div>
                        <div class="h-2.5 w-2.5 rounded-full" :class="(vehicle.status === 'active' || vehicle.status === 'moving') ? 'bg-emerald-500 shadow-[0_0_15px_rgba(16,185,129,0.5)]' : 'bg-zinc-800'"></div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- MOBILE ALERTS DRAWER -->
    <div 
        x-show="isAlertsOpen"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="translate-y-full"
        x-transition:enter-end="translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="translate-y-0"
        x-transition:leave-end="translate-y-full"
        class="xl:hidden fixed inset-0 z-[5000] bg-black/95 backdrop-blur-2xl p-4 flex flex-col"
        style="display: none;"
    >
        <div class="flex justify-between items-center mb-6 px-4 pt-4">
            <div>
                <div class="text-[8px] text-red-500 font-black uppercase tracking-[0.4em] mb-1">Breach Intelligence</div>
                <h3 class="text-xl font-bold text-white tracking-tight">Security Alerts</h3>
            </div>
            <button @click="isAlertsOpen = false" class="h-12 w-12 rounded-full bg-white/5 flex items-center justify-center text-zinc-500 hover:text-white border border-white/10">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        
        <div class="flex-1 overflow-y-auto custom-scrollbar px-2">
            <x-anomaly-feed :anomalies="$recentAlerts" />
        </div>
    </div>
    
    <!-- MOBILE TOGGLES (FABS) -->
    <div class="xl:hidden fixed bottom-28 right-6 z-[2000] flex flex-col gap-4">
        {{-- Fleet FAB --}}
        <button 
            @click="isFleetListOpen = true; isAlertsOpen = false;"
            class="w-16 h-16 rounded-full bg-primary text-black flex items-center justify-center shadow-[0_10px_30px_rgba(255,138,0,0.4)] active:scale-90 transition-transform"
        >
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16m-7 6h7"/></svg>
        </button>

        {{-- Alerts FAB --}}
        <button 
            @click="isAlertsOpen = true; isFleetListOpen = false;"
            class="w-16 h-16 rounded-full bg-red-600 text-white flex items-center justify-center shadow-[0_10px_30px_rgba(220,38,38,0.4)] active:scale-90 transition-transform relative"
        >
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            <template x-if="(recentAlerts || []).filter(a => !a.resolved_at).length > 0">
                <div class="absolute -top-1 -right-1 h-5 w-5 bg-white text-red-600 text-[10px] font-black rounded-full flex items-center justify-center border-2 border-red-600" x-text="(recentAlerts || []).filter(a => !a.resolved_at).length"></div>
            </template>
        </button>
    </div>

    <!-- 4. RIGHT HUD: Anomaly Feed (Desktop) -->
    <div class="hidden xl:flex absolute top-44 right-6 bottom-10 w-80 z-[1000] pointer-events-auto">
        <div class="glass-obsidian rounded-[2rem] border border-white/10 overflow-hidden flex flex-col h-full shadow-2xl">
            <x-anomaly-feed :anomalies="$recentAlerts" />
        </div>
    </div>


    <!-- 5. BOTTOM HUD: Selection Profile (Improved Responsiveness) -->
    <!-- 5. BOTTOM HUD: Selection Profile / Playback HUD (Improved Responsiveness) -->
    <div 
        x-show="selectedVehicle"
        x-ref="selectionHud"
        x-transition:enter="transition ease-out duration-500"
        x-transition:enter-start="opacity-0 translate-y-8"
        class="absolute bottom-24 md:bottom-10 left-4 xl:left-80 right-4 xl:right-96 z-[1001] pointer-events-auto"
        @click.stop
        @mousedown.stop
        @dblclick.stop
    >
        {{-- Selection Profile View --}}
        <template x-if="!isVisualising">
            <div class="glass-obsidian p-4 md:p-8 rounded-[2rem] md:rounded-[2.5rem] border border-primary/20 shadow-[0_0_50px_rgba(255,138,0,0.15)] flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="flex items-center gap-4 md:gap-8 w-full md:w-auto">
                    <div class="h-12 w-12 md:h-14 md:w-14 rounded-xl md:rounded-2xl bg-primary/10 flex items-center justify-center text-primary border border-primary/20 shrink-0">
                        <svg class="w-6 h-6 md:w-8 md:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-[8px] text-zinc-500 uppercase font-bold tracking-[0.3em] mb-1">Target_Unit</div>
                        <h3 class="font-heading text-lg md:text-2xl font-bold text-white tracking-tight truncate" x-text="selectedVehicle?.license_plate"></h3>
                    </div>
                    <div class="hidden lg:block h-10 w-[1px] bg-white/10 mx-4"></div>
                    <div class="flex gap-6 md:gap-8">
                        <div>
                            <div class="text-[8px] text-zinc-500 uppercase font-bold tracking-widest mb-1">Status</div>
                            <div class="flex items-center gap-2">
                                <div class="h-2 w-2 rounded-full" :class="(selectedVehicle?.status === 'active' || selectedVehicle?.status === 'moving') ? 'bg-emerald-500 animate-pulse' : 'bg-zinc-800'"></div>
                                <span class="text-[9px] font-bold text-white uppercase tracking-widest" x-text="selectedVehicle?.status"></span>
                            </div>
                        </div>
                        <div>
                            <div class="text-[8px] text-zinc-500 uppercase font-bold tracking-widest mb-1">Score</div>
                            <div class="text-base md:text-xl font-bold text-white" x-text="selectedVehicle?.driver?.risk_score ? Math.round(selectedVehicle.driver.risk_score) : '—'"></div>
                        </div>
                    </div>
                </div>

                <div class="flex gap-3 w-full md:w-auto">
                    <button @click="visualiseMission()" 
                            class="flex-1 md:flex-none px-6 md:px-8 py-3 md:py-4 bg-white text-black rounded-xl md:rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-zinc-200 transition-all shadow-xl active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                            :disabled="isPlaybackLoading">
                        <template x-if="isPlaybackLoading">
                            <svg class="animate-spin h-3 w-3 text-black" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </template>
                        <span x-text="isPlaybackLoading ? 'Loading_History...' : 'Replay_Mission'"></span>
                    </button>
                    <button @click="selectedVehicle = null; isFollowing = false;" class="p-3 md:p-4 border border-white/10 rounded-xl md:rounded-2xl text-zinc-500 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>
        </template>

        {{-- Playback HUD View --}}
        <template x-if="isVisualising">
            <div class="glass-obsidian p-4 md:p-6 rounded-[2rem] md:rounded-[2.5rem] border border-primary/50 shadow-[0_0_50px_rgba(255,138,0,0.3)] flex flex-col gap-4">
                <div class="flex items-center justify-between gap-6">
                    <div class="flex items-center gap-4">
                        <div class="h-10 w-10 rounded-xl bg-primary text-black flex items-center justify-center shadow-[0_0_20px_rgba(255,138,0,0.4)]">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L4.5 20.29L5.21 21L12 18L18.79 21L19.5 20.29L12 2Z"/></svg>
                        </div>
                        <div>
                            <div class="text-[8px] text-primary font-black uppercase tracking-[0.3em]">Playback_Mode</div>
                            <h3 class="text-base md:text-xl font-bold text-white tracking-tight" x-text="selectedVehicle?.license_plate"></h3>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 md:gap-8">
                        <div class="text-right">
                            <div class="text-[8px] text-zinc-500 uppercase font-bold tracking-widest mb-0.5">Historical Speed</div>
                            <div class="text-sm md:text-lg font-mono font-bold text-white" x-text="Math.round(playbackPath[playbackIndex]?.speed || 0) + ' KM/H'"></div>
                        </div>
                        <div class="text-right">
                            <div class="text-[8px] text-zinc-500 uppercase font-bold tracking-widest mb-0.5">Recorded Time</div>
                            <div class="text-sm md:text-lg font-mono font-bold text-white" x-text="playbackPath[playbackIndex]?.time || '--:--:--'"></div>
                        </div>
                    </div>
                </div>

                {{-- Controls Row --}}
                <div class="flex items-center gap-4 md:gap-6 bg-white/5 p-3 rounded-2xl border border-white/5">
                    {{-- Play/Pause --}}
                    <button @click="togglePlayback()" class="h-10 w-10 md:h-12 md:w-12 bg-white text-black rounded-full flex items-center justify-center hover:scale-105 transition-all shrink-0">
                        <template x-if="!isPlaying">
                            <svg class="h-5 w-5 fill-current" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                        </template>
                        <template x-if="isPlaying">
                            <svg class="h-5 w-5 fill-current" viewBox="0 0 24 24"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>
                        </template>
                    </button>

                    {{-- Progress Bar --}}
                    <div class="flex-1 flex flex-col gap-1.5">
                        <div class="h-1.5 bg-white/10 rounded-full overflow-hidden cursor-pointer relative" @click="scrubPlayback($event)">
                            <div class="absolute inset-0 bg-primary/20"></div>
                            <div class="h-full bg-primary relative transition-all duration-300" :style="'width: ' + ((playbackIndex / playbackPath.length) * 100) + '%'">
                                <div class="absolute right-0 top-1/2 -translate-y-1/2 h-3 w-3 bg-white border-2 border-primary rounded-full shadow-[0_0_10px_#ff8a00]"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Speed Selector --}}
                    <div class="flex items-center gap-2">
                        <template x-for="s in [0.5, 1, 2, 4]" :key="s">
                            <button 
                                @click="playbackSpeedMultiplier = s"
                                class="px-2 md:px-3 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-widest transition-all"
                                :class="playbackSpeedMultiplier === s ? 'bg-primary text-black' : 'bg-white/5 text-zinc-500 hover:text-white'"
                                x-text="s + 'x'"
                            ></button>
                        </template>
                    </div>

                    <div class="h-8 w-[1px] bg-white/10 mx-2 hidden md:block"></div>

                    {{-- Exit --}}
                    <button @click="clearPlayback()" class="px-4 md:px-6 py-2 md:py-3 border border-red-500/30 text-red-500 rounded-xl text-[9px] font-black uppercase tracking-widest hover:bg-red-500 hover:text-white transition-all active:scale-95">
                        Exit_Replay
                    </button>
                </div>
            </div>
        </template>
    </div>

    <div 
        x-show="inspectingAlert" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        class="fixed inset-0 z-[10000] flex items-center justify-center p-4 md:p-6 bg-black/80 backdrop-blur-md"
        @click.self="inspectingAlert = null"
        @keydown.escape.window="inspectingAlert = null"
    >
        <div class="glass-obsidian rounded-[2rem] md:rounded-[3rem] w-full max-w-4xl overflow-hidden border border-white/10 shadow-[0_0_50px_rgba(0,0,0,0.5)] flex flex-col md:flex-row h-[90vh] md:h-[700px]">
            {{-- Left Side: Forensic Map --}}
            <div class="h-1/3 md:h-auto md:w-1/2 relative bg-zinc-900 border-b md:border-b-0 md:border-r border-white/5">
                <div id="forensic-map" class="absolute inset-0" style="background: #09090b;"></div>
                <div class="absolute top-4 md:top-8 left-4 md:left-8 z-[1001] px-3 md:px-4 py-1.5 md:py-2 bg-red-500 text-white text-[8px] md:text-[10px] font-bold uppercase tracking-widest rounded-lg shadow-2xl">
                    Breach Location
                </div>
            </div>

            {{-- Right Side: Data Deep-Dive --}}
            <div class="flex-1 md:w-1/2 p-6 md:p-12 flex flex-col gap-6 md:gap-8 bg-obsidian-950/50 overflow-y-auto custom-scrollbar">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="text-[8px] md:text-[10px] text-red-500 font-bold uppercase tracking-[0.2em] mb-1 md:mb-2">Forensic Analysis</div>
                        <h2 class="text-xl md:text-3xl font-bold text-white tracking-tight" x-text="formatType(inspectingAlert?.type, inspectingAlert?.details)"></h2>
                    </div>
                    <button @click="inspectingAlert = null" class="p-2 md:p-3 text-zinc-500 hover:text-white transition-colors">
                        <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="grid grid-cols-2 gap-4 md:gap-6">
                    <div class="p-4 md:p-5 rounded-2xl bg-white/5 border border-white/5">
                        <div class="text-[8px] md:text-[9px] text-zinc-500 uppercase font-bold tracking-widest mb-1 md:mb-2">Driver Impact</div>
                        <div class="text-lg md:text-2xl font-bold text-red-500" x-text="'-' + inspectingAlert?.impact_score + ' PTS'"></div>
                    </div>
                    <div class="p-4 md:p-5 rounded-2xl bg-white/5 border border-white/5">
                        <div class="text-[8px] md:text-[9px] text-zinc-500 uppercase font-bold tracking-widest mb-1 md:mb-2">Occurred At</div>
                        <div class="text-[10px] md:text-sm font-mono text-white" x-text="inspectingAlert ? new Date(inspectingAlert.occurred_at).toLocaleTimeString() : ''"></div>
                    </div>
                </div>

                <div class="space-y-4 md:space-y-6">
                    <div class="flex items-center gap-3 md:gap-4 p-4 md:p-5 rounded-2xl bg-white/[0.02] border border-white/5">
                        <div class="h-10 w-10 md:h-12 md:w-12 rounded-xl bg-primary/10 flex items-center justify-center text-primary shrink-0">
                            <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <div>
                            <div class="text-[8px] md:text-[9px] text-zinc-500 uppercase font-bold tracking-widest">Involved Operator</div>
                            <div class="text-sm md:text-white font-bold" x-text="inspectingAlert?.driver?.name"></div>
                        </div>
                    </div>

                    <div class="p-5 md:p-6 rounded-2xl border border-dashed border-white/10 bg-white/[0.01]">
                        <div class="text-[8px] md:text-[9px] text-zinc-500 uppercase font-bold tracking-widest mb-2 md:mb-3">Telemetry Snapshot</div>
                        <div class="space-y-2">
                            <div class="flex justify-between text-[10px] md:text-xs">
                                <span class="text-zinc-500 font-medium">Recorded Speed</span>
                                <span class="text-white font-bold" x-text="(inspectingAlert?.details?.speed || '0') + ' KM/H'"></span>
                            </div>
                            <div class="flex justify-between text-[10px] md:text-xs">
                                <span class="text-zinc-500 font-medium">Violation Type</span>
                                <span class="text-white font-bold italic" x-text="inspectingAlert?.details?.breach_type || 'Point Breach'"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-5 md:p-6 rounded-2xl border border-white/5 bg-white/[0.01] space-y-3 md:space-y-4">
                    <div class="text-[8px] md:text-[9px] text-zinc-500 uppercase font-bold tracking-widest">Resolution Audit Note</div>
                    <textarea 
                        x-model="resolutionNote" 
                        placeholder="Why is this case being closed?" 
                        class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-[10px] md:text-xs text-white placeholder-zinc-700 outline-none focus:border-primary/50 transition-all h-20 md:h-24 resize-none"
                    ></textarea>
                </div>

                <div class="mt-auto flex flex-col sm:flex-row gap-3 md:gap-4 pt-4 md:pt-6">
                    <template x-if="inspectingAlert?.driver?.phone_number">
                        <a :href="'tel:' + inspectingAlert.driver.phone_number" class="flex-1 py-4 md:py-5 bg-emerald-500 text-black text-center rounded-full text-[9px] md:text-[10px] font-bold uppercase tracking-widest hover:bg-emerald-400 transition-all shadow-lg shadow-emerald-500/20">
                            Initiate Contact
                        </a>
                    </template>
                    <button 
                        @click="dismissAlert(inspectingAlert.id, resolutionNote); inspectingAlert = null; resolutionNote = '';" 
                        class="flex-1 py-4 md:py-5 border border-white/10 rounded-full text-[9px] md:text-[10px] font-bold uppercase tracking-widest text-zinc-400 hover:text-white transition-all hover:bg-white/5"
                    >
                        Finalize & Resolve
                    </button>
                </div>
            </div>
        </div>
    </div>

<style>
    .leaflet-tooltip-pane { z-index: 1000 !important; }
    .fleet-marker-label {
        background: rgba(9, 9, 11, 0.9) !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        color: white !important;
        border-radius: 4px !important;
        padding: 2px 6px !important;
        font-size: 9px !important;
        font-weight: 800 !important;
        letter-spacing: 0.05em !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.5) !important;
    }
    .leaflet-tooltip-top:before { border-top-color: rgba(9, 9, 11, 0.9) !important; }
    
    .leaflet-popup-content-wrapper, .leaflet-popup-tip {
        background: transparent !important;
        box-shadow: none !important;
        padding: 0 !important;
    }
    .leaflet-container {
        background: #09090b !important;
    }
    .custom-vengo-icon {
        /* No transition by default to prevent fly-in from top-left on load */
    }
    .markers-ready .custom-vengo-icon {
        transition: transform 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .vehicle-rotation {
        transition: transform 0.5s ease-in-out;
    }
    .leaflet-popup-content { margin: 0 !important; }
    .glass-tooltip {
        background: rgba(0, 0, 0, 0.8) !important;
        backdrop-filter: blur(8px) !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        color: white !important;
        border-radius: 8px !important;
        padding: 4px 8px !important;
        font-size: 9px !important;
        font-weight: bold !important;
        text-transform: uppercase !important;
        letter-spacing: 0.1em !important;
    }

    @keyframes fleetco-fade-in {
        from { opacity: 0; transform: translateY(4px) scale(0.95); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }
    .animate-fleetco-fade-in {
        animation: fleetco-fade-in 0.4s cubic-bezier(0, 0, 0.2, 1) forwards;
    }
</style>

@push('scripts')
<script>
    function fleetDashboard() {
        return {
            map: null,
            markers: {},
            paths: {}, // Store live paths
            selectedVehicle: null,
            inspectingAlert: null,
            routePolylines: {}, // Store route polylines
            forensicMap: null,
            forensicMarker: null,
            searchQuery: '',
            statusFilter: 'all',
            resolutionNote: '',
            vehicles: @json($vehicles),
            geofences: @json($geofences),
            viewportCoords: '19.0760° N, 72.8777° E',
            haversineDistance: 0,
            systemStatus: 'Connecting...',
            lastPingTime: 'None',
            recentAlerts: @json($recentAlerts),
            isFleetListOpen: false,
            isMobile: window.innerWidth < 768,

            async fetchVehicles() {
                if (this.isVisualising) return;
                try {
                    const response = await fetch('/api/vehicles');
                    const data = await response.json();
                    
                    // Maintain selection reference if it exists
                    const currentSelectedId = this.selectedVehicle?.id;

                    this.vehicles = data.map(v => {
                        // Ensure correct coordinate mapping for Leaflet [lat, lng]
                        if (v.latest_telematics && v.latest_telematics.location) {
                            // No change needed to data, just ensuring reactivity
                        }
                        return v;
                    });

                    // Re-sync selectedVehicle if it was updated
                    if (currentSelectedId) {
                        this.selectedVehicle = this.vehicles.find(v => v.id === currentSelectedId);
                    }

                    this.updateMarkers();
                } catch (error) {
                    console.error('Polling error:', error);
                }
            },

            get filteredVehicles() {
                return this.vehicles.filter(vehicle => {
                    const matchesSearch = vehicle.license_plate.toLowerCase().includes(this.searchQuery.toLowerCase()) || 
                                          (vehicle.current_driver?.name || '').toLowerCase().includes(this.searchQuery.toLowerCase());
                    
                    const matchesStatus = this.statusFilter === 'all' || 
                                         (this.statusFilter === 'moving' && vehicle.status === 'moving') ||
                                         (this.statusFilter === 'alerting' && vehicle.status === 'alerting');

                    return matchesSearch && matchesStatus;
                });
            },

            reachedCount(waypoints) {
                if (!waypoints) return 0;
                return waypoints.filter(w => w.reached_at).length;
            },
            
            // Playback State
            playbackPolyline: null,
            playedPolyline: null, 
            playbackMarker: null,
            playbackAlertMarkers: [],
            isVisualising: false,
            isPlaybackLoading: false, // New: Loading state
            isPlaying: false,
            playbackIndex: 0,
            playbackPath: [],
            playbackSpeedMultiplier: 1,
            playbackTimer: null,
            isFollowing: false,
            isFleetListOpen: false,
            isAlertsOpen: false,

            init() {
                this.$nextTick(() => {
                    this.initMap();
                    this.startPolling();
                    
                    // Request notification permission
                    if ("Notification" in window && Notification.permission === "default") {
                        Notification.requestPermission();
                    }

                    // Enable smooth marker transitions AFTER initial snap-to-position
                    setTimeout(() => {
                        document.getElementById('map').classList.add('markers-ready');
                    }, 1500);

                    // REAL-TIME: Listen for Vehicle Location Updates via Reverb
                    if (window.Echo) {
                        window.Echo.connector.pusher.connection.bind('connected', () => {
                            this.systemStatus = 'Online';
                        });
                        window.Echo.connector.pusher.connection.bind('disconnected', () => {
                            this.systemStatus = 'Offline (Reconnecting...)';
                        });

                        window.Echo.channel('fleet-updates')
                            .listen('VehicleLocationUpdated', (e) => {
                                console.log('Live Telemetry Received:', e);
                                this.handleRealTimeUpdate(e);
                                
                                // Show a quick toast or pulse
                                this.lastPingTime = new Date().toLocaleTimeString();
                            });

                        window.Echo.channel('fleet-alerts')
                            .listen('AlertGenerated', (e) => {
                                console.log('New Alert Received:', e);
                                
                                // Add to top of list with reactivity
                                this.recentAlerts = [e.alert, ...this.recentAlerts.slice(0, 4)];
                                
                                // Show a visual ping/notification
                                if ("Notification" in window && Notification.permission === "granted") {
                                    new Notification(`SECURITY BREACH: ${this.formatType(e.alert.type, e.alert.details)}`, {
                                        body: `Vehicle ${e.alert.vehicle?.license_plate || 'TEST-001'} - Operator: ${e.alert.driver?.name}`
                                    });
                                }
                            });
                    }

                    window.addEventListener('inspect-alert', (e) => {
                        this.openInspectModal(e.detail);
                    });

                    // Listen for new alerts to show notifications
                    window.addEventListener('new-alert-detected', (e) => {
                        const alert = e.detail;
                        if ("Notification" in window && Notification.permission === "granted") {
                            new Notification(`FLEET ALERT: ${this.formatType(alert.type, alert.details)}`, {
                                body: `Vehicle ${alert.vehicle?.license_plate || 'Unknown'} - ${alert.driver?.name || 'Unknown'}`,
                                icon: '/favicon.ico'
                            }).onclick = () => {
                                window.focus();
                                this.selectVehicle(alert.vehicle);
                                this.openInspectModal(alert);
                            };
                        }
                    });

                    // Check for URL parameters (Inspect button support)
                    const urlParams = new URLSearchParams(window.location.search);
                    const vehicleId = urlParams.get('vehicle');
                    if (vehicleId) {
                        setTimeout(() => {
                            const vehicle = this.vehicles.find(v => v.id == vehicleId);
                            if (vehicle) {
                                this.selectVehicle(vehicle);
                            }
                        }, 1000);
                    }
                });
            },

            initMap() {
                this.map = L.map('map', {
                    zoomControl: false,
                    attributionControl: false,
                    fadeAnimation: true,
                }).setView([30.9010, 75.8573], 12);

                // Prevent HUD clicks from interacting with map
                this.$nextTick(() => {
                    if (this.$refs.selectionHud) {
                        L.DomEvent.disableClickPropagation(this.$refs.selectionHud);
                    }
                });

                L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
                    maxZoom: 20,
                    attribution: ''
                }).addTo(this.map);

                this.map.on('move', () => {
                    const center = this.map.getCenter();
                    this.viewportCoords = `${center.lat.toFixed(4)}° N, ${center.lng.toFixed(4)}° E`;
                });

                // Close selection on map click, but NOT during playback
                this.map.on('click', () => {
                    if (this.isVisualising) return;
                    this.selectedVehicle = null;
                    this.isFollowing = false;
                    this.map.flyTo([30.9010, 75.8573], 12);
                });

                this.updateMarkers();
                this.renderGeofences();
            },

            renderGeofences() {
                this.geofences.forEach(gf => {
                    let coords = [];
                    
                    // Decode logic based on DB storage
                    if (typeof gf.area === 'string') {
                        coords = JSON.parse(gf.area);
                    } else if (gf.area && gf.area.coordinates) {
                        // PostGIS format [lng, lat] to Leaflet [lat, lng]
                        coords = gf.area.coordinates[0].map(p => [p[1], p[0]]);
                    }

                    if (coords.length > 0) {
                        const color = gf.type === 'restricted' ? '#f43f5e' : (gf.type === 'depot' ? '#10b981' : '#3b82f6');
                        L.polygon(coords, {
                            color: color,
                            weight: 2,
                            fillColor: color,
                            fillOpacity: 0.05,
                            dashArray: gf.type === 'restricted' ? '5, 5' : null
                        }).addTo(this.map).bindTooltip(gf.name, { permanent: false, direction: 'center', className: 'glass-tooltip' });
                    }
                });
            },

            updateMarkers() {
                this.vehicles.forEach(vehicle => {
                    if (vehicle.latest_telematics) {
                        const log = vehicle.latest_telematics;
                        let coords = null;
                        
                        if (log.location && log.location.coordinates) {
                            coords = [log.location.coordinates[1], log.location.coordinates[0]];
                        } else if (typeof log.location === 'string' && log.location.includes('POINT')) {
                            const match = log.location.match(/POINT\((.+) (.+)\)/);
                            if (match) coords = [parseFloat(match[2]), parseFloat(match[1])];
                        }

                        if (!coords || isNaN(coords[0]) || isNaN(coords[1])) return;

                        // 1. Handle the Marker (The Vehicle Dot)
                        if (this.markers[vehicle.id]) {
                            this.markers[vehicle.id].setLatLng(coords);
                            
                            // Update tooltip with progress
                            this.markers[vehicle.id].setTooltipContent(
                                `<div>${vehicle.license_plate}</div>` + 
                                (vehicle.active_route ? `<div style="color: #ff8a00; font-size: 8px; margin-top: 2px;">ROUTE: ${this.reachedCount(vehicle.active_route.waypoints)}/${vehicle.active_route.waypoints.length}</div>` : '')
                            );

                            // Update rotation if heading exists
                            if (log.heading) {
                                const iconElement = this.markers[vehicle.id].getElement();
                                if (iconElement) {
                                    const inner = iconElement.querySelector('.vehicle-rotation');
                                    if (inner) inner.style.transform = `rotate(${log.heading}deg)`;
                                }
                            }
                        } else {
                            const initialHeading = log.heading || 0;
                            const icon = L.divIcon({
                                className: 'custom-vengo-icon',
                                html: `
                                    <div class="relative flex items-center justify-center animate-fleetco-fade-in">
                                        <!-- Directional Shadow -->
                                        <div class="absolute h-12 w-12 rounded-full bg-primary/20 blur-md"></div>
                                        
                                        <!-- Top-Down Vehicle Icon -->
                                        <div class="vehicle-rotation" style="transform: rotate(${initialHeading}deg); transition: transform 0.8s cubic-bezier(0.4, 0, 0.2, 1);">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M12 2L4.5 20.29L5.21 21L12 18L18.79 21L19.5 20.29L12 2Z" fill="#ff8a00" stroke="white" stroke-width="1.5" stroke-linejoin="round"/>
                                            </svg>
                                        </div>
                                    </div>
                                `,
                                iconSize: [40, 40],
                                iconAnchor: [20, 20]
                            });

                            this.markers[vehicle.id] = L.marker(coords, { icon, zIndexOffset: 1000 })
                                .addTo(this.map)
                                .bindTooltip(
                                    `<div>${vehicle.license_plate}</div>` + 
                                    (vehicle.active_route ? `<div style="color: #ff8a00; font-size: 8px; margin-top: 2px;">ROUTE: ${this.reachedCount(vehicle.active_route.waypoints)}/${vehicle.active_route.waypoints.length}</div>` : ''), 
                                    {
                                        permanent: true,
                                        direction: 'top',
                                        className: 'fleet-marker-label',
                                        offset: [0, -10]
                                    }
                                );
                        }

                        // 3. Handle the Route Polyline (The Assigned Route)
                        if (vehicle.active_route) {
                            const routeLatLngs = vehicle.active_route.waypoints.map(wp => [wp.lat, wp.lng]);
                            if (this.routePolylines[vehicle.id]) {
                                this.routePolylines[vehicle.id].setLatLngs(routeLatLngs);
                            } else {
                                this.routePolylines[vehicle.id] = L.polyline(routeLatLngs, {
                                    color: '#ff8a00',
                                    weight: 2,
                                    opacity: 0.4,
                                    dashArray: '5, 5'
                                }).addTo(this.map);
                            }
                        } else if (this.routePolylines[vehicle.id]) {
                            this.map.removeLayer(this.routePolylines[vehicle.id]);
                            delete this.routePolylines[vehicle.id];
                        }

                        // 2. Handle the Path (The Trail)
                        if (!this.paths[vehicle.id]) {
                            this.paths[vehicle.id] = L.polyline([coords], {
                                color: '#ff8a00',
                                weight: 3,
                                opacity: 0.6,
                                smoothFactor: 1
                            }).addTo(this.map);
                        } else {
                            const path = this.paths[vehicle.id];
                            const lastLatLng = path.getLatLngs()[path.getLatLngs().length - 1];
                            
                            if (lastLatLng) {
                                // Calculate distance from last point (in meters)
                                const dist = this.map.distance(lastLatLng, L.latLng(coords));
                                
                                // Filter out GPS jitter:
                                // - Skip if distance is > 500m in one ping (likely bad GPS reading)
                                // - Skip if moved less than 5 meters (GPS noise)
                                if (dist > 500) {
                                    // Looks like a GPS jump/glitch — skip this point
                                    console.warn('GPS jitter detected, skipping point. Distance:', dist);
                                } else if (dist > 5) {
                                    path.addLatLng(coords);
                                }
                            }
                        }

                        if (this.isFollowing && this.selectedVehicle?.id === vehicle.id) {
                            this.map.panTo(coords, { animate: true, duration: 4.0 });
                        }
                    }
                });
            },

            handleRealTimeUpdate(data) {
                const vehicleIndex = this.vehicles.findIndex(v => v.id === data.vehicleId);
                if (vehicleIndex !== -1) {
                    this.vehicles[vehicleIndex].status = data.status;
                    this.vehicles[vehicleIndex].latest_telematics = {
                        location: {
                            coordinates: [data.lng, data.lat]
                        },
                        heading: data.heading,
                        speed: data.speed
                    };

                    this.updateMarkers();

                    if (this.selectedVehicle && this.selectedVehicle.id === data.vehicleId) {
                        this.selectedVehicle = { ...this.vehicles[vehicleIndex] };
                        this.calculateHaversine([data.lat, data.lng]);
                    }
                }
            },

            selectVehicle(vehicle) {
                this.selectedVehicle = vehicle;
                this.isFollowing = true;
                if (vehicle.latest_telematics) {
                    const log = vehicle.latest_telematics;
                    const coords = [log.location.coordinates[1], log.location.coordinates[0]];
                    
                    this.map.flyTo(coords, 14, { duration: 2.0, easeLinearity: 0.1 });
                    this.calculateHaversine(coords);
                }
            },

            calculateHaversine(coords) {
                const center = [30.9010, 75.8573]; // Home Depot (Ludhiana)
                const R = 6371;
                const dLat = (coords[0] - center[0]) * Math.PI / 180;
                const dLon = (coords[1] - center[1]) * Math.PI / 180;
                const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                          Math.cos(center[0] * Math.PI / 180) * Math.cos(coords[0] * Math.PI / 180) * 
                          Math.sin(dLon/2) * Math.sin(dLon/2);
                const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
                this.haversineDistance = (R * c).toFixed(2);
            },

            fitFleetBounds() {
                const group = new L.featureGroup(Object.values(this.markers));
                if (group.getLayers().length > 0) {
                    this.map.fitBounds(group.getBounds().pad(0.2));
                }
            },

            async startPolling() {
                let firstLoad = true;
                setInterval(async () => {
                    if (this.isVisualising) return;
                    
                    try {
                        const response = await fetch('/api/vehicles');
                        this.vehicles = await response.json();
                        this.updateMarkers();
                        
                        if (firstLoad && this.vehicles.length > 0) {
                            this.fitFleetBounds();
                            firstLoad = false;
                        }

                        if (this.selectedVehicle) {
                            const updated = this.vehicles.find(v => v.id === this.selectedVehicle.id);
                            if (updated) {
                                this.selectedVehicle = updated;
                                if (updated.latest_telematics) {
                                    this.calculateHaversine([updated.latest_telematics.location.coordinates[1], updated.latest_telematics.location.coordinates[0]]);
                                }
                            }
                        }
                    } catch (error) {
                        console.error('Polling error:', error);
                    }
                }, 15000);
            },

            async visualiseMission() {
                if (!this.selectedVehicle) return;
                this.isPlaybackLoading = true;
                this.isVisualising = false; // Ensure HUD is hidden during load
                this.clearPlayback();

                try {
                    const response = await fetch(`/api/vehicles/${this.selectedVehicle.id}/playback`);
                    if (!response.ok) throw new Error('API Error');
                    const data = await response.json();
                    
                    this.playbackPath = (data.path || []).filter(p => p && p.lat !== null && p.lng !== null).map(p => ({
                        ...p,
                        lat: parseFloat(p.lat),
                        lng: parseFloat(p.lng)
                    })).filter(p => !isNaN(p.lat) && !isNaN(p.lng));

                    if (this.playbackPath.length < 2) {
                        alert("No movement recorded for this unit in the last 24 hours.");
                        this.isPlaybackLoading = false;
                        return;
                    }

                    this.isVisualising = true;
                    this.isPlaybackLoading = false;

                    // Use a direct map reference to avoid Alpine Proxy issues
                    const map = this.map;
                    if (!map) {
                        console.error("Map instance not found during playback initialization.");
                        return;
                    }

                    // 1. Draw the "Ghost" Path (Full Route)
                    const ghostPoints = this.playbackPath.map(p => L.latLng(p.lat, p.lng));
                    this.playbackPolyline = L.polyline(ghostPoints, {
                        color: '#ff8a00',
                        weight: 2,
                        opacity: 0.2,
                        dashArray: '5, 5'
                    }).addTo(map);

                    // 2. Prepare the "Played" Path (The Trail)
                    this.playedPolyline = L.polyline([], {
                        color: '#ff8a00',
                        weight: 3,
                        opacity: 0.8,
                        lineJoin: 'round'
                    }).addTo(map);

                    // 3. Render Incident Markers
                    (data.alerts || []).forEach(alert => {
                        const aLat = parseFloat(alert.lat);
                        const aLng = parseFloat(alert.lng);
                        if (!isNaN(aLat) && !isNaN(aLng)) {
                            const marker = L.circleMarker([aLat, aLng], {
                                radius: 8,
                                color: '#ef4444',
                                fillColor: '#ef4444',
                                fillOpacity: 0.6,
                                weight: 2
                            }).addTo(map).bindTooltip(`${alert.type.toUpperCase()} - ${alert.time}`, { className: 'glass-tooltip' });
                            this.playbackAlertMarkers.push(marker);
                        }
                    });

                    // 4. Fit Bounds
                    const bounds = L.latLngBounds(ghostPoints);
                    if (bounds.isValid()) {
                        map.fitBounds(bounds, { padding: [100, 100], duration: 1.5 });
                    }

                    // 5. Start Playback
                    this.playbackIndex = 0;
                    this.isPlaying = true;
                    this.animatePlayback();
                } catch (error) {
                    console.error('Playback initialization error:', error);
                    alert("System failed to initialize playback. Please try again.");
                    this.isVisualising = false;
                    this.isPlaying = false;
                    this.isPlaybackLoading = false;
                }
            },

            animatePlayback() {
                if (!this.isVisualising || !this.isPlaying) return;

                if (this.playbackIndex >= this.playbackPath.length) {
                    this.isPlaying = false;
                    return;
                }

                const point = this.playbackPath[this.playbackIndex];
                if (!point) return;

                const coords = [point.lat, point.lng];
                const map = this.map;
                if (!map) return;

                // Update Marker
                if (!this.playbackMarker) {
                    const icon = L.divIcon({
                        className: 'playback-icon',
                        html: `
                            <div class="relative flex items-center justify-center">
                                <div class="absolute h-12 w-12 rounded-full bg-primary opacity-20 animate-pulse"></div>
                                <div class="vehicle-rotation transition-all duration-300">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path d="M12 2L4.5 20.29L5.21 21L12 18L18.79 21L19.5 20.29L12 2Z" fill="#ff8a00" stroke="white" stroke-width="1.5" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                            </div>
                        `,
                        iconSize: [40, 40],
                        iconAnchor: [20, 20]
                    });
                    this.playbackMarker = L.marker(coords, { zIndexOffset: 2000, icon: icon }).addTo(map);
                } else {
                    this.playbackMarker.setLatLng(coords);
                    const rotation = this.playbackMarker.getElement()?.querySelector('.vehicle-rotation');
                    if (rotation) rotation.style.transform = `rotate(${point.heading || 0}deg)`;
                }

                // Update Trail
                if (this.playedPolyline) {
                    this.playedPolyline.addLatLng(coords);
                }
                
                // Pan map smoothly
                map.panTo(coords, { animate: true, duration: 0.1 });

                this.playbackIndex++;
                
                // Dynamic speed based on multiplier
                const baseDelay = 200; 
                const delay = baseDelay / this.playbackSpeedMultiplier;
                
                this.playbackTimer = setTimeout(() => this.animatePlayback(), delay);
            },

            togglePlayback() {
                this.isPlaying = !this.isPlaying;
                if (this.isPlaying) {
                    if (this.playbackIndex >= this.playbackPath.length) {
                        this.playbackIndex = 0;
                        if (this.playedPolyline) this.playedPolyline.setLatLngs([]);
                    }
                    this.animatePlayback();
                } else {
                    if (this.playbackTimer) clearTimeout(this.playbackTimer);
                }
            },

            scrubPlayback(event) {
                if (!this.playbackPath.length) return;

                const rect = event.currentTarget.getBoundingClientRect();
                const ratio = (event.clientX - rect.left) / rect.width;
                this.playbackIndex = Math.floor(ratio * this.playbackPath.length);
                if (this.playbackIndex >= this.playbackPath.length) this.playbackIndex = this.playbackPath.length - 1;
                
                // Update trail immediately
                const points = this.playbackPath.slice(0, this.playbackIndex + 1);
                if (this.playedPolyline) {
                    this.playedPolyline.setLatLngs(points.map(p => [p.lat, p.lng]));
                }
                
                if (this.playbackPath[this.playbackIndex]) {
                    const coords = [this.playbackPath[this.playbackIndex].lat, this.playbackPath[this.playbackIndex].lng];
                    if (this.playbackMarker) this.playbackMarker.setLatLng(coords);
                    if (this.map) this.map.panTo(coords);
                }
            },

            clearPlayback() {
                this.isVisualising = false;
                this.isPlaying = false;
                if (this.playbackTimer) clearTimeout(this.playbackTimer);

                const map = this.map;
                if (map) {
                    if (this.playbackPolyline) map.removeLayer(this.playbackPolyline);
                    if (this.playedPolyline) map.removeLayer(this.playedPolyline);
                    if (this.playbackMarker) map.removeLayer(this.playbackMarker);
                    
                    if (this.playbackAlertMarkers) {
                        this.playbackAlertMarkers.forEach(m => map.removeLayer(m));
                    }
                    
                    map.flyTo([30.9010, 75.8573], 12);
                }
                
                this.playbackAlertMarkers = [];
                this.playbackPolyline = null;
                this.playedPolyline = null;
                this.playbackMarker = null;
                this.playbackPath = [];
                this.playbackIndex = 0;
            },

            openInspectModal(alert) {
                this.inspectingAlert = alert;
                // Force a small delay to ensure modal is fully rendered
                setTimeout(() => {
                    this.initForensicMap(alert);
                }, 300);
            },

            formatType(type, details) {
                if (!type) return 'Incident';
                const label = type.replace(/_/g, ' ').toUpperCase();
                if (details && details.breach_type) {
                    return `${label}: ${details.breach_type}`;
                }
                return label;
            },

            dismissAlert(id, note = null) {
                fetch(`/api/alerts/${id}/resolve`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ note: note })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        // Refresh alerts (using global event)
                        window.dispatchEvent(new CustomEvent('refresh-alerts'));
                    }
                });
            },

            initForensicMap(alert) {
                if (this.forensicMap) {
                    this.forensicMap.remove();
                }

                setTimeout(() => {
                    this.forensicMap = L.map('forensic-map', {
                        zoomControl: false,
                        attributionControl: false
                    });

                    L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png').addTo(this.forensicMap);

                    // Calibration: Support both telematics_log and telematicsLog naming
                    const log = alert.telematics_log || alert.telematicsLog;

                    if (log && log.location) {
                        let lat, lng;

                        // HANDLE SQLITE STRING FORMAT: "POINT(lng lat)"
                        if (typeof log.location === 'string' && log.location.includes('POINT')) {
                            const match = log.location.match(/POINT\((.+) (.+)\)/);
                            if (match) {
                                lng = parseFloat(match[1]);
                                lat = parseFloat(match[2]);
                            }
                        } 
                        // HANDLE POSTGRES OBJECT FORMAT
                        else if (log.location.coordinates) {
                            lng = log.location.coordinates[0];
                            lat = log.location.coordinates[1];
                        }

                        if (lat && lng) {
                            const coords = [lat, lng];
                            this.forensicMap.setView(coords, 16);
                            
                            // High-visibility 'Dossier' Target
                            L.circle(coords, {
                                radius: 40,
                                color: '#ff4444',
                                fillColor: '#ff4444',
                                fillOpacity: 0.1,
                                weight: 1
                            }).addTo(this.forensicMap);

                            L.circleMarker(coords, {
                                radius: 6,
                                color: '#ffffff',
                                fillColor: '#ff4444',
                                fillOpacity: 1,
                                weight: 2
                            }).addTo(this.forensicMap);
                        } else {
                            this.forensicMap.setView([31.3831, 75.3857], 12);
                        }
                    } else {
                        // Fallback: Show Kapurthala, Punjab area if log is missing
                        this.forensicMap.setView([31.3831, 75.3857], 12);
                    }
                    
                    this.forensicMap.invalidateSize();
                }, 400);
            }
        }
    }
</script>
@endpush
@endsection
