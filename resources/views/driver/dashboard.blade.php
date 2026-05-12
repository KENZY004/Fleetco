@extends('driver.layouts.app')

@section('content')
<div class="max-w-6xl mx-auto">
    @php
        $logStatus = $currentLog ? $currentLog->status : 'off_duty';
    @endphp

    <!-- STAT ROW -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <!-- Safety Score -->
        <div class="fleetco-card rounded-xl p-4 flex flex-col justify-between">
            <span class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest mb-3">Safety Score</span>
            @php
                $scoreColor = $riskScore >= 80 ? 'text-[#10b981]' : ($riskScore >= 60 ? 'text-primary' : 'text-[#ef4444]');
            @endphp
            <span class="text-[28px] font-bold tracking-tight {{ $scoreColor }}">{{ number_format($riskScore) }}</span>
        </div>

        <!-- Shift Time -->
        <div class="fleetco-card rounded-xl p-4 flex flex-col justify-between"
             x-data="{
                start: {{ $currentLog ? $currentLog->started_at->timestamp * 1000 : 'null' }},
                timer: '00:00:00',
                init() {
                    if (!this.start) return;
                    setInterval(() => {
                        let diff = Math.floor((Date.now() - this.start) / 1000);
                        let h = String(Math.floor(diff / 3600)).padStart(2, '0');
                        let m = String(Math.floor((diff % 3600) / 60)).padStart(2, '0');
                        let s = String(diff % 60).padStart(2, '0');
                        this.timer = `${h}:${m}:${s}`;
                    }, 1000);
                }
             }">
            <span class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest mb-3">Shift Time</span>
            <span class="text-[28px] font-bold text-white font-mono tracking-tight" x-text="timer">00:00:00</span>
        </div>

        <!-- Distance KM -->
        <div class="fleetco-card rounded-xl p-4 flex flex-col justify-between">
            <span class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest mb-3">Distance KM</span>
            <span class="text-[28px] font-bold text-primary tracking-tight">{{ number_format($distanceKM) }}</span>
        </div>

        <!-- Incidents -->
        <div class="fleetco-card rounded-xl p-4 flex flex-col justify-between">
            <span class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest mb-3">Incidents</span>
            @php
                $incColor = $incidents > 0 ? 'text-[#ef4444]' : 'text-white';
            @endphp
            <span class="text-[28px] font-bold tracking-tight {{ $incColor }}">{{ $incidents }}</span>
        </div>
    </div>

    <!-- ASSIGNED ROUTE CARD -->
    <div class="fleetco-card rounded-xl p-6 mb-6 overflow-hidden" x-data="routeHandler({{ $activeRoute ? json_encode($activeRoute->waypoints) : '[]' }})">
        <div class="flex justify-between items-center mb-6">
            <span class="text-[10px] text-primary font-black uppercase tracking-widest">Assigned Route</span>
            @if($activeRoute)
                <span class="text-[9px] px-2 py-0.5 rounded uppercase font-bold bg-[#10b981]/10 text-[#10b981] animate-pulse">● Active</span>
            @else
                <span class="text-[9px] px-2 py-0.5 rounded uppercase font-bold bg-white/5 text-zinc-500">No Route</span>
            @endif
        </div>

        @if($activeRoute)
            <div class="flex flex-col md:flex-row gap-8 h-[300px]">
                <!-- Left Side: Waypoint List (45%) -->
                <div class="w-full md:w-[45%] flex flex-col">
                    <div class="mb-4">
                        <h3 class="text-lg font-bold text-white tracking-tight font-heading">{{ $activeRoute->name }}</h3>
                        <p class="text-[10px] text-zinc-500 uppercase tracking-widest">{{ $activeRoute->scheduled_for ? $activeRoute->scheduled_for->diffForHumans() : 'Immediate' }}</p>
                    </div>
                    
                    <div class="flex-1 overflow-y-auto custom-scrollbar pr-2 space-y-2">
                        <template x-for="(wp, index) in waypoints" :key="index">
                            <div class="flex items-center justify-between p-3 rounded-lg border border-white/5 bg-white/[0.02] transition-colors"
                                 :class="isNext(wp) ? 'border-primary/30 bg-primary/5' : ''">
                                <div class="flex items-center gap-3">
                                    <div class="h-6 w-6 rounded-full flex items-center justify-center text-[10px] font-bold"
                                         :class="wp.reached_at ? 'bg-[#10b981] text-black' : (isNext(wp) ? 'bg-primary text-black' : 'bg-zinc-800 text-zinc-500')">
                                        <template x-if="wp.reached_at">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                        </template>
                                        <template x-if="!wp.reached_at">
                                            <span x-text="index + 1"></span>
                                        </template>
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-white" x-text="wp.label"></div>
                                        <template x-if="wp.reached_at">
                                            <div class="text-[8px] text-[#10b981] uppercase font-bold tracking-widest">Reached</div>
                                        </template>
                                    </div>
                                </div>

                                <template x-if="isNext(wp)">
                                    <button @click="markReached({{ $activeRoute->id }}, wp.order)" 
                                            class="px-3 py-1 bg-[#10b981] text-black text-[9px] font-black uppercase rounded shadow-lg shadow-emerald-500/20 active:scale-95 transition-all">
                                        Mark Reached
                                    </button>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Right Side: Mini Map (55%) -->
                <div class="w-full md:w-[55%] rounded-xl overflow-hidden border border-white/5 bg-zinc-900 relative">
                    <div id="driver-route-map" class="absolute inset-0 z-0"></div>
                    <div class="absolute bottom-4 right-4 z-[1000] glass-obsidian px-3 py-1.5 rounded-lg border border-white/10">
                        <div class="text-[8px] text-zinc-500 uppercase font-bold tracking-widest">Live Progress</div>
                        <div class="text-xs font-bold text-white"><span x-text="reachedCount">0</span> / <span x-text="waypoints.length">0</span> Stops</div>
                    </div>
                </div>
            </div>
        @else
            <!-- Empty State -->
            <div class="flex flex-col items-center justify-center py-12">
                <div class="w-16 h-16 rounded-2xl bg-white/5 flex items-center justify-center mb-6 text-zinc-700">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                </div>
                <h4 class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-500 mb-2">No Route Assigned</h4>
                <p class="text-[11px] text-zinc-600 font-medium text-center max-w-[200px]">Your fleet manager will assign your route before your shift.</p>
            </div>
        @endif
    </div>

    <!-- BENTO GRID -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        
        <!-- Top-left: Safety Score HUD card -->
        <div class="fleetco-card rounded-xl p-4">
            <div class="flex justify-between items-center mb-4">
                <span class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Risk Assessment</span>
                <span class="text-[9px] px-2 py-0.5 rounded uppercase font-bold {{ $riskScore >= 80 ? 'bg-[#10b981]/10 text-[#10b981]' : ($riskScore >= 60 ? 'bg-primary/10 text-primary' : 'bg-[#ef4444]/10 text-[#ef4444]') }}">
                    {{ $riskScore >= 80 ? 'Optimal' : ($riskScore >= 60 ? 'Caution' : 'At Risk') }}
                </span>
            </div>
            <!-- Include existing component untouched -->
            <div class="mt-2 scale-[0.95] origin-top">
                @include('driver.components.safety-hud', ['score' => $riskScore])
            </div>
        </div>
        
        <!-- Top-right: Duty Status card -->
        <div class="fleetco-card rounded-xl p-4">
            <div class="flex justify-between items-center mb-4">
                <span class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Duty Status</span>
                <span class="text-[9px] px-2 py-0.5 rounded uppercase font-bold {{ $logStatus === 'on_duty' ? 'bg-[#10b981]/10 text-[#10b981]' : ($logStatus === 'break' ? 'bg-primary/10 text-primary' : 'bg-[#ef4444]/10 text-[#ef4444]') }}">
                    {{ str_replace('_', ' ', $logStatus) }}
                </span>
            </div>
            <!-- Include existing component untouched -->
            <div class="mt-2">
                @include('driver.components.duty-toggle', ['currentLog' => $currentLog])
            </div>
        </div>

        <!-- Bottom-left: Active Vehicle card -->
        <div class="fleetco-card rounded-xl p-4 flex flex-col">
            <div class="flex justify-between items-center mb-4">
                <span class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Assigned Vehicle</span>
                @if($vehicle)
                    <span class="text-[9px] px-2 py-0.5 rounded uppercase font-bold bg-[#10b981]/10 text-[#10b981]">Active</span>
                @else
                    <span class="text-[9px] px-2 py-0.5 rounded uppercase font-bold bg-white/5 text-zinc-500">Unassigned</span>
                @endif
            </div>
            
            @if($vehicle)
                <div class="mt-2 flex-1 flex flex-col justify-center">
                    @include('driver.components.vehicle-card', ['vehicle' => $vehicle])
                </div>
            @else
                <div class="flex flex-col items-center justify-center py-12 flex-1">
                    <div class="w-16 h-16 rounded-[1.2rem] bg-white/5 flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-zinc-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                    </div>
                    <span class="text-[10px] font-black uppercase tracking-widest text-zinc-600">No Vehicle Assigned</span>
                </div>
            @endif
        </div>

        <!-- Bottom-right: Live Speed card -->
        @php
            $isOnDuty = \App\Models\DutyLog::where('driver_id', auth()->id())
                ->where('status', 'on_duty')
                ->whereNull('ended_at')
                ->exists();
        @endphp
        <div class="fleetco-card rounded-xl p-4 transition-all duration-500" 
             @if($isOnDuty) style="border-color: rgba(16,185,129,0.4); box-shadow: 0 0 20px rgba(16,185,129,0.05);" @endif>
            <div class="flex justify-between items-center mb-4">
                <span class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest">Live Telemetry</span>
                @if($isOnDuty)
                    <span class="text-[9px] px-2 py-0.5 rounded uppercase font-bold bg-[#10b981]/10 text-[#10b981] animate-pulse">
                        ● Live
                    </span>
                @else
                    <span class="text-[9px] px-2 py-0.5 rounded uppercase font-bold bg-white/5 text-zinc-500">
                        Offline
                    </span>
                @endif
            </div>
            
            <div class="mt-2 scale-95 origin-top">
                @php $isOnDuty = $currentLog && $currentLog->status === 'on_duty'; @endphp
                @include('driver.components.speedometer', ['isOnDuty' => $isOnDuty, 'speedLimit' => $speedLimit])
            </div>
        </div>
    </div>

    <!-- Full-width bottom: Incident Feed card -->
    <div class="fleetco-card rounded-xl p-4 w-full">
        <div class="flex justify-between items-center mb-6">
            <span class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Incident Feed</span>
            <span class="text-[9px] px-2 py-0.5 rounded uppercase font-bold bg-[#10b981]/10 text-[#10b981]">Clear</span>
        </div>
        
        <div class="flex flex-col items-center justify-center py-16">
            <div class="w-12 h-12 rounded-full bg-[#10b981]/10 flex items-center justify-center mb-5">
                <svg class="w-6 h-6 text-[#10b981]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            </div>
            <span class="text-[10px] font-black uppercase tracking-widest text-zinc-500">No Incidents This Shift</span>
        </div>
    </div>
