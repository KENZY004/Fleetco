@extends('layouts.app')

@section('content')
<div class="p-8" x-data="routeBuilder()">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-white tracking-tight">Build Route</h1>
            <p class="text-zinc-500 text-sm">Design a mission path and assign it to a unit.</p>
        </div>
        <div class="flex gap-4">
            <a href="{{ route('fleet.routes.index') }}" class="px-6 py-3 border border-white/10 text-zinc-400 font-bold rounded-xl hover:text-white transition-all">
                Cancel
            </a>
            <button @click="saveRoute()" class="px-6 py-3 bg-primary text-black font-bold rounded-xl hover:bg-orange-600 transition-all shadow-lg shadow-primary/20">
                Finalize & Assign
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Sidebar: Configuration -->
        <div class="space-y-6">
            <div class="glass-obsidian p-8 rounded-[2.5rem] border border-white/10 space-y-6">
                <div>
                    <label class="text-[10px] text-zinc-500 uppercase font-bold tracking-[0.2em] mb-3 block">Route Identity</label>
                    <input type="text" x-model="name" placeholder="e.g. Mumbai North Run" 
                        class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 text-white placeholder-zinc-700 outline-none focus:border-primary/50 transition-all">
                </div>

                <div>
                    <label class="text-[10px] text-zinc-500 uppercase font-bold tracking-[0.2em] mb-3 block">Assign Unit</label>
                    <div class="space-y-4">
                        <select x-model="driverId" class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 text-white outline-none focus:border-primary/50 transition-all">
                            <option value="">Select Operator</option>
                            @foreach($drivers as $driver)
                                <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                            @endforeach
                        </select>
                        <select x-model="vehicleId" class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 text-white outline-none focus:border-primary/50 transition-all">
                            <option value="">Select Vehicle</option>
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}">{{ $vehicle->license_plate }} ({{ $vehicle->name }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="text-[10px] text-zinc-500 uppercase font-bold tracking-[0.2em] mb-3 block">Schedule For</label>
                    <input type="datetime-local" x-model="scheduledFor" 
                        class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 text-white outline-none focus:border-primary/50 transition-all">
                </div>
            </div>

            <!-- Waypoint List -->
            <div class="glass-obsidian rounded-[2.5rem] border border-white/10 overflow-hidden">
                <div class="px-8 py-5 border-b border-white/5 bg-white/[0.02]">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-500">Waypoints Pipeline</span>
                </div>
                <div class="p-6 max-h-[400px] overflow-y-auto custom-scrollbar">
                    <div class="space-y-3">
                        <template x-for="(wp, index) in waypoints" :key="wp.id">
                            <div class="p-4 rounded-2xl bg-white/5 border border-white/5 flex items-center gap-4 group">
                                <div class="h-8 w-8 rounded-lg bg-primary/10 flex items-center justify-center text-primary text-xs font-bold shrink-0" x-text="index + 1"></div>
                                <div class="flex-1 min-w-0">
                                    <input type="text" x-model="wp.label" class="bg-transparent border-none p-0 text-sm font-bold text-white outline-none w-full focus:ring-0">
                                    <div class="text-[10px] text-zinc-600 font-mono" x-text="wp.lat.toFixed(4) + ', ' + wp.lng.toFixed(4)"></div>
                                </div>
                                <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button @click="moveWaypoint(index, -1)" class="p-1.5 text-zinc-500 hover:text-white transition-colors" :disabled="index === 0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                                    </button>
                                    <button @click="moveWaypoint(index, 1)" class="p-1.5 text-zinc-500 hover:text-white transition-colors" :disabled="index === waypoints.length - 1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </button>
                                    <button @click="removeWaypoint(index)" class="p-1.5 text-red-500 hover:text-red-400 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                        <template x-if="waypoints.length === 0">
                            <div class="text-center py-12">
                                <div class="text-zinc-600 text-sm italic">Click on the map to add stops</div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Area: Map Builder -->
        <div class="lg:col-span-2 space-y-6">
            <div class="glass-obsidian rounded-[3rem] border border-white/10 overflow-hidden relative" style="height: 600px;">
                <div id="route-map" class="absolute inset-0 z-0 bg-zinc-900"></div>
                
                <!-- Map Controls Overlay -->
                <div class="absolute top-8 left-8 z-[1000] flex flex-col gap-3">
                    <div class="glass-obsidian px-4 py-3 rounded-xl border border-white/10 shadow-2xl">
                        <div class="text-[8px] text-primary font-bold uppercase tracking-widest mb-1">Editor Mode</div>
                        <div class="text-white text-xs font-bold flex items-center gap-2">
                            <div class="h-1.5 w-1.5 bg-primary rounded-full animate-pulse"></div>
                            Active Build
                        </div>
                    </div>
                </div>

                <div class="absolute bottom-8 left-8 right-8 z-[1000]">
                    <div class="glass-obsidian px-8 py-4 rounded-2xl border border-white/10 flex items-center justify-between shadow-2xl">
                        <div class="flex items-center gap-6">
                            <div>
                                <div class="text-[8px] text-zinc-500 uppercase font-bold tracking-widest">Total Distance</div>
                                <div class="text-xl font-bold text-white"><span x-text="totalDistance">0.0</span> <span class="text-xs text-zinc-600">KM</span></div>
                            </div>
                            <div class="h-8 w-[1px] bg-white/10"></div>
                            <div>
                                <div class="text-[8px] text-zinc-500 uppercase font-bold tracking-widest">Est. Duration</div>
                                <div class="text-xl font-bold text-white"><span x-text="Math.round(totalDistance * 3)">0</span> <span class="text-xs text-zinc-600">MIN</span></div>
                            </div>
                        </div>
                        <div class="text-[10px] text-zinc-500 font-medium italic">
                            Tip: Drag markers to adjust positions
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-8 rounded-[2.5rem] border border-dashed border-white/10 bg-white/[0.01]">
                <h4 class="text-sm font-bold text-white mb-2">Instructions</h4>
                <ul class="text-xs text-zinc-500 space-y-2">
                    <li>• Left-click anywhere on the map to drop a waypoint marker.</li>
                    <li>• Markers are numbered sequentially in the order of the mission.</li>
                    <li>• Drag markers to reposition them precisely.</li>
                    <li>• Use the list on the left to rename stops (e.g., "Terminal 1", "Customer Drop-off").</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<form id="route-form" action="{{ route('fleet.routes.store') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="name" :value="name">
    <input type="hidden" name="driver_id" :value="driverId">
    <input type="hidden" name="vehicle_id" :value="vehicleId">
    <input type="hidden" name="scheduled_for" :value="scheduledFor">
    <input type="hidden" name="waypoints" :value="JSON.stringify(waypoints)">
