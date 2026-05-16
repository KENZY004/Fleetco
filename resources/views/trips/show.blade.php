@extends(auth()->user()->role === 'driver' ? 'driver.layouts.app' : 'layouts.app')

@section('content')
<div class="flex flex-col gap-6 min-h-[calc(100vh-6rem)] pb-24 lg:pb-0" x-data="tripReplay()" x-init="init()">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 shrink-0">
        <div class="flex items-center gap-5">
            <a href="{{ auth()->user()->role === 'driver' ? route('driver.trips') : route('trips.index') }}" class="p-3 bg-white/5 border border-white/10 rounded-2xl text-zinc-500 hover:text-white transition-all shrink-0">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <div class="text-[9px] font-bold text-primary uppercase tracking-widest mb-1">Route Playback</div>
                <h1 class="font-heading text-2xl font-bold text-white tracking-tight">{{ $trip->vehicle->name }}</h1>
                <div class="text-xs text-zinc-500 mt-0.5">
                    {{ $trip->start_time->format('M d, Y · H:i') }}
                    @if($trip->end_time)
                        &nbsp;→&nbsp;{{ $trip->end_time->format('H:i') }}
                    @else
                        &nbsp;— <span class="text-emerald-400">In Progress</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 w-full sm:w-auto">
            <div class="px-4 py-3 bg-white/5 border border-white/10 rounded-2xl text-center">
                <div class="text-[8px] md:text-[9px] text-zinc-500 uppercase font-bold tracking-wider mb-1">Distance</div>
                <div class="text-sm md:text-base font-bold text-white">{{ number_format($trip->distance, 2) }} km</div>
            </div>
            <div class="px-4 py-3 bg-white/5 border border-white/10 rounded-2xl text-center">
                <div class="text-[8px] md:text-[9px] text-zinc-500 uppercase font-bold tracking-wider mb-1">Avg Speed</div>
                <div class="text-sm md:text-base font-bold text-white">{{ number_format($trip->average_speed, 1) }} km/h</div>
            </div>
            <div class="px-4 py-3 bg-white/5 border border-white/10 rounded-2xl text-center">
                <div class="text-[8px] md:text-[9px] text-zinc-500 uppercase font-bold tracking-wider mb-1">Pings</div>
                <div class="text-sm md:text-base font-bold text-white" x-text="logs.length"></div>
            </div>
            <div class="px-4 py-3 bg-white/5 border border-white/10 rounded-2xl text-center">
                <div class="text-[8px] md:text-[9px] text-zinc-500 uppercase font-bold tracking-wider mb-1">Incidents</div>
                <div class="text-sm md:text-base font-bold {{ count($alerts ?? []) > 0 ? 'text-red-400' : 'text-emerald-400' }}">{{ count($alerts ?? []) }}</div>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="flex-1 grid grid-cols-12 gap-4 md:gap-6">

        {{-- Map --}}
        <div class="col-span-12 lg:col-span-9 relative bg-zinc-950 border border-white/5 rounded-[1.5rem] md:rounded-[2rem] overflow-hidden h-[400px] lg:h-auto min-h-[400px]">
            <div id="trip-map" class="absolute inset-0 z-0"></div>

            {{-- No data overlay --}}
            <div x-show="logs.length === 0" class="absolute inset-0 z-10 flex flex-col items-center justify-center gap-4">
                <div class="w-16 h-16 rounded-2xl bg-white/5 flex items-center justify-center text-zinc-700">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                </div>
                <div class="text-zinc-600 text-xs uppercase font-bold tracking-widest">No GPS Data for This Trip</div>
            </div>

            {{-- Speed HUD --}}
            <div x-show="logs.length > 0" class="absolute top-4 left-4 md:top-6 md:left-6 z-10 glass-obsidian px-3 py-2 md:px-5 md:py-4 rounded-xl md:rounded-2xl border border-white/10 pointer-events-none">
                <div class="text-[7px] md:text-[9px] text-zinc-500 uppercase font-bold tracking-widest mb-0.5 md:mb-1">Live Speed</div>
                <div class="font-heading text-lg md:text-2xl font-bold text-white" x-text="currentSpeed + ' km/h'">—</div>
            </div>

            {{-- Playback Controls --}}
            <div x-show="logs.length > 0" class="absolute bottom-4 md:bottom-6 left-4 right-4 md:left-1/2 md:-translate-x-1/2 md:w-auto z-10 flex items-center gap-2 md:gap-5 px-3 py-2 md:px-7 md:py-4 bg-black/80 backdrop-blur-xl border border-white/10 rounded-2xl md:rounded-full shadow-2xl">

                {{-- Rewind --}}
                <button @click="rewind()" class="p-2 text-zinc-500 hover:text-white transition-colors shrink-0">
                    <svg class="w-4 h-4 md:w-5 md:h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M6 6h2v12H6zm3.5 6l8.5 6V6z"/></svg>
                </button>

                {{-- Play/Pause --}}
                <button @click="togglePlayback()" class="w-10 h-10 md:w-12 md:h-12 bg-primary text-black rounded-full flex items-center justify-center hover:scale-105 transition-all shadow-lg shadow-primary/20 shrink-0">
                    <template x-if="!isPlaying">
                        <svg class="h-4 w-4 md:h-5 md:w-5 ml-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                    </template>
                    <template x-if="isPlaying">
                        <svg class="h-4 w-4 md:h-5 md:w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>
                    </template>
                </button>

                {{-- Progress bar --}}
                <div class="flex-1 flex flex-col gap-1 min-w-0 md:min-w-[180px]">
                    <div class="flex justify-between text-[8px] md:text-[9px] font-mono text-zinc-500 mb-0.5">
                        <span x-text="currentTime" class="truncate">—</span>
                        <span x-text="Math.round(progress) + '%'">0%</span>
                    </div>
                    <div class="h-1 md:h-1.5 bg-white/10 rounded-full overflow-hidden cursor-pointer" @click="scrub($event)">
                        <div class="h-full bg-primary transition-all rounded-full" :style="'width:' + progress + '%'"></div>
                    </div>
                </div>

                {{-- Speed control --}}
                <select x-model="playSpeed" class="bg-white/10 border-0 rounded-lg px-2 md:px-3 py-1.5 md:py-2 text-[10px] md:text-xs text-white font-bold outline-none appearance-none cursor-pointer shrink-0">
                    <option value="50" class="bg-zinc-900 text-white">2×</option>
                    <option value="100" class="bg-zinc-900 text-white" selected>1×</option>
                    <option value="200" class="bg-zinc-900 text-white">0.5×</option>
                </select>
            </div>
        </div>

        {{-- Incident Ledger --}}
        <div class="col-span-12 lg:col-span-3 bg-zinc-950 border border-white/5 rounded-[2rem] flex flex-col overflow-hidden">
            <div class="px-6 py-5 border-b border-white/5 shrink-0">
                <div class="text-[9px] font-bold text-zinc-500 uppercase tracking-widest">In-Trip Incidents</div>
                @php $alertsData = $alerts ?? []; @endphp
                @if(count($alertsData) > 0)
                    <div class="text-xs text-red-400 font-bold mt-1">{{ count($alertsData) }} event{{ count($alertsData) > 1 ? 's' : '' }} detected</div>
                @endif
            </div>

            <div class="flex-1 overflow-y-auto p-4 space-y-3">
                @forelse($alertsData as $alert)
                    <div class="p-4 rounded-2xl bg-white/5 border border-white/5 hover:border-red-500/30 transition-all cursor-pointer group"
                         @click="jumpToTime('{{ $alert['occurred_at'] }}')">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="h-8 w-8 rounded-xl shrink-0 flex items-center justify-center
                                {{ $alert['type'] === 'speeding' ? 'bg-orange-500/10 text-orange-500' : 'bg-red-500/10 text-red-500' }}">
                                @if($alert['type'] === 'speeding')
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                @else
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                @endif
                            </div>
                            <div class="text-xs font-bold text-white uppercase tracking-wide">
                                @switch($alert['type'])
                                    @case('speeding') Speeding @break
                                    @case('geofence_breach') Geofence Breach @break
                                    @case('geofence_entry') Geofence Entry @break
                                    @default {{ ucwords(str_replace('_', ' ', $alert['type'])) }}
                                @endswitch
                            </div>
                        </div>
                        <div class="text-[10px] text-zinc-500">
                            @if(!empty($alert['details']['speed']))
                                {{ $alert['details']['speed'] }} km/h detected
                            @elseif(!empty($alert['details']['landmark_name']))
                                Zone: {{ $alert['details']['landmark_name'] }}
                            @endif
                        </div>
                        <div class="mt-2 text-[9px] font-mono text-zinc-700 group-hover:text-zinc-500 transition-colors uppercase">
                            {{ $alert['time'] }}
                            <span class="text-primary ml-2 opacity-0 group-hover:opacity-100 transition-opacity">→ Jump</span>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center py-16 text-center">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 flex items-center justify-center text-emerald-500 mb-3">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div class="text-[9px] font-bold text-emerald-500 uppercase tracking-widest">Clean Mission</div>
                        <div class="text-zinc-700 text-[10px] mt-1">No incidents recorded</div>
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
        currentIndex: 0,
        isPlaying: false,
        progress: 0,
        currentTime: '—',
        currentSpeed: '—',
        playbackMarker: null,
        routePath: null,
        playbackTimer: null,
        playSpeed: 100,

        init() {
            this.map = L.map('trip-map', {
                zoomControl: false,
                attributionControl: false
            }).setView([20, 0], 2);

            L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
                maxZoom: 19
            }).addTo(this.map);

            if (this.logs.length === 0) return;

            const coords = this.logs.map(log => [log.lat, log.lng]);

            // Draw full ghost route
            this.routePath = L.polyline(coords, {
                color: '#ff8a00',
                weight: 3,
                opacity: 0.25,
                dashArray: '6, 10'
            }).addTo(this.map);

            // Draw completed route (will grow during playback)
            this.playedPath = L.polyline([], {
                color: '#ff8a00',
                weight: 3,
                opacity: 0.9
            }).addTo(this.map);

            this.map.fitBounds(this.routePath.getBounds(), { padding: [60, 60] });

            // Start/End markers
            const startIcon = L.divIcon({
                className: '',
                html: `<div style="width:12px;height:12px;background:#22c55e;border:2px solid white;border-radius:50%;box-shadow:0 0 8px rgba(34,197,94,0.6)"></div>`,
                iconSize: [12, 12], iconAnchor: [6, 6]
            });
            const endIcon = L.divIcon({
                className: '',
                html: `<div style="width:12px;height:12px;background:#ef4444;border:2px solid white;border-radius:50%;box-shadow:0 0 8px rgba(239,68,68,0.6)"></div>`,
                iconSize: [12, 12], iconAnchor: [6, 6]
            });

            L.marker(coords[0], { icon: startIcon }).addTo(this.map).bindTooltip('Start');
            L.marker(coords[coords.length - 1], { icon: endIcon }).addTo(this.map).bindTooltip('End');

            // Alert markers
            @foreach($alertsData as $alert)
                @if($alert['lat'] && $alert['lng'])
                L.circleMarker([{{ $alert['lat'] }}, {{ $alert['lng'] }}], {
                    radius: 7,
                    color: '{{ $alert["type"] === "speeding" ? "#f97316" : "#ef4444" }}',
                    fillColor: '{{ $alert["type"] === "speeding" ? "#f97316" : "#ef4444" }}',
                    fillOpacity: 0.9,
                    weight: 2
                }).addTo(this.map)
                 .bindTooltip(`{{ ucwords(str_replace('_', ' ', $alert['type'])) }} · {{ $alert['time'] }}`);
                @endif
            @endforeach
        },

        togglePlayback() {
            if (this.currentIndex >= this.logs.length) {
                this.currentIndex = 0;
                this.playedPath?.setLatLngs([]);
            }
            this.isPlaying = !this.isPlaying;
            if (this.isPlaying) this.step();
        },

        step() {
            if (!this.isPlaying || this.currentIndex >= this.logs.length) {
                this.isPlaying = false;
                return;
            }

            const log = this.logs[this.currentIndex];
            const coords = [log.lat, log.lng];

            if (!this.playbackMarker) {
                const icon = L.divIcon({
                    className: '',
                    html: `<div style="width:16px;height:16px;background:#ff8a00;border:3px solid white;border-radius:50%;box-shadow:0 0 15px rgba(255,138,0,0.8)"></div>`,
                    iconSize: [16, 16], iconAnchor: [8, 8]
                });
                this.playbackMarker = L.marker(coords, { icon }).addTo(this.map);
            } else {
                this.playbackMarker.setLatLng(coords);
            }

            this.playedPath?.addLatLng(coords);
            this.map.panTo(coords, { animate: true, duration: 0.2 });

            this.progress = ((this.currentIndex + 1) / this.logs.length) * 100;
            this.currentTime = log.time;
            this.currentSpeed = Math.round(log.speed);

            this.currentIndex++;
            this.playbackTimer = setTimeout(() => this.step(), parseInt(this.playSpeed));
        },

        rewind() {
            this.isPlaying = false;
            clearTimeout(this.playbackTimer);
            this.currentIndex = 0;
            this.progress = 0;
            this.currentTime = '—';
            this.currentSpeed = '—';
            this.playedPath?.setLatLngs([]);
            if (this.playbackMarker && this.logs.length > 0) {
                this.playbackMarker.setLatLng([this.logs[0].lat, this.logs[0].lng]);
            }
        },

        scrub(event) {
            const rect = event.currentTarget.getBoundingClientRect();
            const ratio = (event.clientX - rect.left) / rect.width;
            this.currentIndex = Math.floor(ratio * this.logs.length);
            this.progress = ratio * 100;
            if (this.logs[this.currentIndex]) {
                this.currentTime = this.logs[this.currentIndex].time;
                this.currentSpeed = Math.round(this.logs[this.currentIndex].speed);
            }
        },

        jumpToTime(isoString) {
            const target = new Date(isoString).getTime();
            const idx = this.logs.findIndex(l => new Date(l.captured_at).getTime() >= target);
            if (idx !== -1) {
                this.currentIndex = idx;
                this.isPlaying = false;
                this.step();
            }
        }
    }
}
</script>

<style>
    #trip-map .leaflet-control-attribution { display: none; }
</style>
@endsection
