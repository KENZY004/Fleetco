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
        <div class="flex flex-col md:flex-row justify-between items-start gap-6 md:gap-6">
            <!-- Left Side: Map Info -->
            <div class="glass-obsidian p-4 md:p-6 rounded-2xl md:rounded-[2rem] border border-white/10 pointer-events-auto shadow-2xl">
                <div class="text-[8px] md:text-[10px] text-orange-500 uppercase font-bold tracking-wider mb-1 md:mb-2">Ops Intelligence</div>
                <div class="font-heading text-sm md:text-xl font-bold text-white tracking-tight mb-1" x-text="viewportCoords"></div>
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

    <!-- 3. LEFT HUD: Fleet Matrix (Desktop Only) -->
    <div class="hidden md:flex absolute top-44 left-6 bottom-10 w-72 z-[1000] pointer-events-auto flex-col gap-4">
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

    <!-- MOBILE FLEET DRAWER (DEDICATED OVERLAY) -->
    <div 
        x-show="isFleetListOpen"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="translate-y-full"
        x-transition:enter-end="translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="translate-y-0"
        x-transition:leave-end="translate-y-full"
        class="md:hidden fixed inset-0 z-[5000] bg-black/95 backdrop-blur-2xl p-4 flex flex-col"
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
                        :class="selectedVehicle?.id == vehicle.id ? 'border-primary/50 bg-primary/5 shadow-[0_0_20px_rgba(255,138,0,0.1)]' : ''"
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
    
    <!-- MOBILE FLEET TOGGLE (FAB) -->
    <button 
        @click="isFleetListOpen = true"
        class="md:hidden fixed bottom-28 right-6 z-[2000] w-16 h-16 rounded-full bg-primary text-black flex items-center justify-center shadow-[0_10px_30px_rgba(255,138,0,0.4)] active:scale-90 transition-transform"
    >
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16m-7 6h7"/></svg>
    </button>

    <!-- 4. RIGHT HUD: Anomaly Feed -->
    <div class="hidden md:flex absolute top-44 right-6 bottom-10 w-80 z-[1000] pointer-events-auto">
        <div class="glass-obsidian rounded-[2rem] border border-white/10 overflow-hidden flex flex-col h-full shadow-2xl">
            <x-anomaly-feed :anomalies="$recentAlerts" />
        </div>
    </div>


    <!-- 5. BOTTOM HUD: Selection Profile -->
    <div 
        x-show="selectedVehicle"
        x-cloak
        style="display: none;"
        x-transition:enter="transition ease-out duration-500"
        x-transition:enter-start="opacity-0 translate-y-8"
        class="absolute bottom-24 md:bottom-10 left-4 md:left-80 right-4 md:right-96 z-[1001] pointer-events-auto"
    >
        <div class="glass-obsidian p-4 md:p-8 rounded-[2rem] md:rounded-[2.5rem] border border-primary/20 shadow-[0_0_50px_rgba(255,138,0,0.15)] flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-4 md:gap-8 w-full md:w-auto">
                <div class="h-12 w-12 md:h-14 md:w-14 rounded-xl md:rounded-2xl bg-primary/10 flex items-center justify-center text-primary border border-primary/20 shrink-0">
                    <svg class="w-6 h-6 md:w-8 md:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-[8px] text-zinc-500 uppercase font-bold tracking-[0.3em] mb-1">Target_Unit</div>
                    <h3 class="font-heading text-lg md:text-2xl font-bold text-white tracking-tight truncate" x-text="selectedVehicle?.license_plate"></h3>
                </div>
                <div class="hidden md:block h-10 w-[1px] bg-white/10 mx-4"></div>
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
                <button @click="visualiseMission()" class="flex-1 md:flex-none px-6 md:px-8 py-3 md:py-4 bg-white text-black rounded-xl md:rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-zinc-200 transition-all shadow-xl active:scale-95">Replay_Mission</button>
                <button @click="selectedVehicle = null; isFollowing = false;" class="p-3 md:p-4 border border-white/10 rounded-xl md:rounded-2xl text-zinc-500 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>
    </div>

    {{-- FORENSIC INCIDENT MODAL --}}
    <div 
        x-show="inspectingAlert" 
        x-cloak
        style="display: none;"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        class="fixed inset-0 z-[10000] flex items-center justify-center p-6 bg-black/80 backdrop-blur-md"
        @click.self="inspectingAlert = null"
        @keydown.escape.window="inspectingAlert = null"
    >
        <div class="glass-obsidian rounded-[3rem] w-full max-w-4xl overflow-hidden border border-white/10 shadow-[0_0_50px_rgba(0,0,0,0.5)] flex h-[700px]">
            {{-- Left Side: Forensic Map --}}
            <div class="w-1/2 relative bg-zinc-900 border-r border-white/5">
                <div id="forensic-map" class="absolute inset-0" style="height: 700px; background: #09090b;"></div>
                <div class="absolute top-8 left-8 z-[1001] px-4 py-2 bg-red-500 text-white text-[10px] font-bold uppercase tracking-widest rounded-lg shadow-2xl">
                    Breach Location
                </div>
            </div>

            {{-- Right Side: Data Deep-Dive --}}
            <div class="w-1/2 p-12 flex flex-col gap-8 bg-obsidian-950/50">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="text-[10px] text-red-500 font-bold uppercase tracking-[0.2em] mb-2">Forensic Analysis</div>
                        <h2 class="text-3xl font-bold text-white tracking-tight" x-text="formatType(inspectingAlert?.type, inspectingAlert?.details)"></h2>
                    </div>
                    <button @click="inspectingAlert = null" class="p-3 text-zinc-500 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div class="p-5 rounded-2xl bg-white/5 border border-white/5">
                        <div class="text-[9px] text-zinc-500 uppercase font-bold tracking-widest mb-2">Driver Impact</div>
                        <div class="text-2xl font-bold text-red-500" x-text="'-' + inspectingAlert?.impact_score + ' PTS'"></div>
                    </div>
                    <div class="p-5 rounded-2xl bg-white/5 border border-white/5">
                        <div class="text-[9px] text-zinc-500 uppercase font-bold tracking-widest mb-2">Occurred At</div>
                        <div class="text-sm font-mono text-white" x-text="inspectingAlert ? new Date(inspectingAlert.occurred_at).toLocaleTimeString() : ''"></div>
                    </div>
                </div>

                <div class="flex-1 space-y-6">
                    <div class="flex items-center gap-4 p-5 rounded-2xl bg-white/[0.02] border border-white/5">
                        <div class="h-12 w-12 rounded-xl bg-primary/10 flex items-center justify-center text-primary">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <div>
                            <div class="text-[9px] text-zinc-500 uppercase font-bold tracking-widest">Involved Operator</div>
                            <div class="text-white font-bold" x-text="inspectingAlert?.driver?.name"></div>
                        </div>
                    </div>

                    <div class="p-6 rounded-2xl border border-dashed border-white/10 bg-white/[0.01]">
                        <div class="text-[9px] text-zinc-500 uppercase font-bold tracking-widest mb-3">Telemetry Snapshot</div>
                        <div class="space-y-2">
                            <div class="flex justify-between text-xs">
                                <span class="text-zinc-500 font-medium">Recorded Speed</span>
                                <span class="text-white font-bold" x-text="(inspectingAlert?.details?.speed || '0') + ' KM/H'"></span>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span class="text-zinc-500 font-medium">Violation Type</span>
                                <span class="text-white font-bold italic" x-text="inspectingAlert?.details?.breach_type || 'Point Breach'"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-6 rounded-2xl border border-white/5 bg-white/[0.01] space-y-4">
                    <div class="text-[9px] text-zinc-500 uppercase font-bold tracking-widest">Resolution Audit Note</div>
                    <textarea 
                        x-model="resolutionNote" 
                        placeholder="Why is this case being closed? (e.g. Authorized detour)" 
                        class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-xs text-white placeholder-zinc-700 outline-none focus:border-primary/50 transition-all h-24 resize-none"
                    ></textarea>
                </div>

                <div class="mt-auto flex gap-4 pt-6">
                    <template x-if="inspectingAlert?.driver?.phone_number">
                        <a :href="'tel:' + inspectingAlert.driver.phone_number" class="flex-1 py-5 bg-emerald-500 text-black text-center rounded-full text-[10px] font-bold uppercase tracking-widest hover:bg-emerald-400 transition-all shadow-lg shadow-emerald-500/20">
                            Initiate Contact
                        </a>
                    </template>
                    <button 
                        @click="dismissAlert(inspectingAlert.id, resolutionNote); inspectingAlert = null; resolutionNote = '';" 
                        class="flex-1 py-5 border border-white/10 rounded-full text-[10px] font-bold uppercase tracking-widest text-zinc-400 hover:text-white transition-all hover:bg-white/5"
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
        transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1); /* Snappy but smooth glide */
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
            routePolylines: {},
            forensicMap: null,
            forensicMarker: null,
            searchQuery: '',
            statusFilter: 'all',
            resolutionNote: '',
            vehicles: @json($vehicles),
            geofences: @json($geofences),
            viewportCoords: '00.0000° N, 00.0000° E',
            haversineDistance: 0,
            systemStatus: 'Connecting...',
            lastPingTime: 'None',
            recentAlerts: @json($recentAlerts),
            isFleetListOpen: false,
            isMobile: window.innerWidth < 768,

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
            playbackMarker: null,
            isVisualising: false,
            playbackIndex: 0,
            playbackPath: [],
            isFollowing: false,

            init() {
                window.addEventListener('load', () => {
                    console.log('Dashboard Initializing...');
                    this.$nextTick(() => {
                        try {
                            this.initMap();
                            this.systemStatus = 'Map Ready';
                        } catch (e) {
                            console.error('Map Engine Failed:', e);
                            this.systemStatus = 'Map Error';
                        }
                        this.startPolling();
                    });
                });
            },
                    
                    // Request notification permission
                    if ("Notification" in window && Notification.permission === "default") {
                        Notification.requestPermission();
                    }

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
                                this.recentAlerts.unshift(e.alert);
                                
                                // Optional: Dispatch native notification
                                if ("Notification" in window && Notification.permission === "granted") {
                                    new Notification(`ALERT: ${this.formatType(e.alert.type, e.alert.details)}`, {
                                        body: `Vehicle ${e.alert.vehicle?.license_plate || 'TEST-001'} - Impact: ${e.alert.impact_score}`
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
                            // Clean up URL so refresh doesn't trigger this again
                            window.history.replaceState({}, document.title, "/dashboard");
                        }, 1000);
                    } else {
                        // FORCE CLOSE MODAL ON STARTUP just in case
                        this.inspectingAlert = null;
                    }
                });
            },

            initMap() {
                this.map = L.map('map', {
                    zoomControl: false,
                    attributionControl: false,
                    fadeAnimation: true,
                }).setView([19.0760, 72.8777], 11);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '© OpenStreetMap contributors'
                }).addTo(this.map);

                this.map.on('move', () => {
                    const center = this.map.getCenter();
                    this.viewportCoords = `${center.lat.toFixed(4)}° N, ${center.lng.toFixed(4)}° E`;
                });

                this.map.on('click', () => {
                    this.selectedVehicle = null;
                    this.isFollowing = false;
                    this.map.flyTo([19.0760, 72.8777], 11);
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
                        const coords = [log.location.coordinates[1], log.location.coordinates[0]];

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
                            const icon = L.divIcon({
                                className: 'custom-vengo-icon',
                                html: `
                                    <div class="relative flex items-center justify-center">
                                        <!-- Directional Shadow -->
                                        <div class="absolute h-12 w-12 rounded-full bg-primary/20 blur-md"></div>
                                        
                                        <!-- Top-Down Vehicle Icon -->
                                        <div class="vehicle-rotation" style="transform: rotate(${log.heading || 0}deg)">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M12 2L4.5 20.29L5.21 21L12 18L18.79 21L19.5 20.29L12 2Z" fill="#ff8a00" stroke="white" stroke-width="1.5" stroke-linejoin="round"/>
                                            </svg>
                                        </div>
                                    </div>
                                `,
                                iconSize: [40, 40],
                                iconAnchor: [20, 20]
                            });

                            this.markers[vehicle.id] = L.marker(coords, { icon })
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
                const center = [19.0760, 72.8777]; // Home Depot
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
                this.clearPlayback();
                this.isVisualising = true;

                try {
                    const response = await fetch(`/api/vehicles/${this.selectedVehicle.id}/playback`);
                    const data = await response.json();
                    this.playbackPath = data.path;

                    if (this.playbackPath.length < 2) {
                        alert("Insufficient historical data for this mission.");
                        this.isVisualising = false;
                        return;
                    }

                    // Draw the static path
                    const latlngs = this.playbackPath.map(p => [p.lat, p.lng]);
                    this.playbackPolyline = L.polyline(latlngs, {
                        color: '#ff8a00',
                        weight: 3,
                        opacity: 0.5,
                        dashArray: '10, 10'
                    }).addTo(this.map);

                    this.map.fitBounds(this.playbackPolyline.getBounds(), { padding: [50, 50] });

                    // Start Animation
                    this.playbackIndex = 0;
                    this.animatePlayback();
                } catch (error) {
                    console.error('Playback error:', error);
                    this.isVisualising = false;
                }
            },

            animatePlayback() {
                if (!this.isVisualising || this.playbackIndex >= this.playbackPath.length) {
                    this.isVisualising = false;
                    return;
                }

                const point = this.playbackPath[this.playbackIndex];
                const coords = [point.lat, point.lng];

                if (!this.playbackMarker) {
                    const icon = L.divIcon({
                        className: 'playback-icon',
                        html: `
                            <div class="relative flex items-center justify-center">
                                <div class="absolute h-12 w-12 rounded-full bg-primary opacity-20 animate-fleetco-pulse"></div>
                                <div class="h-3 w-3 rounded-full bg-white border-2 border-primary shadow-[0_0_15px_#ff8a00]"></div>
                            </div>
                        `,
                        iconSize: [40, 40],
                        iconAnchor: [20, 20]
                    });
                    this.playbackMarker = L.marker(coords, { icon }).addTo(this.map);
                } else {
                    this.playbackMarker.setLatLng(coords);
                }

                this.map.panTo(coords);
                this.playbackIndex++;
                
                // Speed-based animation delay (faster playback)
                setTimeout(() => this.animatePlayback(), 150);
            },

            clearPlayback() {
                this.isVisualising = false;
                if (this.playbackPolyline) {
                    this.map.removeLayer(this.playbackPolyline);
                    this.playbackPolyline = null;
                }
                if (this.playbackMarker) {
                    this.map.removeLayer(this.playbackMarker);
                    this.playbackMarker = null;
                }
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
