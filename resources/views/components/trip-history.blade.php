@props(['trips'])

<div class="flex flex-col h-full overflow-hidden fleetco-card rounded-3xl">
    <div class="py-6 px-8 border-b border-white/5 bg-white/[0.02] flex items-center justify-between">
        <h3 class="font-heading text-sm font-bold text-white tracking-tight">Recent Activity</h3>
        <span class="text-[10px] font-bold text-zinc-600 uppercase tracking-widest">Logs</span>
    </div>
    <div class="flex-1 overflow-y-auto custom-scrollbar">
        <div class="divide-y divide-white/5">
            @forelse($trips as $trip)
                <div class="p-6 hover:bg-white/[0.04] transition-colors flex items-center justify-between group cursor-pointer">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="text-[10px] font-bold text-white uppercase tracking-widest">ID: {{ strtoupper(substr($trip->id, 0, 4)) }}</span>
                            <span class="text-[10px] font-medium text-zinc-600 tracking-tight">{{ $trip->start_time->format('M d, Y') }}</span>
                        </div>
                        <div class="flex items-center gap-4 text-[11px] font-medium">
                            <div class="flex items-center gap-1.5 text-zinc-500">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>{{ $trip->start_time->format('H:i') }}</span>
                            </div>
                            <div class="text-orange-500 font-bold tracking-tight">
                                {{ number_format($trip->average_speed, 0) }} km/h avg
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-8">
                        <div class="text-right">
                            <div class="font-heading text-2xl font-bold text-white tracking-tight">{{ number_format($trip->distance, 1) }}<span class="text-xs text-zinc-600 font-bold ml-1 uppercase">km</span></div>
                        </div>
                        <a href="{{ route('trips.show', $trip->id) }}" class="p-3 bg-white/5 border border-white/10 rounded-xl text-zinc-500 hover:text-white transition-all opacity-0 group-hover:opacity-100">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center">
                    <p class="text-xs text-zinc-500 font-medium tracking-tight">No activity logs found</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
