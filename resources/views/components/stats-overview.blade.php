@props(['stats'])

<div class="flex md:grid overflow-x-auto md:overflow-x-visible pb-4 md:pb-0 gap-3 md:gap-6 md:grid-cols-5 custom-scrollbar snap-x no-scrollbar pointer-events-auto">
    <div class="fleetco-card p-5 md:p-6 rounded-2xl flex flex-col gap-1 relative overflow-hidden min-w-[140px] md:min-w-0 snap-start">
        <div class="text-xs text-zinc-500 font-medium tracking-tight">Total Vehicles</div>
        <div class="font-heading text-4xl font-bold text-white tracking-tight" x-text="vehicles.length"></div>
    </div>
    
    <div class="fleetco-card p-5 md:p-6 rounded-2xl flex flex-col gap-1 relative overflow-hidden min-w-[140px] md:min-w-0 snap-start">
        <div class="text-xs text-zinc-500 font-medium tracking-tight flex items-center gap-2">
            <div class="h-1.5 w-1.5 bg-orange-500 rounded-full animate-pulse"></div>
            Online Now
        </div>
        <div class="font-heading text-4xl font-bold text-white tracking-tight" x-text="vehicles.filter(v => v.status === 'active' || v.status === 'moving').length"></div>
    </div>

    <div class="fleetco-card p-5 md:p-6 rounded-2xl flex flex-col gap-1 relative overflow-hidden min-w-[140px] md:min-w-0 snap-start">
        <div class="text-xs text-zinc-500 font-medium tracking-tight">Idle Units</div>
        <div class="font-heading text-4xl font-bold text-white tracking-tight" x-text="vehicles.filter(v => v.status === 'idle').length"></div>
    </div>

    <div class="fleetco-card p-5 md:p-6 rounded-2xl flex flex-col gap-1 relative overflow-hidden min-w-[140px] md:min-w-0 snap-start">
        <div class="text-xs text-zinc-500 font-medium tracking-tight">Total Distance</div>
        <div class="flex items-baseline gap-1">
            <div class="font-heading text-4xl font-bold text-white tracking-tight" x-text="Math.round(vehicles.reduce((acc, v) => acc + (v.odometer || 0), 0))"></div>
            <span class="text-xs font-bold text-zinc-600 uppercase">km</span>
        </div>
    </div>

    <div class="fleetco-card p-5 md:p-6 rounded-2xl flex flex-col gap-1 relative overflow-hidden border-rose-500/10 min-w-[140px] md:min-w-0 snap-start">
        <div class="text-xs text-zinc-500 font-medium tracking-tight flex items-center gap-2">
            <div class="h-1.5 w-1.5 bg-rose-500 rounded-full"></div>
            Active Alerts
        </div>
        <div class="font-heading text-4xl font-bold text-white tracking-tight" x-text="recentAlerts.filter(a => !a.resolved_at).length"></div>
    </div>
</div>
