@extends('layouts.app')

@section('content')
<div class="h-[calc(100vh-140px)] flex flex-col gap-6" x-data="geofenceBuilder()">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between shrink-0">
        <div>
            <div class="text-[10px] font-bold tracking-widest text-orange-500 uppercase mb-2">Spatial Intelligence</div>
            <h1 class="font-heading text-3xl font-bold tracking-tight">Geofence Builder</h1>
            <p class="text-zinc-500 text-sm mt-1">Draw restricted zones and operational hubs on the map.</p>
        </div>
        
        <div class="flex gap-4">
            <template x-if="points.length > 0">
                <button @click="clearDraft()" class="px-6 py-3 border border-white/10 rounded-full text-[11px] font-bold uppercase tracking-wider text-zinc-400 hover:text-white transition-all">
                    Clear Draft
                </button>
            </template>
            <button 
                @click="saveGeofence()" 
                :disabled="points.length < 3"
                class="flex items-center gap-3 px-8 py-3 bg-white text-black rounded-full text-[11px] font-bold uppercase tracking-wider hover:bg-white/90 transition-all shadow-lg active:scale-95 disabled:opacity-30 disabled:cursor-not-allowed"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                Save Geofence
            </button>
        </div>
    </div>

    <div class="flex-1 flex gap-6 min-h-0">
        {{-- Map Canvas --}}
        <div class="flex-1 glass-obsidian rounded-[2.5rem] overflow-hidden border border-white/10 relative">
            <div id="geofenceMap" class="w-full h-full z-10"></div>
            
            {{-- Instructions Overlay --}}
            <div class="absolute bottom-8 left-1/2 -translate-x-1/2 z-[1000] px-6 py-3 bg-black/80 backdrop-blur-md rounded-full border border-white/10 text-[10px] font-bold uppercase tracking-widest text-zinc-400 flex items-center gap-4">
                <span :class="points.length === 0 ? 'text-primary' : ''">1. Click Map to drop points</span>
                <span class="opacity-20">|</span>
                <span :class="points.length >= 3 ? 'text-primary' : ''">2. Form a shape (min 3 points)</span>
                <span class="opacity-20">|</span>
                <span :class="isSaving ? 'text-primary' : ''">3. Save Zone</span>
            </div>
        </div>

        {{-- Geofence List / Sidebar --}}
        <div class="w-80 flex flex-col gap-6 shrink-0 overflow-y-auto custom-scrollbar pr-2">
            @forelse($geofences as $gf)
                <x-fleet-card class="p-6 relative group">
                    <div class="flex items-center justify-between mb-4">
                        @php
                            $badgeType = [
                                'restricted' => 'danger',
                                'depot' => 'success',
                                'client' => 'info',
                                'optimized_route' => 'warning',
                            ][$gf['type']] ?? 'neutral';
                        @endphp
                        <x-badge :type="$badgeType">{{ $gf['type'] }}</x-badge>

                        <form method="POST" action="{{ route('geofences.destroy', $gf['id']) }}" onsubmit="return confirm('Delete geofence?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-zinc-600 hover:text-red-500 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                    <h4 class="font-heading text-lg font-bold text-white mb-1">{{ $gf['name'] }}</h4>
                    <div class="flex items-center justify-between mt-2">
                        <div class="text-[10px] text-zinc-500 font-medium">Vertices: {{ count($gf['area']) }}</div>
                        <div class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-[9px] text-zinc-400 font-bold uppercase tracking-widest">Active Tracking</span>
                        </div>
                    </div>
                </x-fleet-card>
            @empty
                <div class="p-12 text-center border-2 border-dashed border-white/5 rounded-3xl">
                    <div class="text-[10px] text-zinc-600 uppercase font-bold tracking-widest mb-2">No Zones Defined</div>
                    <p class="text-zinc-700 text-xs leading-relaxed">Start clicking the map to define your fleet's operational boundaries.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- SAVE MODAL --}}
    <x-fleet-modal name="isSaving" title="Identify this Geofence" subtitle="Save Zone Definition">
        <form id="saveGeofenceForm" method="POST" action="{{ route('geofences.store') }}" class="space-y-6">
            @csrf
            <input type="hidden" name="coordinates" :value="JSON.stringify(points)">
            
            <div>
                <label class="text-[10px] font-bold uppercase tracking-wider text-zinc-500 block mb-2">Zone Name</label>
                <input type="text" name="name" required placeholder="e.g. Restricted Warehouse A" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-zinc-700 text-sm outline-none focus:border-primary/50 transition-colors">
            </div>
            
            <div>
                <label class="text-[10px] font-bold uppercase tracking-wider text-zinc-500 block mb-2">Zone Classification</label>
                <select name="type" class="w-full bg-zinc-900 border border-white/10 rounded-xl px-4 py-3 text-white text-sm outline-none focus:border-primary/50 transition-colors">
                    <option value="restricted">Restricted (Alert on Entry)</option>
                    <option value="depot">Fleet Depot (Home Base)</option>
                    <option value="client">Client Site (Delivery Zone)</option>
                    <option value="optimized_route">Route Corridor (Alert on Exit)</option>
                </select>
            </div>

            <div class="flex gap-4 pt-4">
                <button type="button" @click="isSaving = false" class="flex-1 py-4 border border-white/10 rounded-full text-[10px] font-bold uppercase tracking-wider text-zinc-400 hover:text-white transition-all">Cancel</button>
                <button type="submit" class="flex-1 py-4 bg-white text-black rounded-full text-[10px] font-bold uppercase tracking-wider hover:bg-white/90 transition-all">Finalize Zone</button>
            </div>
        </form>
    </x-fleet-modal>
