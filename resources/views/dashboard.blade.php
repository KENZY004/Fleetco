@extends('layouts.app')

@section('content')
<div class="h-full flex overflow-hidden -m-6" x-data="fleetDashboard()" x-init="init()">
    
    <!-- Left Panel: Vehicle List & Details -->
    <div class="w-[360px] border-r border-white/5 bg-[#09090b] flex flex-col shrink-0 relative z-10">
        
        <!-- Panel Header -->
        <div class="p-4 border-b border-white/5">
            <h2 class="text-[11px] font-black uppercase tracking-widest text-white mb-3">Nearby</h2>
            <!-- Search -->
            <div class="relative mb-3">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-3.5 w-3.5 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" class="block w-full pl-9 pr-3 py-2 border border-white/5 rounded-lg bg-white/5 text-zinc-300 placeholder-zinc-600 focus:outline-none focus:border-primary/30 text-[10px] font-mono transition-all" placeholder="Locate vehicles, places on map...">
            </div>
            <!-- Filter Tabs -->
            <div class="flex gap-2 flex-wrap">
                <button class="px-3 py-1.5 text-[8px] font-bold uppercase tracking-widest bg-primary/10 text-primary border border-primary/20 rounded-full">Vehicles</button>
                <button class="px-3 py-1.5 text-[8px] font-bold uppercase tracking-widest text-zinc-500 border border-white/5 rounded-full hover:text-white transition-colors">Places</button>
                <button class="px-3 py-1.5 text-[8px] font-bold uppercase tracking-widest text-zinc-500 border border-white/5 rounded-full hover:text-white transition-colors">Geofences</button>
            </div>
        </div>

        <!-- Vehicle Count -->
        <div class="px-4 py-2 border-b border-white/5 flex justify-between items-center bg-white/[0.02]">
            <span class="text-[9px] font-mono text-primary" x-text="vehicles.length + ' results'"></span>
            <span class="text-[9px] font-mono text-zinc-500">↕ Distance</span>
        </div>

        <!-- Vehicle List -->
        <div class="flex-1 overflow-y-auto custom-scrollbar divide-y divide-white/[0.03]">
            <template x-for="vehicle in vehicles" :key="vehicle.id">
                <div @click="selectVehicle(vehicle)" class="px-4 py-3 hover:bg-white/[0.03] cursor-pointer transition-all flex items-center gap-3" :class="selectedVehicle?.id == vehicle.id ? 'bg-primary/5 border-l-2 border-primary' : 'border-l-2 border-transparent'">
                    <div class="w-8 h-8 rounded-lg bg-primary/10 border border-primary/20 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="text-[10px] font-bold text-white uppercase tracking-wider truncate" x-text="vehicle.name"></h4>
                        <div class="flex items-center gap-2 mt-0.5">
                            <span class="text-[8px] font-mono text-zinc-600" x-text="vehicle.license_plate"></span>
                            <span class="text-[8px] font-mono text-primary" x-text="parseFloat(vehicle.current_odometer || 0).toFixed(1) + ' km'"></span>
                        </div>
                    </div>
                    <div class="flex flex-col items-end gap-1">
                        <div :class="isVehicleOnline(vehicle) ? 'bg-emerald-500' : 'bg-zinc-700'" class="h-1.5 w-1.5 rounded-full"></div>
                        <span class="text-[7px] font-mono" :class="isVehicleOnline(vehicle) ? 'text-emerald-500' : 'text-zinc-600'" x-text="isVehicleOnline(vehicle) ? 'Active' : 'Offline'"></span>
                    </div>
                </div>
            </template>
            <div x-show="vehicles.length === 0" class="p-8 text-center">
                <p class="text-[10px] text-zinc-500">No vehicles found in this area.</p>
            </div>
        </div>

        <!-- Selected Vehicle Detail Panel (slides up) -->
        <div x-show="selectedVehicle" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0" class="border-t border-white/5 bg-[#0a0a0c] p-4 space-y-4 max-h-[45%] overflow-y-auto custom-scrollbar">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-[8px] text-zinc-500 uppercase font-black tracking-widest mb-0.5">Selected Unit</div>
                    <h3 class="text-sm font-bold text-white tracking-widest uppercase" x-text="selectedVehicle?.name"></h3>
                </div>
                <button @click="selectedVehicle = null" class="text-zinc-500 hover:text-white transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="grid grid-cols-3 gap-2 mb-4">
                <div class="p-2.5 rounded-lg bg-white/5 border border-white/5 overflow-hidden">
                    <div class="text-[7px] text-zinc-500 uppercase font-bold tracking-widest mb-1">Speed</div>
                    <div class="text-sm font-black text-white truncate" x-text="parseFloat(selectedVehicle?.telematics_logs?.[0]?.speed || 0).toFixed(1) + ' km/h'"></div>
                </div>
                <div class="p-2.5 rounded-lg bg-white/5 border border-white/5 overflow-hidden">
                    <div class="text-[7px] text-zinc-500 uppercase font-bold tracking-widest mb-1">Distance</div>
                    <div class="text-sm font-black text-white truncate" x-text="parseFloat(haversineDistance || 0).toFixed(1) + ' km'"></div>
                </div>
                <div class="p-2.5 rounded-lg bg-white/5 border border-white/5 overflow-hidden">
                    <div class="text-[7px] text-zinc-500 uppercase font-bold tracking-widest mb-1">Odometer</div>
                    <div class="text-sm font-black text-primary truncate" x-text="parseFloat(selectedVehicle?.current_odometer || 0).toFixed(1) + ' km'"></div>
                </div>
            </div>
            <div class="space-y-2">
                <button @click="visualiseMission()" class="w-full py-2.5 bg-white text-black rounded-lg text-[9px] font-black uppercase tracking-widest hover:bg-zinc-200 transition-all">Visualise Mission</button>
                <button @click="copyTrackingLink(selectedVehicle)" class="w-full py-2.5 border border-white/10 text-white rounded-lg text-[9px] font-black uppercase tracking-widest hover:bg-white/5 transition-all">Share Link</button>
                <a :href="selectedVehicle ? '/vehicles/' + selectedVehicle.id + '/track' : '#'" target="_blank" class="block w-full py-2.5 border border-primary/30 text-primary rounded-lg text-[9px] font-black uppercase tracking-widest hover:bg-primary/5 text-center">Neural Link</a>
            </div>
        </div>
    </div>

    <!-- Right: Full Map -->
    <div class="flex-1 relative">
        <!-- Map HUD Overlay -->
        <div class="absolute bottom-4 right-4 z-[1000] glass-obsidian px-4 py-3 rounded-xl border border-white/10 pointer-events-none">
            <div class="text-[8px] text-primary uppercase font-black tracking-[0.4em] mb-0.5">Spatial Hub</div>
            <div class="text-sm font-bold text-white tracking-tight" x-text="viewportCoords"></div>
        </div>

        <!-- Map Options Button -->
        <div class="absolute bottom-4 left-14 z-[1000]">
            <div class="glass-obsidian px-3 py-2 rounded-lg border border-white/10 flex items-center gap-2 cursor-pointer hover:border-primary/30 transition-all">
                <svg class="w-3.5 h-3.5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-widest">Map Options</span>
            </div>
        </div>

        <!-- Alerts Badge (floating on map) -->
        <div class="absolute top-4 right-4 z-[1000]" x-show="issues.length > 0">
            <div class="glass-obsidian px-3 py-2 rounded-lg border border-red-500/20 flex items-center gap-2">
                <div class="h-1.5 w-1.5 rounded-full bg-red-500 animate-pulse"></div>
                <span class="text-[9px] font-bold text-red-400 uppercase tracking-widest" x-text="issues.length + ' Alerts'"></span>
            </div>
        </div>

        <!-- Leaflet Map -->
        <div id="map" class="absolute inset-0 z-0"></div>
    </div>
</div>

@push('scripts')
<script>
    function fleetDashboard() {
        return {
            map: null, markers: {}, selectedVehicle: null,
            vehicles: @json($vehicles),
            issues: [], userSettings: @json($userSettings ?? []),
            viewportCoords: '00.0000° N, 00.0000° E',
            haversineDistance: '0.0',
            playbackPolyline: null, playbackMarker: null,
            isVisualising: false, playbackIndex: 0, playbackPath: [],
            now: Date.now(),

            init() {
                this.initMap();
                this.initSockets();
                this.updateMarkers();
                this.fetchGeofences();
                
                // Keep the 'now' timer fresh for status checks
                setInterval(() => {
                    this.now = Date.now();
                }, 10000);

                this.$nextTick(() => {
                    this.fetchIssues();
                    this.fetchLandmarks();
                });
            },

            initMap() {
                this.map = L.map('map', { zoomControl: false, attributionControl: false }).setView([31.3260, 75.5762], 12);
                L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', { maxZoom: 19 }).addTo(this.map);
                
                const drawItems = new L.FeatureGroup();
                this.map.addLayer(drawItems);
                
                const drawControl = new L.Control.Draw({
                    edit: { featureGroup: drawItems },
                    draw: { polygon: true, polyline: false, rectangle: false, circle: false, marker: false, circlemarker: false }
                });
                this.map.addControl(drawControl);

                this.map.on('draw:created', async (e) => {
                    const layer = e.layer;
                    const name = prompt("Enter Geofence Name:", "Zone " + (Date.now() % 1000));
                    if (!name) return;

                    const coordinates = layer.getLatLngs()[0].map(ll => ({ lat: ll.lat, lng: ll.lng }));
                    
                    const res = await fetch('/api/geofences', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
                        body: JSON.stringify({ name, coordinates })
                    });

                    if (res.ok) {
                        drawItems.addLayer(layer);
                        alert("Geofence Synced.");
                    }
                });
            },

            async fetchGeofences() {
                const res = await fetch('/api/geofences');
                if (res.ok) {
                    const geofences = await res.json();
                    geofences.forEach(gf => {
                        // Assuming gf.area is a GeoJSON-like structure from Magellan
                        // For simplicity, we expect the points in the area
                        // This logic might need refinement based on Magellan output
                    });
                }
            },

            isVehicleOnline(vehicle) {
                if (!vehicle.telematics_logs || vehicle.telematics_logs.length === 0) return false;
                const lastLog = vehicle.telematics_logs[0];
                const capturedAt = new Date(lastLog.captured_at).getTime();
                const diff = (this.now - capturedAt) / 1000; // seconds
                return diff < 60; // Online if last signal was < 60 seconds ago
            },

            async fetchIssues() {
                const res = await fetch('/api/issues', { headers: { 'Accept': 'application/json' } });
                if (res.ok) this.issues = await res.json();
            },

            initMap() {
                if (this.map) return;
                this.map = L.map('map', { zoomControl: true, attributionControl: false }).setView([20.5937, 78.9629], 5);
                L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', { maxZoom: 19 }).addTo(this.map);
                
                this.drawnItems = new L.FeatureGroup();
                this.map.addLayer(this.drawnItems);

                const drawControl = new L.Control.Draw({
                    draw: { polygon: { shapeOptions: { color: '#ef4444' } }, polyline: false, rectangle: false, circle: false, marker: false, circlemarker: false },
                    edit: { featureGroup: this.drawnItems }
                });
                this.map.addControl(drawControl);

                this.map.on(L.Draw.Event.CREATED, (e) => {
                    const layer = e.layer;
                    this.drawnItems.addLayer(layer);
                    const name = prompt("Enter Geofence Name:");
                    if (name) { this.saveLandmark(name, layer.getLatLngs()[0]); }
                    else { this.drawnItems.removeLayer(layer); }
                });

                this.map.on('move', () => {
                    const center = this.map.getCenter();
                    this.viewportCoords = `${center.lat.toFixed(4)}° N, ${center.lng.toFixed(4)}° E`;
                });
                this.updateMarkers();
            },

            async fetchLandmarks() {
                const res = await fetch('/api/landmarks', { headers: { 'Accept': 'application/json' } });
                if (res.ok) {
                    const landmarks = await res.json();
                    landmarks.forEach(lm => {
                        if (lm.area_geojson && lm.area_geojson.type === 'Polygon') {
                            const latLngs = lm.area_geojson.coordinates[0].map(coord => [coord[1], coord[0]]);
                            L.polygon(latLngs, { color: '#ef4444', weight: 2, fillOpacity: 0.1 })
                             .bindPopup(`<b>${lm.name}</b><br>Type: ${lm.type}`)
                             .addTo(this.drawnItems);
                        }
                    });
                }
            },

            async saveLandmark(name, latLngs) {
                const coordinates = latLngs.map(ll => ({ lat: ll.lat, lng: ll.lng }));
                const res = await fetch('/api/landmarks', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
                    body: JSON.stringify({ name: name, type: 'restricted', coordinates: coordinates })
                });
                if (res.ok) alert('Geofence deployed successfully.');
                else alert('Error deploying geofence.');
            },

            updateMarkers() {
                this.vehicles.forEach(vehicle => {
                    if (vehicle.telematics_logs?.length > 0) {
                        const log = vehicle.telematics_logs[0];
                        const coords = [log.lat, log.lng];
                        if (this.markers[vehicle.id]) { this.markers[vehicle.id].setLatLng(coords); }
                        else {
                            const icon = L.divIcon({
                                className: 'custom-icon',
                                html: `<div class="relative flex items-center justify-center"><div class="absolute h-6 w-6 rounded-full bg-primary opacity-20 animate-pulse"></div><div class="h-2 w-2 rounded-full bg-primary shadow-lg border border-white/20"></div></div>`,
                                iconSize: [24, 24], iconAnchor: [12, 12]
                            });
                            this.markers[vehicle.id] = L.marker(coords, { icon }).addTo(this.map).on('click', () => this.selectVehicle(vehicle));
                        }
                    }
                });
            },

            selectVehicle(vehicle) {
                this.selectedVehicle = vehicle;
                if (vehicle.telematics_logs?.length > 0) {
                    const log = vehicle.telematics_logs[0];
                    this.map.flyTo([log.lat, log.lng], 14, { duration: 1.5 });
                    this.calculateHaversine([log.lat, log.lng]);
                }
            },

            copyTrackingLink(vehicle) {
                const url = window.location.origin + '/track/' + vehicle.tracking_hash;
                navigator.clipboard.writeText(url).then(() => alert('Link Copied.'));
            },

            calculateHaversine(coords) {
                const center = [19.0760, 72.8777], R = 6371;
                const dLat = (coords[0] - center[0]) * Math.PI / 180;
                const dLon = (coords[1] - center[1]) * Math.PI / 180;
                const a = Math.sin(dLat/2)**2 + Math.cos(center[0]*Math.PI/180) * Math.cos(coords[0]*Math.PI/180) * Math.sin(dLon/2)**2;
                let dist = R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
                if (this.userSettings.units === 'miles') dist *= 0.621371;
                this.haversineDistance = dist.toFixed(2);
            },

            initSockets() {
                if (!window.Echo) return;
                window.Echo.channel('fleet-monitoring')
                .listen('.telematics.updated', (e) => {
                    const vehicle = this.vehicles.find(v => v.id === e.log.vehicle_id);
                    if (vehicle) {
                        const isFirst = !vehicle.telematics_logs || vehicle.telematics_logs.length === 0;
                        vehicle.telematics_logs = [e.log];
                        vehicle.current_odometer = e.current_odometer;
                        this.updateMarkers();
                        if (isFirst) this.selectVehicle(vehicle);
                        else if (this.selectedVehicle?.id === vehicle.id) {
                            this.selectedVehicle = { ...vehicle };
                            this.calculateHaversine([e.log.lat, e.log.lng]);
                        }
                    }
                })
                .listen('.issue.created', (e) => { this.issues.unshift(e.issue); });
            },

            async visualiseMission() {
                if (!this.selectedVehicle) return;
                this.clearPlayback();
                this.isVisualising = true;
                const res = await fetch(`/api/vehicles/${this.selectedVehicle.id}/playback`);
                const data = await res.json();
                this.playbackPath = data.path;
                if (this.playbackPath.length < 2) { alert("No mission data."); this.isVisualising = false; return; }
                const latlngs = this.playbackPath.map(p => [p.lat, p.lng]);
                this.playbackPolyline = L.polyline(latlngs, { color: '#ff8a00', weight: 2, opacity: 0.5, dashArray: '5, 5' }).addTo(this.map);
                this.map.fitBounds(this.playbackPolyline.getBounds());
                this.playbackIndex = 0;
                this.animatePlayback();
            },

            animatePlayback() {
                if (!this.isVisualising || this.playbackIndex >= this.playbackPath.length) { this.isVisualising = false; return; }
                const coords = [this.playbackPath[this.playbackIndex].lat, this.playbackPath[this.playbackIndex].lng];
                if (!this.playbackMarker) this.playbackMarker = L.marker(coords).addTo(this.map);
                else this.playbackMarker.setLatLng(coords);
                this.map.panTo(coords);
                this.playbackIndex++;
                setTimeout(() => this.animatePlayback(), 100);
            },

            clearPlayback() {
                this.isVisualising = false;
                if (this.playbackPolyline) this.map.removeLayer(this.playbackPolyline);
                if (this.playbackMarker) this.map.removeLayer(this.playbackMarker);
            }
        }
    }
</script>
@endpush
@endsection
