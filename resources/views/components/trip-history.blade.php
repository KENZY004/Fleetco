@props(['trips'])

<div class="flex flex-col h-full overflow-hidden bg-obsidian-900/50 border border-border rounded-3xl">
    <div class="py-6 px-8 border-b border-border bg-obsidian-900/80 flex items-center justify-between">
        <div class="flex flex-col">
            <span class="text-[10px] font-black uppercase tracking-[0.4em] text-zinc-500 mb-1">Sovereign Ledger</span>
            <h3 class="text-xs font-bold text-white uppercase tracking-widest">Mission Ledger</h3>
        </div>
    </div>
    <div class="flex-1 overflow-y-auto custom-scrollbar">
        <div class="divide-y divide-border">
            @forelse($trips as $trip)
                <div class="p-6 hover:bg-white/5 transition-colors flex items-center justify-between group cursor-pointer">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="text-[10px] font-mono font-bold text-white uppercase tracking-widest italic">MISSION_{{ strtoupper(substr($trip->id, 0, 4)) }}</span>
                            <span class="text-[9px] font-bold text-zinc-600 uppercase tracking-tighter">{{ $trip->start_time->format('M d, Y') }}</span>
                        </div>
                        <div class="flex items-center gap-4 text-[10px] font-mono">
                            <div class="flex items-center gap-1.5 text-zinc-400">
                                <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>{{ $trip->start_time->format('H:i') }}</span>
                            </div>
                            <div class="text-primary font-bold uppercase tracking-tighter">
                                AVG: {{ number_format($trip->average_speed, 0) }} kmh
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-extrabold text-white tracking-tighter">{{ number_format($trip->distance, 1) }} <span class="text-[9px] text-zinc-500 font-bold ml-1 uppercase">km</span></div>
                        <div class="text-[9px] font-mono text-zinc-600 uppercase tracking-widest">Inertia Logged</div>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center">
                    <p class="text-[10px] text-zinc-500 uppercase tracking-widest font-bold opacity-30 italic">No Mission Assets Detected</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
