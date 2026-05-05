@extends('layouts.app')

@section('content')
<div 
    class="space-y-8 h-full flex flex-col" 
    x-data="fleetDashboard()"
    x-init="init()"
>
    <!-- Stats Overview -->
    <x-stats-overview :stats="$stats ?? []" />

    <!-- Main Bento Matrix -->
    <div class="grid grid-cols-12 gap-8 flex-1 min-h-0">
        
        <!-- Hero Map Interface -->
        <div class="col-span-12 lg:col-span-8 lg:row-span-8 relative min-h-[500px] bg-obsidian-900 border border-border rounded-[2rem] overflow-hidden group shadow-2xl">
            <!-- Map Overlay: HUD -->
            <div class="absolute top-8 left-8 z-[1000] glass-obsidian p-5 rounded-2xl border border-white/10 pointer-events-none">
                <div class="text-[10px] text-orange-500 uppercase font-bold tracking-wider mb-2">Real-time Map Data</div>
                <div class="font-heading text-lg font-bold text-white tracking-tight mb-1" x-text="viewportCoords"></div>
                <div class="flex items-center gap-2">
                    <div class="h-1 w-1 bg-emerald-500 rounded-full animate-pulse"></div>
                    <span class="text-[9px] font-bold text-zinc-500 uppercase tracking-widest">System Status: Online</span>
                </div>
            </div>

            <!-- Leaflet Map -->
            <div id="map" class="absolute inset-0 z-0 opacity-80 group-hover:opacity-100 transition-opacity duration-700"></div>

            <!-- Side HUD: Telemetry -->
            <div class="absolute bottom-8 right-8 z-[1000] glass-obsidian p-6 rounded-2xl w-56 opacity-0 translate-y-4 group-hover:opacity-100 group-hover:translate-y-0 transition-all duration-500 border border-white/10">
                <h4 class="text-[10px] font-bold mb-4 uppercase text-zinc-500 tracking-wider">Map Legend</h4>
                <div class="space-y-4">
                    <div class="flex justify-between items-center text-[10px] font-bold uppercase tracking-widest">
                        <span class="text-zinc-400">Vehicle Route</span>
                        <div class="w-8 h-1 bg-primary rounded-full shadow-[0_0_8px_#ff8a00]"></div>
                    </div>
                    <div class="flex justify-between items-center text-[10px] font-bold uppercase tracking-widest">
                        <span class="text-zinc-400">Heat Points</span>
                        <div class="w-8 h-1 bg-white/20 rounded-full"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Heuristic Deviations Feed -->
        <div class="col-span-12 lg:col-span-4 lg:row-span-7 fleetco-card rounded-[2rem] overflow-hidden">
            <x-anomaly-feed :anomalies="$recentAlerts" />
        </div>

        <!-- Selection Intelligent Profile -->
        <div class="col-span-12 lg:col-span-4 lg:row-span-5 fleetco-card p-10 flex flex-col rounded-[2rem] relative overflow-hidden">
            <div x-show="selectedVehicle" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-y-4">
                <div class="flex items-center gap-6 mb-10">
                    <div class="h-16 w-16 rounded-2xl bg-white/5 border border-border flex items-center justify-center text-primary group-hover:scale-110 transition-transform duration-500">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                    </div>
                    <div>
                        <div class="text-[10px] text-zinc-500 uppercase font-bold tracking-wider mb-1">Vehicle Details</div>
                        <h3 class="font-heading text-2xl font-bold text-white tracking-tight" x-text="selectedVehicle?.license_plate"></h3>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-8 mb-10">
                    <div class="p-6 rounded-2xl bg-white/5">
                        <div class="text-[10px] text-zinc-500 uppercase font-bold tracking-wider mb-2">Driver Safety Score</div>
                        <div class="font-heading text-3xl font-bold text-white tracking-tight" x-text="selectedVehicle?.driver?.risk_score ? Math.round(selectedVehicle.driver.risk_score) : '—'"></div>
                    </div>
                    <div class="p-6 rounded-2xl bg-white/5">
                        <div class="text-[10px] text-zinc-500 uppercase font-bold tracking-[0.2em] mb-2">Status</div>
                        <div class="flex items-center gap-2 mt-1">
                            <div class="h-2 w-2 rounded-full" :class="(selectedVehicle?.telematics_logs?.length > 0) ? 'bg-emerald-500 animate-pulse' : 'bg-zinc-800'"></div>
                            <span class="text-xs font-bold text-white uppercase tracking-widest" x-text="(selectedVehicle?.telematics_logs?.length > 0) ? 'Tracking Active' : 'Offline'"></span>
                        </div>
                    </div>
                </div>

                <div class="p-6 rounded-2xl border border-border font-mono text-[11px] mb-10">
                    <div class="text-zinc-500 uppercase font-bold text-[9px] mb-4 tracking-wider">Location Intelligence</div>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-zinc-500">CENTROID_DIST</span>
                            <span class="text-white font-bold" x-text="haversineDistance + ' KM'"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-zinc-500">LATENCY</span>
                            <span class="text-emerald-400 font-bold uppercase tracking-widest">Optimized</span>
                        </div>
                    </div>
                </div>

                <div class="flex gap-4">
                    <button 
                        @click="visualiseMission()"
                        class="flex-1 py-5 bg-white text-black rounded-full text-[10px] font-bold uppercase tracking-wider hover:bg-white/90 transition-all shadow-lg active:scale-95"
                    >
                        Replay Route
                    </button>
                    <button 
                        class="px-6 py-5 border border-white/10 rounded-full text-zinc-500 hover:text-white transition-colors"
                        @click="selectedVehicle = null; clearPlayback();"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>

            <div x-show="!selectedVehicle" class="h-full flex flex-col items-center justify-center text-center p-12">
                <div class="w-20 h-20 rounded-full border border-white/5 flex items-center justify-center mb-8 opacity-20">
                    <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                </div>
                <h4 class="text-[10px] text-zinc-500 uppercase tracking-wider font-bold">Select a vehicle to view data</h4>
            </div>
        </div>

        <!-- Vehicle Fleet Matrix -->
        <div class="col-span-12 lg:col-span-3 lg:row-span-4 fleetco-card rounded-[2rem] overflow-hidden flex flex-col">
            <div class="py-6 px-8 border-b border-border bg-obsidian-900/50 flex justify-between items-center">
                <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-500">Fleet List</span>
                <span class="text-[9px] font-bold px-2 py-1 bg-white/5 text-white uppercase" x-text="vehicles.length + ' Units'"></span>
            </div>
            <div class="flex-1 overflow-y-auto custom-scrollbar">
                <div class="divide-y divide-border">
                    <template x-for="vehicle in vehicles" :key="vehicle.id">
                        <div 
                            @click="selectVehicle(vehicle)"
                            class="p-6 hover:bg-white/5 cursor-pointer transition-all flex items-center gap-5"
                            :class="selectedVehicle?.id == vehicle.id ? 'bg-white/5 border-l-4 border-primary' : ''"
                        >
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-bold text-white tracking-tight truncate" x-text="vehicle.name"></h4>
                                <span class="text-[10px] font-medium text-zinc-600 tracking-tight" x-text="vehicle.license_plate"></span>
                            </div>
                            <div 
                                class="h-1.5 w-1.5 rounded-full"
                                :class="vehicle.latest_log ? 'bg-primary shadow-[0_0_10px_#ff8a00]' : 'bg-zinc-800'"
                            ></div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Mission Ledger -->
        <div class="col-span-12 lg:col-span-8 lg:row-span-4">
            <x-trip-history :trips="$trips" />
        </div>

    {{-- FORENSIC INCIDENT MODAL --}}
    <div 
        x-show="inspectingAlert" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        class="fixed inset-0 z-[10000] flex items-center justify-center p-6 bg-black/80 backdrop-blur-md"
        @click.self="inspectingAlert = null"
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
    .leaflet-popup-content { margin: 0 !important; }