</form>

<style>
    .leaflet-container { background: #09090b !important; }
    .waypoint-icon {
        background: none !important;
        border: none !important;
    }
    .waypoint-marker {
        width: 32px;
        height: 32px;
        background: #ff8a00;
        border: 2px solid white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: black;
        font-weight: 900;
        font-size: 14px;
        box-shadow: 0 0 20px rgba(255, 138, 0, 0.4);
        cursor: grab;
    }
    .waypoint-marker:active { cursor: grabbing; }
</style>

@push('scripts')
<script>
    function routeBuilder() {
        return {
            map: null,
            name: '',
            driverId: '',
            vehicleId: '',
            scheduledFor: '',
            waypoints: [],
            markers: [],
            polyline: null,
            totalDistance: 0,

            init() {
                this.$nextTick(() => {
                    this.initMap();
                });
            },

            initMap() {
                this.map = L.map('route-map', {
                    zoomControl: false,
                    attributionControl: false
                }).setView([19.0760, 72.8777], 12);

                L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
                    maxZoom: 20
                }).addTo(this.map);

                this.map.on('click', (e) => {
                    this.addWaypoint(e.latlng.lat, e.latlng.lng);
                });
            },

            addWaypoint(lat, lng) {
                const id = Date.now();
                const order = this.waypoints.length + 1;
                const waypoint = {
                    id: id,
                    lat: lat,
                    lng: lng,
                    label: `Stop ${order}`,
                    order: order,
                    reached_at: null
                };

                this.waypoints.push(waypoint);
                this.renderOnMap();
            },

            removeWaypoint(index) {
                this.waypoints.splice(index, 1);
                // Re-order remaining
                this.waypoints.forEach((wp, i) => wp.order = i + 1);
                this.renderOnMap();
            },

            moveWaypoint(index, direction) {
                const newIndex = index + direction;
                if (newIndex < 0 || newIndex >= this.waypoints.length) return;
                
                const temp = this.waypoints[index];
                this.waypoints[index] = this.waypoints[newIndex];
                this.waypoints[newIndex] = temp;
                
                // Re-order
                this.waypoints.forEach((wp, i) => wp.order = i + 1);
                this.renderOnMap();
            },

            renderOnMap() {
                // Clear existing
                this.markers.forEach(m => this.map.removeLayer(m));
                this.markers = [];
                if (this.polyline) this.map.removeLayer(this.polyline);

                const latlngs = [];

                this.waypoints.forEach((wp, index) => {
                    const coords = [wp.lat, wp.lng];
                    latlngs.push(coords);

                    const icon = L.divIcon({
                        className: 'waypoint-icon',
                        html: `<div class="waypoint-marker">${index + 1}</div>`,
                        iconSize: [32, 32],
                        iconAnchor: [16, 16]
                    });

                    const marker = L.marker(coords, { icon, draggable: true })
                        .addTo(this.map)
                        .on('dragend', (e) => {
                            const newLatLng = e.target.getLatLng();
                            this.waypoints[index].lat = newLatLng.lat;
                            this.waypoints[index].lng = newLatLng.lng;
                            this.renderOnMap();
                        });
                    
                    this.markers.push(marker);
                });

                if (latlngs.length > 1) {
                    this.polyline = L.polyline(latlngs, {
                        color: '#ff8a00',
                        weight: 4,
                        opacity: 0.6,
                        dashArray: '10, 10'
                    }).addTo(this.map);
                    
                    this.calculateDistance(latlngs);
                } else {
                    this.totalDistance = 0;
                }
            },

            calculateDistance(latlngs) {
                let dist = 0;
                for (let i = 0; i < latlngs.length - 1; i++) {
                    dist += this.map.distance(L.latLng(latlngs[i]), L.latLng(latlngs[i+1]));
                }
                this.totalDistance = (dist / 1000).toFixed(1);
            },

            saveRoute() {
                if (!this.name) return alert('Please enter a route name.');
                if (this.waypoints.length < 2) return alert('Please add at least 2 waypoints.');
                
                this.$nextTick(() => {
                    document.getElementById('route-form').submit();
                });
            }
        }
    }
</script>
@endpush
@endsection