</div>
@endsection

@push('scripts')
<script>
    function geofenceBuilder() {
        return {
            map: null,
            points: [],
            markers: [],
            polygon: null,
            isSaving: false,
            existingGeofences: @json($geofences),

            init() {
                this.map = L.map('geofenceMap', {
                    zoomControl: false,
                    attributionControl: false
                }).setView([31.3831, 75.3857], 13);

                L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
                    maxZoom: 20
                }).addTo(this.map);

                // Add zoom control to right
                L.control.zoom({ position: 'topright' }).addTo(this.map);

                // Click to place points
                this.map.on('click', (e) => {
                    if (this.isSaving) return;
                    this.addPoint(e.latlng.lat, e.latlng.lng);
                });

                // Render existing geofences
                this.existingGeofences.forEach(gf => {
                    if (gf.area && gf.area.length > 0) {
                        const color = gf.type === 'restricted' ? '#f43f5e' : (gf.type === 'depot' ? '#10b981' : '#3b82f6');
                        L.polygon(gf.area, {
                            color: color,
                            weight: 2,
                            fillOpacity: 0.1,
                            dashArray: gf.type === 'restricted' ? '5, 10' : null
                        }).addTo(this.map).bindTooltip(gf.name, { permanent: false });
                    }
                });
            },

            addPoint(lat, lng) {
                this.points.push([lat, lng]);
                
                // Add marker
                const marker = L.circleMarker([lat, lng], {
                    radius: 5,
                    color: '#ff8a00',
                    fillColor: '#ff8a00',
                    fillOpacity: 1,
                    weight: 2
                }).addTo(this.map);
                this.markers.push(marker);

                // Update/Draw polygon
                if (this.polygon) this.map.removeLayer(this.polygon);
                if (this.points.length >= 2) {
                    this.polygon = L.polygon(this.points, {
                        color: '#ff8a00',
                        weight: 3,
                        fillOpacity: 0.2,
                        dashArray: '5, 10'
                    }).addTo(this.map);
                }
            },

            clearDraft() {
                this.markers.forEach(m => this.map.removeLayer(m));
                if (this.polygon) this.map.removeLayer(this.polygon);
                this.points = [];
                this.markers = [];
                this.polygon = null;
            },

            saveGeofence() {
                if (this.points.length < 3) return;
                this.isSaving = true;
            }
        }
    }
</script>
@endpush
