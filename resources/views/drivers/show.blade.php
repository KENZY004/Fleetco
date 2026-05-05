@extends('layouts.app')

@section('content')
<div class="flex flex-col gap-8">
    
    {{-- Header / Profile Summary --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div class="flex items-center gap-6">
            <div class="h-20 w-20 rounded-[2rem] bg-gradient-to-br from-primary to-orange-600 flex items-center justify-center text-black text-3xl font-black shadow-xl shadow-primary/20">
                {{ substr($driver->name, 0, 1) }}
            </div>
            <div>
                <div class="text-[10px] font-bold text-primary uppercase tracking-[0.3em] mb-2">Driver_Scorecard</div>
                <h1 class="font-heading text-4xl font-bold text-white tracking-tight">{{ $driver->name }}</h1>
                <div class="flex items-center gap-4 mt-2">
                    <div class="flex items-center gap-2 text-zinc-500 text-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        {{ $driver->user->email ?? 'N/A' }}
                    </div>
                    <div class="h-1 w-1 rounded-full bg-zinc-800"></div>
                    <div class="text-zinc-500 text-xs font-mono uppercase tracking-wider">ID: #{{ str_pad($driver->id, 5, '0', STR_PAD_LEFT) }}</div>
                </div>
            </div>
        </div>

        <div class="flex gap-4">
            <form action="{{ route('drivers.reset-score', $driver->id) }}" method="POST">
                @csrf @method('PATCH')
                <button type="submit" class="px-6 py-3 bg-white/5 border border-white/10 rounded-2xl text-[10px] font-bold uppercase tracking-widest text-zinc-400 hover:text-white hover:bg-white/10 transition-all">
                    Reset Safety Score
                </button>
            </form>
            <a href="{{ route('drivers.index') }}" class="px-6 py-3 bg-white/5 border border-white/10 rounded-2xl text-[10px] font-bold uppercase tracking-widest text-zinc-400 hover:text-white hover:bg-white/10 transition-all">
                Back to Roster
            </a>
        </div>
    </div>

    {{-- Stats Bento Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        
        {{-- Safety Score --}}
        <div class="p-8 rounded-[2.5rem] bg-zinc-950 border border-white/5 flex flex-col items-center justify-center text-center relative overflow-hidden group">
            <div class="absolute inset-0 bg-primary/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <div class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-6">Safety Performance</div>
            <div class="relative">
                <svg class="w-32 h-32 transform -rotate-90">
                    <circle cx="64" cy="64" r="58" stroke="currentColor" stroke-width="8" fill="transparent" class="text-white/5"/>
                    <circle cx="64" cy="64" r="58" stroke="currentColor" stroke-width="8" fill="transparent" 
                        class="{{ $driver->risk_score > 80 ? 'text-emerald-500' : ($driver->risk_score > 60 ? 'text-orange-500' : 'text-red-500') }}"
                        stroke-dasharray="364.4"
                        stroke-dashoffset="{{ 364.4 - (364.4 * $driver->risk_score / 100) }}"
                        stroke-linecap="round"/>
                </svg>
                <div class="absolute inset-0 flex items-center justify-center flex-col">
                    <span class="text-3xl font-black text-white">{{ number_format($driver->risk_score, 0) }}</span>
                    <span class="text-[8px] font-bold text-zinc-500 uppercase">Points</span>
                </div>
            </div>
        </div>

        {{-- Active Vehicle --}}
        <div class="p-8 rounded-[2.5rem] bg-zinc-950 border border-white/5">
            <div class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-6">Current Assignment</div>
            @if($driver->vehicle)
                <div class="flex items-center gap-4 mb-4">
                    <div class="h-12 w-12 rounded-2xl bg-white/5 flex items-center justify-center text-primary">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                    </div>
                    <div>
                        <div class="text-lg font-bold text-white">{{ $driver->vehicle->name }}</div>
                        <div class="text-[10px] font-mono text-zinc-500 uppercase">{{ $driver->vehicle->license_plate }}</div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <div class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></div>
                    <span class="text-[10px] font-bold text-emerald-400 uppercase tracking-widest">Active Connection</span>
                </div>
            @else
                <div class="py-4 text-zinc-600 text-sm italic">No vehicle assigned</div>
            @endif
        </div>

        {{-- Clean Pings --}}
        <div class="p-8 rounded-[2.5rem] bg-zinc-950 border border-white/5">
            <div class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-6">Experience</div>
            <div class="text-4xl font-black text-white mb-2">{{ number_format($driver->clean_pings_count ?? 0) }}</div>
            <div class="text-[10px] text-zinc-500 font-bold uppercase tracking-wider">Clean Telemetry Pings</div>
            <div class="mt-4 h-1 w-full bg-white/5 rounded-full overflow-hidden">
                <div class="h-full bg-emerald-500" style="width: 70%"></div>
            </div>
        </div>

        {{-- Total Distance --}}
        <div class="p-8 rounded-[2.5rem] bg-zinc-950 border border-white/5">
            <div class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-6">Duty Range</div>
            <div class="text-4xl font-black text-white mb-2">{{ number_format($trips->sum('distance'), 1) }}</div>
            <div class="text-[10px] text-zinc-500 font-bold uppercase tracking-wider">Kilometers Tracked</div>
            <div class="mt-4 flex gap-1">
                @foreach(range(1, 12) as $i)
                    <div class="h-6 flex-1 bg-white/5 rounded-sm {{ $i < 8 ? 'bg-primary/20' : '' }}"></div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Main Sections --}}
    <div class="grid grid-cols-12 gap-8">
        
        {{-- Trip History --}}
        <div class="col-span-12 lg:col-span-8 space-y-6">
            <div class="flex items-center justify-between px-2">
                <h3 class="text-lg font-bold text-white tracking-tight">Recent Missions</h3>
                <span class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Latest 10</span>
            </div>
            
            <div class="bg-zinc-950 border border-white/5 rounded-[2.5rem] overflow-hidden">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-white/[0.02] border-b border-white/5">
                            <th class="px-8 py-5 text-[9px] font-bold uppercase tracking-widest text-zinc-500">Date</th>
                            <th class="px-8 py-5 text-[9px] font-bold uppercase tracking-widest text-zinc-500">Vehicle</th>
                            <th class="px-8 py-5 text-[9px] font-bold uppercase tracking-widest text-zinc-500 text-right">Distance</th>
                            <th class="px-8 py-5 text-[9px] font-bold uppercase tracking-widest text-zinc-500 text-right">Avg Speed</th>
                            <th class="px-8 py-5 text-[9px] font-bold uppercase tracking-widest text-zinc-500 text-right">Replay</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($trips as $trip)
                            <tr class="hover:bg-white/[0.02] transition-colors group">
                                <td class="px-8 py-6">
                                    <div class="text-white font-medium">{{ $trip->start_time->format('M d, Y') }}</div>
                                    <div class="text-zinc-600 text-xs">{{ $trip->start_time->format('H:i') }}</div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="text-zinc-300 font-bold">{{ $trip->vehicle->name }}</div>
                                    <div class="text-[10px] font-mono text-zinc-500 uppercase">{{ $trip->vehicle->license_plate }}</div>
                                </td>
                                <td class="px-8 py-6 text-right font-bold text-white">{{ number_format($trip->distance, 2) }} <span class="text-[10px] text-zinc-500 font-normal">km</span></td>
                                <td class="px-8 py-6 text-right font-bold text-white">{{ number_format($trip->average_speed, 1) }} <span class="text-[10px] text-zinc-500 font-normal">km/h</span></td>
                                <td class="px-8 py-6 text-right">
                                    <a href="{{ route('trips.show', $trip->id) }}" class="p-2 bg-white/5 border border-white/10 rounded-lg text-zinc-500 hover:text-primary hover:border-primary/50 transition-all inline-block">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-8 py-12 text-center text-zinc-600 italic">No trip history found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Risk Events --}}
        <div class="col-span-12 lg:col-span-4 space-y-6">
            <div class="flex items-center justify-between px-2">
                <h3 class="text-lg font-bold text-white tracking-tight">Recent Incidents</h3>
                <span class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest text-red-500">Security Log</span>
            </div>

            <div class="space-y-4">
                @forelse($alerts as $alert)
                    <div class="p-6 rounded-[2rem] bg-zinc-950 border border-white/5 hover:border-red-500/20 transition-all group">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="h-10 w-10 rounded-xl bg-red-500/10 flex items-center justify-center text-red-500">
                                @if($alert->type === 'speeding')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                @else
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                @endif
                            </div>
                            <div>
                                <div class="text-xs font-bold text-white uppercase tracking-wider">{{ ucwords(str_replace('_', ' ', $alert->type)) }}</div>
                                <div class="text-[10px] text-zinc-600 font-mono">{{ $alert->occurred_at->format('M d · H:i:s') }}</div>
                            </div>
                        </div>
                        <p class="text-[11px] text-zinc-500 leading-relaxed mb-4">
                            @if($alert->type === 'speeding')
                                Detected at {{ $alert->details['speed'] }} km/h in a {{ $alert->details['limit'] }} km/h zone.
                            @else
                                Entered restricted zone: {{ $alert->details['landmark_name'] ?? 'Unknown' }}.
                            @endif
                        </p>
                        <div class="flex items-center justify-between">
                            <span class="px-3 py-1 bg-white/5 rounded-full text-[9px] font-bold text-zinc-500 uppercase tracking-widest">{{ $alert->vehicle->license_plate }}</span>
                            <span class="text-[9px] font-bold uppercase tracking-widest {{ $alert->resolved_at ? 'text-emerald-500' : 'text-red-500' }}">
                                {{ $alert->resolved_at ? 'Resolved' : 'Unresolved' }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center rounded-[2rem] bg-zinc-950 border border-white/5 border-dashed">
                        <div class="text-emerald-500 mb-2">
                            <svg class="w-8 h-8 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Clean Safety Record</div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
