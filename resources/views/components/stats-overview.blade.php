@props(['stats'])

<div class="grid grid-cols-2 lg:grid-cols-5 gap-6">
    <div class="fleetco-card p-6 rounded-3xl flex flex-col gap-2 relative overflow-hidden group">
        <div class="text-[10px] text-zinc-500 uppercase font-black tracking-[0.3em] relative z-10">Sovereign Assets</div>
        <div class="text-4xl font-extrabold text-white tracking-tighter relative z-10">{{ $stats['totalVehicles'] ?? 0 }}</div>
        <div class="absolute -right-4 -bottom-4 opacity-5 scale-150 rotate-12 group-hover:rotate-0 transition-transform duration-700">
            <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
        </div>
    </div>
    
    <div class="fleetco-card p-6 rounded-3xl flex flex-col gap-2 relative overflow-hidden group">
        <div class="text-[10px] text-primary uppercase font-black tracking-[0.3em] relative z-10">Neural Sync: Live</div>
        <div class="text-4xl font-extrabold text-white tracking-tighter relative z-10">{{ $stats['activeVehicles'] ?? 0 }}</div>
        <div class="absolute top-0 right-0 h-1 w-1/2 bg-primary"></div>
    </div>

    <div class="fleetco-card p-6 rounded-3xl flex flex-col gap-2 relative overflow-hidden group">
        <div class="text-[10px] text-zinc-500 uppercase font-black tracking-[0.3em] relative z-10">Dormant Nodes</div>
        <div class="text-4xl font-extrabold text-white tracking-tighter relative z-10">{{ $stats['idleVehicles'] ?? 0 }}</div>
    </div>

    <div class="vengo-card p-6 rounded-3xl flex flex-col gap-2 relative overflow-hidden group border-primary/20">
        <div class="text-[10px] text-zinc-500 uppercase font-black tracking-[0.3em] relative z-10">Cumulative Inertia (KM)</div>
        <div class="text-4xl font-extrabold text-white tracking-tighter relative z-10">{{ number_format($stats['totalDistance'] ?? 0, 0) }}</div>
    </div>

    <div class="fleetco-card p-6 rounded-3xl flex flex-col gap-2 relative overflow-hidden group">
        <div class="text-[10px] text-rose-500 uppercase font-black tracking-[0.3em] relative z-10">Risk Signatures</div>
        <div class="text-4xl font-extrabold text-white tracking-tighter relative z-10">{{ $stats['activeAnomalies'] ?? 0 }}</div>
    </div>
</div>
