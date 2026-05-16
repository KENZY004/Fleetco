@extends('layouts.app')

@section('content')
<div class="p-8" x-data="routeBuilder()">
    <div class="flex justify-between items-center mb-8">
        <div>
            <div class="text-[10px] font-bold tracking-widest text-orange-500 uppercase mb-2">Mission Control</div>
            <h1 class="text-3xl font-bold text-white tracking-tight">Edit Route</h1>
            <p class="text-zinc-500 text-sm">Modify mission path and re-assign assets.</p>
        </div>

        @if ($errors->any())
            <div class="bg-red-500/10 border border-red-500/50 p-4 rounded-xl text-red-500 text-xs font-bold">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="flex gap-4">
            <a href="{{ route('fleet.routes.show', $route->id) }}" class="px-6 py-3 border border-white/10 text-zinc-400 font-bold rounded-xl hover:text-white transition-all">
                Cancel
            </a>
            <button @click="saveRoute()" class="px-6 py-3 bg-primary text-black font-bold rounded-xl hover:bg-orange-600 transition-all shadow-lg shadow-primary/20">
                Update Mission
            </button>
        </div>
    </div>

    <div class="flex flex-col-reverse lg:grid lg:grid-cols-3 gap-8">
        <!-- Sidebar: Configuration -->
        <div class="space-y-6">
            <div class="glass-obsidian p-8 rounded-[2.5rem] border border-white/10 space-y-6">
                <div>
                    <label class="text-[10px] text-zinc-500 uppercase font-bold tracking-[0.2em] mb-3 block">Route Identity</label>
                    <input type="text" x-model="name" placeholder="e.g. Mumbai North Run" 
                        class="w-full bg-[#111111] border border-white/10 rounded-2xl px-5 py-4 text-white placeholder-zinc-700 outline-none focus:border-primary/50 transition-all shadow-inner">
                </div>

                <div>
                    <label class="text-[10px] text-zinc-500 uppercase font-bold tracking-[0.2em] mb-3 block">Assign Unit</label>
                    <div class="space-y-4">
                        <select x-model="driverId" class="w-full bg-[#111111] border border-white/10 rounded-2xl px-5 py-4 text-white outline-none focus:border-primary/50 transition-all appearance-none cursor-pointer">
                            <option value="" class="bg-[#111111]">Select Operator</option>
                            @foreach($drivers as $driver)
                                <option value="{{ $driver->id }}" class="bg-[#111111]">{{ $driver->name }}</option>
                            @endforeach
                        </select>
                        <select x-model="vehicleId" class="w-full bg-[#111111] border border-white/10 rounded-2xl px-5 py-4 text-white outline-none focus:border-primary/50 transition-all appearance-none cursor-pointer">
                            <option value="" class="bg-[#111111]">Select Vehicle</option>
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}" class="bg-[#111111]">{{ $vehicle->license_plate }} ({{ $vehicle->name }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="text-[10px] text-zinc-500 uppercase font-bold tracking-[0.2em] mb-3 block">Schedule For</label>
                    <input type="datetime-local" id="scheduled_at_input" x-model="scheduledFor" 
                        class="w-full bg-[#111111] border border-white/10 rounded-2xl px-5 py-4 text-white outline-none focus:border-primary/50 transition-all [color-scheme:dark]">
                </div>
                
                <div>
                    <label class="text-[10px] text-zinc-500 uppercase font-bold tracking-[0.2em] mb-3 block">Status Override</label>
                    <select x-model="status" class="w-full bg-[#111111] border border-white/10 rounded-2xl px-5 py-4 text-white outline-none focus:border-primary/50 transition-all appearance-none cursor-pointer">
                        <option value="draft">Draft (Saved as Template)</option>
                        <option value="active">Active (Dispatched to Driver)</option>
                        <option value="completed">Completed</option>
                    </select>
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
                                    <button @click="moveWaypoint(index, -1)" 
                                            class="p-1.5 text-zinc-500 hover:text-white disabled:opacity-20 disabled:cursor-not-allowed transition-colors" 
                                            :disabled="index === 0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                                    </button>
                                    <button @click="moveWaypoint(index, 1)" 
                                            class="p-1.5 text-zinc-500 hover:text-white disabled:opacity-20 disabled:cursor-not-allowed transition-colors" 
                                            :disabled="index === waypoints.length - 1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </button>
                                    <button @click="removeWaypoint(index)" class="p-1.5 text-red-500 hover:text-red-400 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Area: Map Builder -->
        <div class="lg:col-span-2 space-y-6">
            <div class="glass-obsidian rounded-[2rem] md:rounded-[3rem] border border-white/10 overflow-hidden relative h-[400px] md:h-[600px]">
                <div id="route-map" class="absolute inset-0 z-0 bg-zinc-900"></div>
                
                <!-- Map Controls Overlay -->
                <div class="absolute top-8 left-8 z-[1000] flex flex-col gap-3">
                    <div class="glass-obsidian px-4 py-3 rounded-xl border border-white/10 shadow-2xl">
                        <div class="text-[8px] text-primary font-bold uppercase tracking-widest mb-1">Editor Mode</div>
                        <div class="text-white text-xs font-bold flex items-center gap-2">
                            <div class="h-1.5 w-1.5 bg-primary rounded-full animate-pulse"></div>
                            Modify Route
                        </div>
                    </div>

                    <div class="glass-obsidian px-4 py-3 rounded-xl border border-white/10 shadow-2xl">
                        <div class="text-[8px] text-orange-500 font-bold uppercase tracking-widest mb-1">Ops Intelligence</div>
                        <div class="text-white text-[10px] font-mono font-bold" id="coord-hud">30.9010° N, 75.8573° E</div>
                    </div>
                    
                    <button 
                        @click="undoLastWaypoint()" 
                        x-show="waypoints.length > 0"
                        class="glass-obsidian px-4 py-3 rounded-xl border border-white/10 shadow-2xl flex items-center gap-2 text-zinc-400 hover:text-white transition-all group"
                    >
                        <svg class="w-4 h-4 group-hover:-rotate-45 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                        <span class="text-[10px] font-bold uppercase tracking-widest">Undo Last</span>
                    </button>
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
                            Click map to add waypoints · Drag to adjust
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <form id="route-form" action="{{ route('fleet.routes.update', $route->id) }}" method="POST" style="display: none;">
        @csrf
        @method('PATCH')
        <input type="hidden" name="name" value="">
        <input type="hidden" name="driver_id" value="">
        <input type="hidden" name="vehicle_id" value="">
        <input type="hidden" name="scheduled_for" value="">
        <input type="hidden" name="waypoints" value="">
        <input type="hidden" name="status" value="">
    </form>
</div>

<style>
    .leaflet-container { background: #09090b !important; }
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
    #scheduled_at_input {
        color-scheme: dark !important;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='white'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'%3E%3C/path%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 1.25rem center;
        background-size: 1.25rem;
    }
    #scheduled_at_input::-webkit-calendar-picker-indicator { opacity: 0 !important; cursor: pointer !important; width: 2rem; }
</style>

@push('scripts')
<script>
    function routeBuilder() {
        return {
            map: null,
            name: '{{ $route->name }}',
            driverId: '{{ $route->driver_id }}',
            vehicleId: '{{ $route->vehicle_id }}',
            scheduledFor: '{{ $route->scheduled_for ? \Carbon\Carbon::parse($route->scheduled_for)->format('Y-m-d\TH:i') : '' }}',
            status: '{{ $route->status }}',
            waypoints: @json($route->waypoints),
            markers: [],
            polyline: null,
            totalDistance: 0,
            geofences: @json($geofences),
            
            init() {
                this.$nextTick(() => {
                    this.initMap();
                    this.renderGeofences();
                    if (this.waypoints.length > 0) this.renderOnMap();
                });
            },

            initMap() {
                const center = this.waypoints.length > 0 ? [this.waypoints[0].lat, this.waypoints[0].lng] : [30.9010, 75.8573];
                this.map = L.map('route-map', { zoomControl: false, attributionControl: false }).setView(center, 13);
                L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png').addTo(this.map);

                this.map.on('click', (e) => this.addWaypoint(e.latlng.lat, e.latlng.lng));
                this.map.on('move', () => {
                    const center = this.map.getCenter();
                    document.getElementById('coord-hud').innerText = `${center.lat.toFixed(4)}° N, ${center.lng.toFixed(4)}° E`;
                });
            },

            renderGeofences() {
                this.geofences.forEach(gf => {
                    let coords = [];
                    if (typeof gf.area === 'string') coords = JSON.parse(gf.area);
                    else if (gf.area && gf.area.coordinates) coords = gf.area.coordinates[0].map(p => [p[1], p[0]]);

                    if (coords.length > 0) {
                        const color = gf.type === 'restricted' ? '#f43f5e' : (gf.type === 'depot' ? '#10b981' : '#3b82f6');
                        L.polygon(coords, { color, weight: 2, fillColor: color, fillOpacity: 0.1 }).addTo(this.map);
                    }
                });
            },

            addWaypoint(lat, lng) {
                const order = this.waypoints.length + 1;
                this.waypoints = [...this.waypoints, { id: Date.now(), lat, lng, label: `Stop ${order}`, order, reached_at: null }];
                this.renderOnMap();
            },

            removeWaypoint(index) {
                this.waypoints.splice(index, 1);
                this.waypoints.forEach((wp, i) => {
                    if (wp.label.startsWith('Stop ')) wp.label = `Stop ${i + 1}`;
                    wp.order = i + 1;
                });
                this.renderOnMap();
            },

            undoLastWaypoint() { if (this.waypoints.length > 0) this.removeWaypoint(this.waypoints.length - 1); },

            moveWaypoint(index, direction) {
                const newIndex = index + direction;
                if (newIndex < 0 || newIndex >= this.waypoints.length) return;
                const temp = this.waypoints[index];
                this.waypoints[index] = this.waypoints[newIndex];
                this.waypoints[newIndex] = temp;
                this.waypoints.forEach((wp, i) => {
                    if (wp.label.startsWith('Stop ')) wp.label = `Stop ${i + 1}`;
                    wp.order = i + 1;
                });
                this.renderOnMap();
            },

            renderOnMap() {
                this.markers.forEach(m => this.map.removeLayer(m));
                this.markers = [];
                if (this.polyline) this.map.removeLayer(this.polyline);

                const latlngs = this.waypoints.map((wp, index) => {
                    const icon = L.divIcon({ className: 'waypoint-icon', html: `<div class="waypoint-marker">${index + 1}</div>`, iconSize: [32, 32], iconAnchor: [16, 16] });
                    const marker = L.marker([wp.lat, wp.lng], { icon, draggable: true }).addTo(this.map).on('dragend', (e) => {
                        const pos = e.target.getLatLng();
                        this.waypoints[index].lat = pos.lat;
                        this.waypoints[index].lng = pos.lng;
                        this.renderOnMap();
                    });
                    this.markers.push(marker);
                    return [wp.lat, wp.lng];
                });

                if (latlngs.length > 1) {
                    this.polyline = L.polyline(latlngs, { color: '#ff8a00', weight: 4, opacity: 0.6, dashArray: '10, 10' }).addTo(this.map);
                    let dist = 0;
                    for (let i = 0; i < latlngs.length - 1; i++) dist += this.map.distance(L.latLng(latlngs[i]), L.latLng(latlngs[i+1]));
                    this.totalDistance = (dist / 1000).toFixed(1);
                } else this.totalDistance = 0;
            },

            saveRoute() {
                if (!this.name) return alert('Please enter a route name.');
                if (this.waypoints.length < 2) return alert('Please add at least 2 waypoints.');
                
                const form = document.getElementById('route-form');
                form.querySelector('[name="name"]').value = this.name;
                form.querySelector('[name="driver_id"]').value = this.driverId;
                form.querySelector('[name="vehicle_id"]').value = this.vehicleId;
                form.querySelector('[name="scheduled_for"]').value = this.scheduledFor;
                form.querySelector('[name="waypoints"]').value = JSON.stringify(this.waypoints);
                form.querySelector('[name="status"]').value = this.status;
                form.submit();
            }
        }
    }
</script>
@endpush
@endsection