</div>
@push('scripts')
<script>
    function routeHandler(initialWaypoints) {
        return {
            waypoints: initialWaypoints,
            map: null,
            markers: [],
            polyline: null,
            userMarker: null,

            init() {
                if (this.waypoints.length === 0) return;
                
                this.$nextTick(() => {
                    this.initMap();
                    // Listen for GPS updates from the dashboard's existing geolocation logic
                    window.addEventListener('gps-update', (e) => {
                        this.updateUserLocation(e.detail.lat, e.detail.lng);
                    });
                });
            },

            initMap() {
                this.map = L.map('driver-route-map', {
                    zoomControl: false,
                    attributionControl: false
                });

                L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
                    maxZoom: 20
                }).addTo(this.map);

                this.renderRoute();
            },

            renderRoute() {
                const latlngs = this.waypoints.map(wp => [wp.lat, wp.lng]);
                
                // Polyline
                if (this.polyline) this.map.removeLayer(this.polyline);
                this.polyline = L.polyline(latlngs, {
                    color: '#ff8a00',
                    weight: 3,
                    opacity: 0.8
                }).addTo(this.map);

                // Markers
                this.markers.forEach(m => this.map.removeLayer(m));
                this.markers = [];

                this.waypoints.forEach((wp, index) => {
                    const isNext = this.isNext(wp);
                    const isReached = wp.reached_at !== null;
                    
                    const icon = L.divIcon({
                        className: 'waypoint-icon',
                        html: `<div class="waypoint-marker ${isReached ? 'reached' : (isNext ? 'next' : 'future')}">${index + 1}</div>`,
                        iconSize: [24, 24],
                        iconAnchor: [12, 12]
                    });

                    const marker = L.marker([wp.lat, wp.lng], { icon }).addTo(this.map);
                    this.markers.push(marker);
                });

                this.map.fitBounds(this.polyline.getBounds(), { padding: [30, 30] });
            },

            isNext(wp) {
                if (wp.reached_at) return false;
                const firstUnreached = this.waypoints.find(w => !w.reached_at);
                return firstUnreached && firstUnreached.order === wp.order;
            },

            get reachedCount() {
                return this.waypoints.filter(w => w.reached_at).length;
            },

            async markReached(routeId, order) {
                try {
                    const response = await fetch(`/driver/route/${routeId}/waypoint/${order}/reach`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });
                    
                    const data = await response.json();
                    if (data.success) {
                        // Update local state
                        const index = this.waypoints.findIndex(w => w.order === order);
                        if (index !== -1) {
                            this.waypoints[index].reached_at = new Date().toISOString();
                        }
                        this.renderRoute();
                        
                        if (data.status === 'completed') {
                            alert('Route Completed! Well done.');
                            window.location.reload();
                        }
                    }
                } catch (error) {
                    console.error('Error marking waypoint reached:', error);
                }
            },

            updateUserLocation(lat, lng) {
                if (!this.map) return;
                
                const coords = [lat, lng];
                if (!this.userMarker) {
                    this.userMarker = L.circleMarker(coords, {
                        radius: 6,
                        fillColor: '#3b82f6',
                        color: 'white',
                        weight: 2,
                        fillOpacity: 1
                    }).addTo(this.map);
                } else {
                    this.userMarker.setLatLng(coords);
                }
            }
        };
    }
</script>

<style>
    .waypoint-marker.reached { background: #10b981; box-shadow: 0 0 10px rgba(16, 185, 129, 0.4); }
    .waypoint-marker.next { background: #ff8a00; box-shadow: 0 0 15px rgba(255, 138, 0, 0.6); animation: pulse 2s infinite; }
    .waypoint-marker.future { background: #333; border-color: #444; color: #666; box-shadow: none; }
    
    @keyframes pulse {
        0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(255, 138, 0, 0.7); }
        70% { transform: scale(1.1); box-shadow: 0 0 0 10px rgba(255, 138, 0, 0); }
        100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(255, 138, 0, 0); }
    }
</style>
@endpush
@endsection
