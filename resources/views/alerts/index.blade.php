@extends('layouts.app')

@section('content')
<div class="flex flex-col gap-8">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between">
        <div>
            <div class="text-[10px] font-bold tracking-widest text-red-500 uppercase mb-2">Security Archives</div>
            <h1 class="font-heading text-3xl font-bold tracking-tight">Incident History</h1>
            <p class="text-zinc-500 text-sm mt-1">Review and manage all recorded fleet anomalies.</p>
        </div>
        
        @if($alerts->count() > 0)
        <form method="POST" action="{{ route('alerts.clear') }}" onsubmit="return confirm('Clear entire history?')">
            @csrf @method('DELETE')
            <button type="submit" class="px-6 py-3 border border-red-500/20 rounded-full text-[10px] font-bold uppercase tracking-wider text-red-400 hover:bg-red-500/10 transition-all">
                Clear All Logs
            </button>
        </form>
        @endif
    </div>

    {{-- Stats Row --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="fleetco-card p-8 rounded-[2rem]">
            <div class="text-[10px] text-zinc-500 uppercase font-bold tracking-widest mb-4">Total Incidents</div>
            <div class="text-4xl font-bold text-white tracking-tighter">{{ $alerts->total() }}</div>
        </div>
        <div class="fleetco-card p-8 rounded-[2rem]">
            <div class="text-[10px] text-zinc-500 uppercase font-bold tracking-widest mb-4">Critical Breaches</div>
            <div class="text-4xl font-bold text-red-500 tracking-tighter">
                {{ \App\Models\RiskEvent::where('type', 'geofence_breach')->count() }}
            </div>
        </div>
        <div class="fleetco-card p-8 rounded-[2rem]">
            <div class="text-[10px] text-zinc-500 uppercase font-bold tracking-widest mb-4">Active Monitoring</div>
            <div class="text-emerald-400 font-bold uppercase tracking-widest text-sm flex items-center gap-2">
                <div class="h-1.5 w-1.5 bg-emerald-500 rounded-full animate-pulse"></div>
                Live Ingestion Active
            </div>
        </div>
    </div>

    {{-- History Table --}}
    <div class="fleetco-card rounded-[2.5rem] overflow-hidden border border-white/5">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-white/[0.02] border-b border-white/5">
                        <th class="px-8 py-5 text-[10px] font-bold uppercase tracking-widest text-zinc-500">Incident</th>
                        <th class="px-8 py-5 text-[10px] font-bold uppercase tracking-widest text-zinc-500">Vehicle</th>
                        <th class="px-8 py-5 text-[10px] font-bold uppercase tracking-widest text-zinc-500">Driver</th>
                        <th class="px-8 py-5 text-[10px] font-bold uppercase tracking-widest text-zinc-500">Impact</th>
                        <th class="px-8 py-5 text-[10px] font-bold uppercase tracking-widest text-zinc-500">Timestamp</th>
                        <th class="px-8 py-5 text-[10px] font-bold uppercase tracking-widest text-zinc-500 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5 text-sm">
                    @forelse($alerts as $alert)
                        <tr class="hover:bg-white/[0.02] transition-colors group">
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="h-10 w-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center
                                        {{ $alert->type === 'speeding' ? 'text-orange-500' : 'text-red-500' }}">
                                        @if($alert->type === 'speeding')
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                        @else
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="font-bold text-white tracking-tight">
                                            @if($alert->type === 'speeding')
                                                Speeding Violation
                                            @else
                                                {{ $alert->details['breach_type'] === 'route_deviation' ? 'Route Deviation' : 'Unauthorized Entry' }}
                                            @endif
                                        </div>
                                        <div class="text-[10px] text-zinc-500 uppercase font-bold tracking-wider mt-0.5">
                                            {{ $alert->details['landmark_name'] ?? 'Zone: Unnamed' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6 font-mono text-zinc-400">{{ $alert->vehicle->license_plate }}</td>
                            <td class="px-8 py-6">
                                <div class="text-white font-bold">{{ $alert->driver->name }}</div>
                            </td>
                            <td class="px-8 py-6">
                                <span class="px-3 py-1 rounded-full bg-red-500/10 text-red-500 text-[10px] font-bold border border-red-500/20">
                                    -{{ $alert->impact_score }} PTS
                                </span>
                            </td>
                            <td class="px-8 py-6 text-zinc-500 font-medium">{{ $alert->occurred_at->format('M d, H:i:s') }}</td>
                            <td class="px-8 py-6 text-right">
                                <form method="POST" action="{{ route('alerts.destroy', $alert->id) }}" class="inline-block">
                                    @csrf @method('DELETE')
                                    <button class="p-2 text-zinc-600 hover:text-red-500 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-20 text-center">
                                <div class="text-zinc-600 text-xs uppercase font-bold tracking-widest">No Incidents Recorded</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($alerts->hasPages())
        <div class="px-8 py-6 bg-white/[0.01] border-t border-white/5">
            {{ $alerts->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
