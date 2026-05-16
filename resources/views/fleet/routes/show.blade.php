@extends('layouts.app')

@section('content')
<div class="space-y-8">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="text-[10px] font-bold tracking-widest text-orange-500 uppercase mb-2">Route Management</div>
            <h1 class="font-heading text-3xl font-bold tracking-tight">{{ $route->name }}</h1>
            <p class="text-zinc-500 text-sm mt-1">Route Details and Waypoints</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('fleet.routes.index') }}" class="px-6 py-3 bg-white/5 border border-white/10 rounded-full text-[11px] font-bold uppercase tracking-wider text-zinc-400 hover:text-white transition-all">
                Back to List
            </a>
            @if($route->status !== 'completed')
            @if($route->status !== 'completed')
            <a href="{{ route('fleet.routes.edit', $route->id) }}" class="px-6 py-3 bg-primary text-black rounded-full text-[11px] font-bold uppercase tracking-wider hover:bg-white/90 transition-all shadow-lg active:scale-95">
                Edit Route
            </a>
            @endif
            @endif
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <x-fleet-card>
            <div class="text-[10px] text-zinc-500 uppercase font-bold tracking-widest mb-1">Status</div>
            <div class="flex items-center gap-2">
                <div class="h-2 w-2 rounded-full {{ $route->status === 'active' ? 'bg-emerald-500 shadow-[0_0_8px_#10b981]' : 'bg-zinc-600' }}"></div>
                <div class="text-lg font-bold text-white uppercase">{{ $route->status }}</div>
            </div>
        </x-fleet-card>
        
        <x-fleet-card>
            <div class="text-[10px] text-zinc-500 uppercase font-bold tracking-widest mb-1">Assigned Driver</div>
            <div class="text-lg font-bold text-white">{{ $route->driver ? $route->driver->name : 'Unassigned' }}</div>
        </x-fleet-card>

        <x-fleet-card>
            <div class="text-[10px] text-zinc-500 uppercase font-bold tracking-widest mb-1">Vehicle</div>
            <div class="text-lg font-bold text-white">{{ $route->vehicle ? $route->vehicle->license_plate : '—' }}</div>
        </x-fleet-card>

        <x-fleet-card>
            <div class="text-[10px] text-zinc-500 uppercase font-bold tracking-widest mb-1">Waypoints</div>
            <div class="text-lg font-bold text-white">{{ count($route->waypoints) }} Stops</div>
        </x-fleet-card>
    </div>

    <div class="grid grid-cols-12 gap-8">
        {{-- Map View --}}
        <div class="col-span-12 lg:col-span-8">
            <div class="glass-obsidian rounded-[2rem] border border-white/5 overflow-hidden h-[600px] relative">
                <div id="route-map" class="absolute inset-0 z-0"></div>
            </div>
        </div>

        {{-- Waypoint List --}}
        <div class="col-span-12 lg:col-span-4">
            <div class="glass-obsidian rounded-[2rem] border border-white/5 p-8 h-[600px] flex flex-col">
                <h3 class="font-heading text-lg font-bold text-white mb-6">Waypoint Sequence</h3>
                <div class="flex-1 overflow-y-auto space-y-6 pr-2 custom-scrollbar">
                    @foreach($route->waypoints as $index => $wp)
                    <div class="flex gap-4 relative group">
                        @if(!$loop->last)
                        <div class="absolute left-4 top-10 bottom-[-24px] w-0.5 bg-white/5 group-hover:bg-primary/20 transition-colors"></div>
                        @endif
                        
                        <div class="w-8 h-8 rounded-full bg-white/5 border border-white/10 flex items-center justify-center text-[10px] font-bold text-zinc-500 group-hover:border-primary group-hover:text-primary transition-all shrink-0">
                            {{ $index + 1 }}
                        </div>
                        
                        <div class="flex-1">
                            <div class="text-sm font-bold text-white">{{ $wp['name'] ?? 'Waypoint ' . ($index + 1) }}</div>
                            <div class="text-[10px] text-zinc-500 mt-1 uppercase tracking-wider">
                                Lat: {{ number_format($wp['lat'], 4) }} · Lng: {{ number_format($wp['lng'], 4) }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const waypoints = @json($route->waypoints);
        
        const map = L.map('route-map', {
            zoomControl: false,
            attributionControl: false
        }).setView([waypoints[0].lat, waypoints[0].lng], 13);

        L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
            maxZoom: 19
        }).addTo(map);

        const coords = waypoints.map(wp => [wp.lat, wp.lng]);
        
        // Draw Route Line
        L.polyline(coords, {
            color: '#ff8a00',
            weight: 4,
            opacity: 0.8,
            dashArray: '10, 10'
        }).addTo(map);

        // Add Markers
        waypoints.forEach((wp, index) => {
            const isFirst = index === 0;
            const isLast = index === waypoints.length - 1;
            
            const color = isFirst ? '#22c55e' : (isLast ? '#ef4444' : '#ff8a00');
            
            const icon = L.divIcon({
                className: '',
                html: `<div style="width:24px;height:24px;background:${color};border:3px solid white;border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;font-size:10px;font-weight:bold;box-shadow:0 0 15px rgba(0,0,0,0.5)">${index + 1}</div>`,
                iconSize: [24, 24],
                iconAnchor: [12, 12]
            });

            L.marker([wp.lat, wp.lng], { icon }).addTo(map)
                .bindTooltip(wp.name || `Stop ${index + 1}`);
        });

        const bounds = L.latLngBounds(coords);
        map.fitBounds(bounds, { padding: [50, 50] });
    });
</script>
@endpush
@endsection
