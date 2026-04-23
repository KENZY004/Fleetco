@props(['stats'])

<div class="grid grid-cols-2 lg:grid-cols-5 gap-6">
    <div class="fleetco-card p-6 rounded-2xl flex flex-col gap-1 relative overflow-hidden">
        <div class="text-xs text-zinc-500 font-medium tracking-tight">Total Vehicles</div>
        <div class="font-heading text-4xl font-bold text-white tracking-tight">{{ $stats['totalVehicles'] ?? 0 }}</div>
    </div>
    
    <div class="fleetco-card p-6 rounded-2xl flex flex-col gap-1 relative overflow-hidden">
        <div class="text-xs text-zinc-500 font-medium tracking-tight flex items-center gap-2">
            <div class="h-1.5 w-1.5 bg-orange-500 rounded-full animate-pulse"></div>
            Online Now
        </div>
        <div class="font-heading text-4xl font-bold text-white tracking-tight">{{ $stats['activeVehicles'] ?? 0 }}</div>
    </div>

    <div class="fleetco-card p-6 rounded-2xl flex flex-col gap-1 relative overflow-hidden">
        <div class="text-xs text-zinc-500 font-medium tracking-tight">Idle Units</div>
        <div class="font-heading text-4xl font-bold text-white tracking-tight">{{ $stats['idleVehicles'] ?? 0 }}</div>
    </div>

    <div class="fleetco-card p-6 rounded-2xl flex flex-col gap-1 relative overflow-hidden">
        <div class="text-xs text-zinc-500 font-medium tracking-tight">Total Distance</div>
        <div class="flex items-baseline gap-1">
            <div class="font-heading text-4xl font-bold text-white tracking-tight">{{ number_format($stats['totalDistance'] ?? 0, 0) }}</div>
            <span class="text-xs font-bold text-zinc-600 uppercase">km</span>
        </div>
    </div>

    <div class="fleetco-card p-6 rounded-2xl flex flex-col gap-1 relative overflow-hidden border-rose-500/10">
        <div class="text-xs text-zinc-500 font-medium tracking-tight flex items-center gap-2">
            <div class="h-1.5 w-1.5 bg-rose-500 rounded-full"></div>
            Active Alerts
        </div>
        <div class="font-heading text-4xl font-bold text-white tracking-tight">{{ $stats['activeAnomalies'] ?? 0 }}</div>
    </div>
</div>