</style>

@push('scripts')
<script>
    function fleetDashboard() {
        return {
            map: null,
            markers: {},
            selectedVehicle: null,
            inspectingAlert: null,
            forensicMap: null,
            forensicMarker: null,
            searchQuery: '',
            statusFilter: 'all',
            resolutionNote: '',
            vehicles: @json($vehicles),
            geofences: @json($geofences),
            viewportCoords: '00.0000° N, 00.0000° E',
            haversineDistance: '0.0',

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
            
            // Playback State
            playbackPolyline: null,
            playbackMarker: null,
            isVisualising: false,
            playbackIndex: 0,
            playbackPath: [],
            isFollowing: false,

            init() {
                this.$nextTick(() => {
                    this.initMap();
                    this.startPolling();
                    
                    // Request notification permission
                    if ("Notification" in window && Notification.permission === "default") {
                        Notification.requestPermission();
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
                }).setView([19.0760, 72.8777], 11);

                L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
                    maxZoom: 19,
                    backgroundColor: '#020203'
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
                            weight: 1,
                            fillOpacity: 0.1,
                            dashArray: gf.type === 'restricted' ? '5, 5' : null
                        }).addTo(this.map).bindTooltip(gf.name);
                    }
                });
            },

            updateMarkers() {
                this.vehicles.forEach(vehicle => {
                    if (vehicle.telematics_logs && vehicle.telematics_logs.length > 0) {
                        const log = vehicle.telematics_logs[0];
                        const coords = [log.location.coordinates[1], log.location.coordinates[0]];

                        if (this.markers[vehicle.id]) {
                            this.markers[vehicle.id].setLatLng(coords);
                        } else {
                            const icon = L.divIcon({
                                className: 'custom-vengo-icon',
                                html: `
                                    <div class="relative flex items-center justify-center">
                                        <div class="absolute h-10 w-10 rounded-full bg-primary opacity-10 animate-fleetco-pulse"></div>
                                        <div class="h-2 w-2 rounded-full bg-primary shadow-[0_0_10px_#ff8a00] border border-white/20"></div>
                                    </div>
                                `,
                                iconSize: [40, 40],
                                iconAnchor: [20, 20]
                            });

                            this.markers[vehicle.id] = L.marker(coords, { icon })
                                .addTo(this.map)
                                .bindTooltip(vehicle.license_plate, {
                                    permanent: true,
                                    direction: 'top',
                                    className: 'fleet-marker-label',
                                    offset: [0, -10]
                                })
                                .bindPopup(`
                                    <div class="p-3 bg-obsidian-900 text-white rounded-xl border border-white/10 min-w-[150px]">
                                        <div class="text-[9px] text-zinc-500 uppercase font-bold tracking-widest mb-1">Vehicle Info</div>
                                        <div class="text-sm font-bold mb-3">${vehicle.license_plate}</div>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <div class="text-[8px] text-zinc-500 uppercase font-bold">Speed</div>
                                                <div class="text-xs font-bold text-primary">${Math.round(log.speed)} km/h</div>
                                            </div>
                                            <div>
                                                <div class="text-[8px] text-zinc-500 uppercase font-bold">Status</div>
                                                <div class="text-[10px] font-bold text-emerald-400">ACTIVE</div>
                                            </div>
                                        </div>
                                    </div>
                                `, {
                                    className: 'fleet-popup-custom',
                                    closeButton: false
                                })
                                .on('click', () => this.selectVehicle(vehicle));
                        }

                        if (this.isFollowing && this.selectedVehicle?.id === vehicle.id) {
                            this.map.panTo(coords);
                        }
                    }
                });
            },

            selectVehicle(vehicle) {
                this.selectedVehicle = vehicle;
                this.isFollowing = true;
                if (vehicle.telematics_logs && vehicle.telematics_logs.length > 0) {
                    const log = vehicle.telematics_logs[0];
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

            async startPolling() {
                setInterval(async () => {
                    if (this.isVisualising) return; // Pause polling during playback for immersion
                    
                    try {
                        const response = await fetch('/api/vehicles');
                        this.vehicles = await response.json();
                        this.updateMarkers();
                        
                        if (this.selectedVehicle) {
                            const updated = this.vehicles.find(v => v.id === this.selectedVehicle.id);
                            if (updated) {
                                this.selectedVehicle = updated;
                                if (updated.telematics_logs && updated.telematics_logs.length > 0) {
                                    this.calculateHaversine([updated.telematics_logs[0].location.coordinates[1], updated.telematics_logs[0].location.coordinates[0]]);
                                }
                            }
                        }
                    } catch (error) {
                        console.error('Polling error:', error);
                    }
                }, 5000);
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
