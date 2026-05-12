@extends('driver.layouts.app')

@section('content')
@php
    $currentLog = \App\Models\DutyLog::where('driver_id', auth()->id())->whereNull('ended_at')->first();
    $logStatus = $currentLog ? $currentLog->status : 'off_duty';
    $isOnDuty = $logStatus === 'on_duty';
@endphp

<div class="flex flex-col gap-6 w-full max-w-7xl mx-auto">
    <!-- 1. TOP STATS MATRIX (Exact Admin Style) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 w-full">
        {{-- Safety Score --}}
        <div class="fleetco-card p-5 rounded-2xl flex flex-col gap-1 relative overflow-hidden">
            <div class="text-xs text-zinc-500 font-medium tracking-tight flex items-center gap-2">
                <div class="h-1.5 w-1.5 bg-emerald-500 rounded-full animate-pulse"></div>
                Safety Score
            </div>
            <div class="font-heading text-4xl font-bold text-white tracking-tight">{{ number_format($riskScore, 2) }}</div>
        </div>

        {{-- Shift Time --}}
        <div class="fleetco-card p-5 rounded-2xl flex flex-col gap-1 relative overflow-hidden">
            <div class="text-xs text-zinc-500 font-medium tracking-tight">Shift Chrono</div>
            <div class="font-heading text-4xl font-bold text-white tracking-tight" id="shift-timer-display">00:00:00</div>
        </div>

        {{-- Total Distance --}}
        <div class="fleetco-card p-5 rounded-2xl flex flex-col gap-1 relative overflow-hidden">
            <div class="text-xs text-zinc-500 font-medium tracking-tight">Trip Distance</div>
            <div class="flex items-baseline gap-1">
                <div class="font-heading text-4xl font-bold text-white tracking-tight">{{ number_format($distanceKM, 1) }}</div>
                <span class="text-xs font-bold text-zinc-600 uppercase">km</span>
            </div>
        </div>

        {{-- Shift Incidents --}}
        <div class="fleetco-card p-5 rounded-2xl flex flex-col gap-1 relative overflow-hidden border-rose-500/10">
            <div class="text-xs text-zinc-500 font-medium tracking-tight flex items-center gap-2">
                <div class="h-1.5 w-1.5 bg-rose-500 rounded-full"></div>
                Active Alerts
            </div>
            <div class="font-heading text-4xl font-bold text-white tracking-tight">{{ $incidents }}</div>
        </div>
    </div>

    <!-- 2. MAIN COMMAND DECK -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 w-full items-start">
        
        <!-- LEFT: Route Hub (70%) -->
        <div class="lg:col-span-8 flex flex-col gap-6 h-full">
            <div class="glass-obsidian rounded-[2rem] border border-white/10 overflow-hidden shadow-2xl flex flex-col min-h-[500px]" x-data="routeHandler({{ $activeRoute ? json_encode($activeRoute->waypoints) : '[]' }})">
                {{-- Header --}}
                <div class="py-5 px-8 border-b border-white/5 bg-white/[0.02] flex justify-between items-center shrink-0">
                    <div>
                        <span class="text-[10px] text-orange-500 font-bold uppercase tracking-[0.3em] mb-0.5 block">Logistics Queue</span>
                        <h2 class="text-lg font-bold text-white tracking-tight font-heading uppercase">Route Intelligence</h2>
                    </div>
                    @if($activeRoute)
                        <span class="text-[10px] px-3 py-1 rounded-full uppercase font-bold bg-orange-500/10 text-orange-500 border border-orange-500/20 animate-pulse">● Active Stream</span>
                    @else
                        <div class="px-4 py-1.5 rounded-lg bg-white/5 border border-white/10 text-[10px] font-bold text-zinc-600 uppercase tracking-widest">Protocol: Standby</div>
                    @endif
                </div>

                {{-- Content Area --}}
                <div class="p-8 flex-1 flex flex-col">
                    @if($activeRoute)
                        <div class="flex flex-col gap-6 flex-1">
                            <div class="w-full h-[350px] rounded-2xl overflow-hidden border border-white/5 bg-zinc-900 relative shadow-inner shrink-0">
                                <div id="driver-route-map" class="absolute inset-0"></div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @if(empty($activeRoute->waypoints))
                                    <div class="col-span-full p-8 rounded-2xl border border-dashed border-white/10 bg-white/[0.01] flex flex-col items-center justify-center text-center">
                                        <svg class="w-8 h-8 text-zinc-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                        <span class="text-[10px] text-zinc-500 font-bold uppercase tracking-[0.2em]">Route Active, No Waypoints Assigned</span>
                                    </div>
                                @endif
                                <template x-for="(wp, index) in waypoints" :key="index">
                                    <div class="flex items-center justify-between p-4 rounded-xl border border-white/5 bg-white/[0.01] transition-all"
                                         :class="isNext(wp) ? 'border-orange-500/40 bg-orange-500/5 shadow-lg' : ''">
                                        <div class="flex items-center gap-4">
                                            <div class="h-8 w-8 rounded-lg flex items-center justify-center text-[11px] font-bold"
                                                 :class="wp.reached_at ? 'bg-emerald-500 text-black' : (isNext(wp) ? 'bg-orange-500 text-black' : 'bg-zinc-800 text-zinc-600')">
                                                <template x-if="wp.reached_at">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                </template>
                                                <template x-if="!wp.reached_at">
                                                    <span x-text="index + 1"></span>
                                                </template>
                                            </div>
                                            <div class="text-sm font-bold text-white font-heading" x-text="wp.label"></div>
                                        </div>
                                        <template x-if="isNext(wp)">
                                            <button @click="markReached({{ $activeRoute->id }}, wp.order)" 
                                                    class="px-5 py-2 bg-emerald-500 text-black text-[10px] font-black uppercase rounded-lg shadow-xl active:scale-95 transition-all">
                                                Acknowledge
                                            </button>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>
                    @else
                        <div class="flex-1 flex flex-col items-center justify-center py-12 opacity-60">
                            <div class="relative mb-8">
                                <div class="absolute inset-0 bg-orange-500/10 blur-3xl rounded-full"></div>
                                <div class="relative w-20 h-20 rounded-3xl bg-white/5 border border-white/10 flex items-center justify-center text-zinc-700">
                                    <svg class="w-10 h-10 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                                </div>
                            </div>
                            <h4 class="text-[11px] font-black uppercase tracking-[0.4em] text-zinc-500 mb-2">System Standby</h4>
                            <p class="text-[10px] text-zinc-600 font-medium text-center max-w-[240px] leading-relaxed uppercase tracking-widest">No logistics mission assigned. Communications link is verified and active.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- RIGHT: Side Controls (30%) -->
        <div class="lg:col-span-4 flex flex-col gap-6">
            
            <!-- Operator Panel -->
            <div class="glass-obsidian rounded-[2rem] border border-white/10 p-6 shadow-2xl space-y-10">
                {{-- Risk Hud --}}
                <div>
                    <div class="flex justify-between items-center mb-6">
                        <span class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest">Risk Profile</span>
                        <span class="text-[10px] font-bold text-emerald-400 uppercase tracking-widest">Stable</span>
                    </div>
                    <div class="scale-100">
                        @include('driver.components.safety-hud', ['score' => $riskScore])
                    </div>
                </div>

                <div class="h-px bg-white/5"></div>

                {{-- Duty Toggles --}}
                <div>
                    <div class="flex justify-between items-center mb-6">
                        <span class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest">Duty Interface</span>
                        <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">{{ str_replace('_', ' ', $logStatus) }}</span>
                    </div>
                    @include('driver.components.duty-toggle', ['currentLog' => $currentLog])
                </div>
            </div>

            <!-- Telemetry Gauge -->
            <div class="glass-obsidian rounded-[2rem] border border-white/10 p-6 shadow-2xl transition-all duration-700"
                 @if($isOnDuty) style="border-color: rgba(52,211,153,0.4); box-shadow: 0 0 50px rgba(16,185,129,0.05);" @endif>
                <div class="flex justify-between items-center mb-6">
                    <span class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest">Telemetry</span>
                    @if($isOnDuty)
                        <div class="flex items-center gap-2 text-[10px] font-bold text-emerald-400 uppercase">
                            <div class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse shadow-[0_0_12px_#10b981]"></div>
                            Link Active
                        </div>
                    @else
                        <span class="text-[10px] font-bold text-zinc-700 uppercase tracking-widest italic">Offline</span>
                    @endif
                </div>
                @include('driver.components.speedometer', ['isOnDuty' => $isOnDuty, 'speedLimit' => $speedLimit])
            </div>

            <!-- Asset Info -->
            <div class="fleetco-card rounded-[2rem] p-6 shadow-2xl border-white/10">
                <div class="flex justify-between items-center mb-5">
                    <span class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest">Assigned Asset</span>
                    @if($vehicle)
                        <span class="text-[9px] font-bold text-emerald-400 uppercase tracking-widest">Verified</span>
                    @endif
                </div>
                
                @if($vehicle)
                    @include('driver.components.vehicle-card', ['vehicle' => $vehicle])
                @else
                    <div class="py-8 flex flex-col items-center justify-center opacity-30">
                        <svg class="w-10 h-10 text-zinc-500 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                        <span class="text-[10px] font-bold uppercase tracking-widest">No Link</span>
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    function routeHandler(waypoints) {
        return {
            waypoints: waypoints,
            map: null,
            routeLine: null,
            markers: [],

            get reachedCount() {
                return this.waypoints.filter(w => w.reached_at).length;
            },

            isNext(wp) {
                if (wp.reached_at) return false;
                const firstUnreached = this.waypoints.find(w => !w.reached_at);
                return firstUnreached && firstUnreached.order === wp.order;
            },

            init() {
                this.$nextTick(() => {
                    const center = this.waypoints && this.waypoints.length > 0 ? [this.waypoints[0].lat, this.waypoints[0].lng] : [19.0760, 72.8777];
                    
                    this.map = L.map('driver-route-map', {
                        zoomControl: false,
                        attributionControl: false
                    }).setView(center, 13);

                    L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png').addTo(this.map);

                    if (this.waypoints && this.waypoints.length > 0) {
                        const latlngs = this.waypoints.map(w => [w.lat, w.lng]);
                        this.routeLine = L.polyline(latlngs, {
                            color: '#ff8a00',
                            weight: 4,
                            opacity: 0.6,
                            dashArray: '10, 10'
                        }).addTo(this.map);

                        this.waypoints.forEach((wp, index) => {
                            const icon = L.divIcon({
                                className: 'custom-wp-icon',
                                html: `<div class="w-4 h-4 rounded-full border-2 ${wp.reached_at ? 'bg-emerald-500 border-emerald-400' : (this.isNext(wp) ? 'bg-orange-500 border-white' : 'bg-zinc-800 border-zinc-700')}"></div>`,
                                iconSize: [16, 16],
                                iconAnchor: [8, 8]
                            });
                            L.marker([wp.lat, wp.lng], { icon }).addTo(this.map);
                        });

                        this.map.fitBounds(this.routeLine.getBounds(), { padding: [40, 40] });
                    }
                });
            },

            markReached(routeId, order) {
                fetch(`/driver/routes/${routeId}/waypoints/${order}/reach`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                }).then(res => res.json()).then(data => {
                    if (data.success) {
                        const wp = this.waypoints.find(w => w.order === order);
                        if (wp) {
                            wp.reached_at = new Date().toISOString();
                            window.location.reload();
                        }
                    }
                });
            }
        }
    }

    // Global Shift Timer
    (function() {
        @if($currentLog && $currentLog->status === 'on_duty')
            const startTime = new Date("{{ $currentLog->started_at }}").getTime();
            setInterval(() => {
                const now = new Date().getTime();
                const diff = now - startTime;
                const h = Math.floor(diff / 3600000).toString().padStart(2, '0');
                const m = Math.floor((diff % 3600000) / 60000).toString().padStart(2, '0');
                const s = Math.floor((diff % 60000) / 1000).toString().padStart(2, '0');
                const display = document.getElementById('shift-timer-display');
                if (display) display.innerText = `${h}:${m}:${s}`;
            }, 1000);
        @endif
    })();
</script>
@endpush
@endsection
