@props(['anomalies'])

<div class="flex flex-col h-full overflow-hidden">
    <div class="py-6 px-8 border-b border-white/5 bg-white/[0.02] flex justify-between items-center">
        <h3 class="font-heading text-sm font-bold text-white tracking-tight">Security Alerts</h3>
        <div class="flex items-center gap-2 px-2.5 py-1 bg-emerald-500/10 border border-emerald-500/20 rounded-lg">
            <div class="h-1.5 w-1.5 bg-emerald-500 rounded-full"></div>
            <span class="text-[10px] font-bold text-emerald-500 uppercase tracking-wider italic">Scanning</span>
        </div>
    </div>
    <div class="flex-1 overflow-y-auto custom-scrollbar">
        <div class="divide-y divide-white/5">
            @forelse($anomalies as $anomaly)
                <div class="p-6 hover:bg-white/[0.04] transition-colors flex items-center gap-5 group">
                    <div class="h-10 w-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center flex-shrink-0 group-hover:border-orange-500/50 transition-colors">
                        <svg class="w-5 h-5 text-zinc-500 group-hover:text-orange-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-baseline mb-1">
                            <h4 class="text-sm font-bold text-white tracking-tight truncate">{{ $anomaly->type === 'speeding' ? 'High Speed Detected' : 'Geofence Breach' }}</h4>
                            <span class="text-[10px] font-medium text-zinc-600">{{ $anomaly->occurred_at->diffForHumans(null, true) }}</span>
                        </div>
                        <p class="text-xs text-zinc-500 font-medium">
                            Unit <span class="text-zinc-300 font-bold tracking-wider">{{ $anomaly->vehicle->license_plate }}</span> • Impact: -{{ $anomaly->impact_score }}
                        </p>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center">
                    <p class="text-xs text-zinc-500 font-medium tracking-tight">No critical alerts detected</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
