@props(['anomalies'])

<div class="flex flex-col h-full overflow-hidden">
    <div class="py-6 px-8 border-b border-border bg-obsidian-900/50 flex justify-between items-center">
        <div class="flex flex-col">
            <span class="text-[10px] font-black uppercase tracking-[0.4em] text-primary mb-1">Heuristic Matrix</span>
            <h3 class="text-xs font-bold text-white uppercase tracking-widest">Risk Signatures</h3>
        </div>
        <div class="flex items-center gap-2 px-3 py-1 bg-rose-500/10 border border-rose-500/20 rounded-full">
            <div class="h-1 w-1 bg-rose-500 rounded-full animate-pulse"></div>
            <span class="text-[9px] font-bold text-rose-500 uppercase tracking-tighter">Subsurface Scanning</span>
        </div>
    </div>
    <div class="flex-1 overflow-y-auto custom-scrollbar">
        <div class="divide-y divide-border">
            @forelse($anomalies as $anomaly)
                <div class="p-6 hover:bg-white/5 transition-colors flex items-center gap-6 group">
                    <div class="h-10 w-10 rounded bg-white/5 border border-border flex items-center justify-center flex-shrink-0 group-hover:border-primary/50 transition-colors">
                        <svg class="w-4 h-4 text-zinc-400 group-hover:text-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-start mb-1">
                            <h4 class="text-xs font-bold text-white uppercase tracking-widest truncate">{{ $anomaly->type === 'speeding' ? 'Velocity Deviation' : 'Perimeter Breach' }}</h4>
                            <span class="text-[10px] font-mono text-zinc-500">{{ $anomaly->occurred_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-[10px] text-zinc-500 uppercase tracking-[0.2em] font-medium">
                            UNIT {{ $anomaly->vehicle->license_plate }} • SCORE_IMPACT: -{{ $anomaly->impact_score }}
                        </p>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center">
                    <p class="text-[10px] text-zinc-500 uppercase tracking-widest font-bold">Sovereign Integrity: Intact</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
