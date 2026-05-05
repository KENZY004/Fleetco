@extends('layouts.app')

@section('content')
<div class="h-[calc(100vh-12rem)] flex flex-col gap-6" x-data="tripReplay()">
    
    {{-- Header --}}
    <div class="flex items-center justify-between px-2">
        <div class="flex items-center gap-6">
            <a href="{{ route('dashboard') }}" class="p-3 bg-white/5 border border-white/10 rounded-2xl text-zinc-500 hover:text-white transition-all">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <div class="text-[10px] font-bold text-primary uppercase tracking-widest mb-1">Trip Analysis</div>
                <h1 class="text-2xl font-bold text-white tracking-tight">Mission Replay: {{ $trip->vehicle->name }}</h1>
            </div>
        </div>
        
        <div class="flex gap-4">
            <div class="px-6 py-3 bg-white/5 border border-white/10 rounded-2xl flex items-center gap-4">
                <div class="text-[10px] text-zinc-500 uppercase font-bold">Total Distance</div>
                <div class="text-lg font-bold text-white">{{ number_format($trip->distance, 2) }} KM</div>
            </div>
            <div class="px-6 py-3 bg-white/5 border border-white/10 rounded-2xl flex items-center gap-4">
                <div class="text-[10px] text-zinc-500 uppercase font-bold">Avg Speed</div>
                <div class="text-lg font-bold text-white">{{ number_format($trip->average_speed, 1) }} KM/H</div>
            </div>
        </div>
    </div>

    <div class="flex-1 grid grid-cols-12 gap-6 overflow-hidden">
        {{-- Map View --}}
        <div class="col-span-12 lg:col-span-9 fleetco-card rounded-[2.5rem] relative overflow-hidden border border-white/5">
            <div id="trip-map" class="absolute inset-0 z-0"></div>
            
            {{-- Replay Controls --}}
            <div class="absolute bottom-8 left-1/2 -translate-x-1/2 z-10 flex items-center gap-4 px-6 py-4 bg-black/80 backdrop-blur-xl border border-white/10 rounded-full shadow-2xl">
                <button @click="togglePlayback()" class="p-3 bg-primary text-black rounded-full hover:scale-105 transition-all shadow-lg">
                    <template x-if="!isPlaying">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                    </template>
                    <template x-if="isPlaying">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>
                    </template>
                </button>
                <div class="flex flex-col min-w-[150px]">
                    <div class="text-[9px] text-zinc-500 uppercase font-bold tracking-widest mb-1">Playback Progress</div>
                    <div class="h-1 bg-white/10 rounded-full overflow-hidden">
                        <div class="h-full bg-primary" :style="'width: ' + progress + '%'"></div>
                    </div>
                </div>
                <div class="text-xs font-mono text-zinc-400" x-text="currentTime"></div>
            </div>
        </div>

        {{-- Incident Ledger --}}
        <div class="col-span-12 lg:col-span-3 fleetco-card rounded-[2.5rem] overflow-hidden flex flex-col border border-white/5">
            <div class="p-6 border-b border-white/5 bg-white/[0.02]">
                <h3 class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">In-Trip Incidents</h3>
            </div>
            <div class="flex-1 overflow-y-auto p-6 space-y-4 custom-scrollbar">
                @forelse($alerts as $alert)
                    <div class="p-5 rounded-2xl bg-white/5 border border-white/5 hover:border-red-500/30 transition-all cursor-pointer" @click="jumpToTime('{{ $alert->occurred_at->toIso8601String() }}')">
                        <div class="flex items-center gap-4 mb-3">
                            <div class="h-8 w-8 rounded-lg bg-red-500/10 flex items-center justify-center text-red-500">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            </div>
                            <div class="text-xs font-bold text-white uppercase tracking-wider">
                                {{ $alert->type === 'speeding' ? 'Speeding' : 'Geofence Breach' }}
                            </div>
                        </div>
                        <p class="text-[10px] text-zinc-500 font-medium">
                            {{ $alert->type === 'speeding' ? 'Detected at ' . $alert->details['speed'] . ' KM/H' : ($alert->details['breach_type'] === 'route_deviation' ? 'Deviated from ' . $alert->details['landmark_name'] : 'Entered Restricted Area') }}
                        </p>
                        <div class="mt-3 text-[9px] font-mono text-zinc-600 uppercase">{{ $alert->occurred_at->format('H:i:s') }}</div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center py-20 text-center opacity-30">
                        <svg class="h-8 w-8 text-zinc-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div class="text-[9px] font-bold uppercase tracking-widest">Clean Mission</div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script>
    function tripReplay() {
        return {
            map: null,
            logs: @json($logs),
            alerts: @json($alerts),
            currentIndex: 0,
            isPlaying: false,
            progress: 0,
            currentTime: '00:00:00',
            playbackMarker: null,
            routePath: null,

            init() {
                this.map = L.map('trip-map', {
                    zoomControl: false,
                    attributionControl: false
                }).setView([0,0], 2);

                L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
                    maxZoom: 19
                }).addTo(this.map);

                const coords = this.logs.map(log => [log.location.coordinates[1], log.location.coordinates[0]]);
                
                if (coords.length > 0) {
                    this.routePath = L.polyline(coords, {
                        color: '#ff8a00',
                        weight: 3,
                        opacity: 0.5,
                        dashArray: '5, 10'
                    }).addTo(this.map);

                    this.map.fitBounds(this.routePath.getBounds(), { padding: [50, 50] });

                    // Add markers for alerts
                    this.alerts.forEach(alert => {
                        const icon = L.divIcon({
                            className: 'alert-ping',
                            html: `<div class="h-3 w-3 bg-red-500 rounded-full border-2 border-white shadow-[0_0_10px_red]"></div>`,
                            iconSize: [12, 12]
                        });
                        L.marker([alert.telematics_log.location.coordinates[1], alert.telematics_log.location.coordinates[0]], { icon })
                            .addTo(this.map)
                            .bindTooltip(alert.type.toUpperCase());
                    });
                }
            },

            togglePlayback() {
                this.isPlaying = !this.isPlaying;
                if (this.isPlaying) this.step();
            },

            step() {
                if (!this.isPlaying || this.currentIndex >= this.logs.length) {
                    this.isPlaying = false;
                    return;
                }

                const log = this.logs[this.currentIndex];
                const coords = [log.location.coordinates[1], log.location.coordinates[0]];

                if (!this.playbackMarker) {
                    this.playbackMarker = L.circleMarker(coords, {
                        radius: 8,
                        color: '#ff8a00',
                        fillColor: '#ff8a00',
                        fillOpacity: 1
                    }).addTo(this.map);
                } else {
                    this.playbackMarker.setLatLng(coords);
                }

                this.map.panTo(coords);
                this.progress = (this.currentIndex / this.logs.length) * 100;
                this.currentTime = new Date(log.captured_at).toLocaleTimeString();
                
                this.currentIndex++;
                setTimeout(() => this.step(), 100);
            },

            jumpToTime(isoString) {
                const targetTime = new Date(isoString).getTime();
                const index = this.logs.findIndex(log => new Date(log.captured_at).getTime() >= targetTime);
                if (index !== -1) {
                    this.currentIndex = index;
                    this.isPlaying = false;
                    this.step();
                }
            }
        }
    }
</script>

<style>
    .alert-ping {
        filter: drop-shadow(0 0 5px rgba(239, 68, 68, 0.5));
    }
</style>
@endsection
