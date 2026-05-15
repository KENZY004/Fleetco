@extends('layouts.app')

@section('content')
<div class="flex flex-col gap-8">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="text-[9px] font-bold tracking-widest text-primary uppercase mb-2">Fleet Intelligence</div>
            <h1 class="font-heading text-3xl font-bold tracking-tight">Trip History</h1>
            <p class="text-zinc-500 text-sm mt-1">Browse and replay all recorded vehicle journeys.</p>
        </div>
    </div>

    {{-- Filter --}}
    <form method="GET" action="{{ route('trips.index') }}" class="flex flex-wrap items-center gap-3">
        <select name="vehicle_id" onchange="this.form.submit()"
            class="bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-xs text-white outline-none focus:border-primary/50 transition-colors">
            <option value="">All Vehicles</option>
            @foreach($vehicles as $v)
                <option value="{{ $v->id }}" {{ request('vehicle_id') == $v->id ? 'selected' : '' }}>
                    {{ $v->name }} · {{ $v->license_plate }}
                </option>
            @endforeach
        </select>

        @if(request('vehicle_id'))
            <a href="{{ route('trips.index') }}" class="text-[10px] font-bold uppercase tracking-wider text-zinc-500 hover:text-white transition-colors">
                Clear Filter
            </a>
        @endif
    </form>

    {{-- Trips Table (Desktop) --}}
    <x-fleet-card class="hidden md:block overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-white/[0.02] border-b border-white/5">
                        <th class="px-8 py-5 text-[9px] font-bold uppercase tracking-widest text-zinc-500">Vehicle</th>
                        <th class="px-8 py-5 text-[9px] font-bold uppercase tracking-widest text-zinc-500">Started</th>
                        <th class="px-8 py-5 text-[9px] font-bold uppercase tracking-widest text-zinc-500">Duration</th>
                        <th class="px-8 py-5 text-[9px] font-bold uppercase tracking-widest text-zinc-500">Distance</th>
                        <th class="px-8 py-5 text-[9px] font-bold uppercase tracking-widest text-zinc-500">Avg Speed</th>
                        <th class="px-8 py-5 text-[9px] font-bold uppercase tracking-widest text-zinc-500">Status</th>
                        <th class="px-8 py-5 text-[9px] font-bold uppercase tracking-widest text-zinc-500 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5 text-sm">
                    @forelse($trips as $trip)
                        <tr class="hover:bg-white/[0.02] transition-colors group">
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="h-10 w-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary shrink-0">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                                    </div>
                                    <div>
                                        <div class="font-bold text-white">{{ $trip->vehicle?->name ?? '—' }}</div>
                                        <div class="text-[10px] font-mono text-zinc-500">{{ $trip->vehicle?->license_plate }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="text-white font-medium">{{ $trip->start_time->format('M d, Y') }}</div>
                                <div class="text-zinc-600 text-xs">{{ $trip->start_time->format('H:i:s') }}</div>
                            </td>
                            <td class="px-8 py-6 text-zinc-300 font-medium">
                                @if($trip->end_time)
                                    @php
                                        $mins = $trip->start_time->diffInMinutes($trip->end_time);
                                        $h = intdiv($mins, 60); $m = $mins % 60;
                                    @endphp
                                    {{ $h > 0 ? $h . 'h ' : '' }}{{ $m }}m
                                @else
                                    <span class="text-emerald-400 text-xs font-bold uppercase tracking-wider">In Progress</span>
                                @endif
                            </td>
                            <td class="px-8 py-6">
                                <span class="font-bold text-white">{{ number_format($trip->distance, 2) }}</span>
                                <span class="text-zinc-600 text-xs ml-1">km</span>
                            </td>
                            <td class="px-8 py-6">
                                <span class="font-bold text-white">{{ number_format($trip->average_speed, 1) }}</span>
                                <span class="text-zinc-600 text-xs ml-1">km/h</span>
                            </td>
                            <td class="px-8 py-6">
                                @if($trip->end_time)
                                    <x-badge type="success">Completed</x-badge>
                                @else
                                    <x-badge type="info">Live</x-badge>
                                @endif
                            </td>
                            <td class="px-8 py-6 text-right">
                                <a href="{{ route('trips.show', $trip->id) }}"
                                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-white/5 border border-white/10 rounded-xl text-[10px] font-bold uppercase tracking-wider text-zinc-400 hover:text-white hover:border-primary/30 hover:bg-primary/5 transition-all group-hover:border-white/20">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                    Replay
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-20 text-center text-zinc-500 italic text-xs uppercase tracking-widest">No Trips Recorded</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-fleet-card>

    {{-- Mobile Trip Cards --}}
    <div class="md:hidden space-y-4">
        @forelse($trips as $trip)
            <div class="p-6 rounded-[2rem] bg-zinc-950 border border-white/5 space-y-4">
                <div class="flex justify-between items-start">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary">
                             <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                        </div>
                        <div>
                            <div class="text-sm font-bold text-white">{{ $trip->vehicle?->name }}</div>
                            <div class="text-[10px] font-mono text-zinc-600 uppercase">{{ $trip->vehicle?->license_plate }}</div>
                        </div>
                    </div>
                    @if($trip->end_time)
                        <span class="px-2 py-1 bg-emerald-500/10 text-emerald-500 text-[8px] font-black uppercase tracking-widest rounded">Completed</span>
                    @else
                        <span class="px-2 py-1 bg-blue-500/10 text-blue-500 text-[8px] font-black uppercase tracking-widest rounded animate-pulse">Live</span>
                    @endif
                </div>

                <div class="grid grid-cols-2 gap-4 py-2 border-y border-white/5">
                    <div>
                        <div class="text-[8px] text-zinc-500 uppercase font-black tracking-widest mb-1">Distance</div>
                        <div class="text-sm font-bold text-white">{{ number_format($trip->distance, 1) }} KM</div>
                    </div>
                    <div>
                        <div class="text-[8px] text-zinc-500 uppercase font-black tracking-widest mb-1">Avg Speed</div>
                        <div class="text-sm font-bold text-white">{{ number_format($trip->average_speed, 1) }} KM/H</div>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest">
                        {{ $trip->start_time->format('M d, H:i') }}
                    </div>
                    <a href="{{ route('trips.show', $trip->id) }}" class="px-4 py-2 bg-white text-black text-[9px] font-black uppercase tracking-widest rounded-lg">
                        Replay
                    </a>
                </div>
            </div>
        @empty
            <div class="p-12 rounded-[2rem] bg-zinc-950 border border-white/5 text-center text-zinc-500 italic uppercase text-[10px] tracking-widest">No trips found.</div>
        @endforelse
    </div>

        @if($trips->hasPages())
            <div class="px-8 py-6 bg-white/[0.01] border-t border-white/5">
                {{ $trips->appends(request()->query())->links() }}
            </div>
        @endif
</div>
@endsection
