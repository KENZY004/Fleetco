@extends('driver.layouts.app')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-6">
        <div class="text-[10px] text-[#ff8a00] font-black uppercase tracking-[0.2em] mb-1">Security Archives</div>
        <h1 class="text-2xl font-bold text-white tracking-tight">My Incidents</h1>
    </div>

    <!-- STAT ROW -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-[#111] border border-[#1a1a1a] rounded-xl p-4 flex flex-col justify-between">
            <span class="text-[10px] text-[#555] font-black uppercase tracking-widest mb-3">Total Incidents</span>
            <span class="text-3xl font-bold text-white tracking-tight">{{ number_format($totalIncidents) }}</span>
        </div>

        <div class="bg-[#111] border border-[#1a1a1a] rounded-xl p-4 flex flex-col justify-between">
            <span class="text-[10px] text-[#555] font-black uppercase tracking-widest mb-3">Speeding Events</span>
            @php $speedColor = $speedingEvents > 0 ? 'text-[#ef4444]' : 'text-white'; @endphp
            <span class="text-3xl font-bold tracking-tight {{ $speedColor }}">{{ number_format($speedingEvents) }}</span>
        </div>

        <div class="bg-[#111] border border-[#1a1a1a] rounded-xl p-4 flex flex-col justify-between">
            <span class="text-[10px] text-[#555] font-black uppercase tracking-widest mb-3">Geofence Breaches</span>
            <span class="text-3xl font-bold tracking-tight text-[#ff8a00]">{{ number_format($geofenceBreaches) }}</span>
        </div>

        <div class="bg-[#111] border border-[#1a1a1a] rounded-xl p-4 flex flex-col justify-between">
            <span class="text-[10px] text-[#555] font-black uppercase tracking-widest mb-3 flex items-center gap-2">
                Unresolved
                @if($unresolvedCount > 0)
                <div class="w-1.5 h-1.5 rounded-full bg-[#ef4444] shadow-[0_0_8px_rgba(239,68,68,0.5)] animate-pulse"></div>
                @endif
            </span>
            <span class="text-3xl font-bold tracking-tight {{ $unresolvedCount > 0 ? 'text-[#ef4444]' : 'text-white' }}">{{ number_format($unresolvedCount) }}</span>
        </div>
    </div>

    <!-- MAIN CARD -->
    <div class="bg-[#111] border border-[#1a1a1a] rounded-xl overflow-hidden">
        <div class="p-6 border-b border-[#1a1a1a] flex flex-col md:flex-row justify-between items-center bg-[#111] gap-4">
            <span class="text-[10px] text-[#555] font-black uppercase tracking-widest">Incident History</span>
            
            <!-- FILTERS -->
            <form method="GET" action="{{ route('driver.risk') }}" class="flex gap-2 w-full md:w-auto">
                <select name="type" onchange="this.form.submit()" class="bg-[#1a1a1a] border border-[#222] text-xs font-bold text-white uppercase tracking-widest rounded-lg px-3 py-2 outline-none focus:border-[#ff8a00]">
                    <option value="all" {{ request('type') == 'all' ? 'selected' : '' }}>All Types</option>
                    <option value="speeding" {{ request('type') == 'speeding' ? 'selected' : '' }}>Speeding</option>
                    <option value="geofence_breach" {{ request('type') == 'geofence_breach' ? 'selected' : '' }}>Geofence Breach</option>
                    <option value="hard_braking" {{ request('type') == 'hard_braking' ? 'selected' : '' }}>Hard Braking</option>
                </select>

                <select name="status" onchange="this.form.submit()" class="bg-[#1a1a1a] border border-[#222] text-xs font-bold text-white uppercase tracking-widest rounded-lg px-3 py-2 outline-none focus:border-[#ff8a00]">
                    <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Status</option>
                    <option value="unresolved" {{ request('status') == 'unresolved' ? 'selected' : '' }}>Unresolved</option>
                    <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                </select>
            </form>
        </div>

        @if($incidents->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-[#0a0a0a] border-b border-[#1a1a1a]">
                            <th class="px-6 py-4 text-[10px] font-black text-[#555] uppercase tracking-widest">Incident</th>
                            <th class="px-6 py-4 text-[10px] font-black text-[#555] uppercase tracking-widest">Impact</th>
                            <th class="px-6 py-4 text-[10px] font-black text-[#555] uppercase tracking-widest">Status</th>
                            <th class="px-6 py-4 text-[10px] font-black text-[#555] uppercase tracking-widest">Timestamp</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#1a1a1a]">
                        @foreach($incidents as $incident)
                            @php
                                $typeLabel = str_replace('_', ' ', strtoupper($incident->type));
                                if (isset($incident->details['breach_type'])) {
                                    $typeLabel .= ': ' . strtoupper($incident->details['breach_type']);
                                }

                                $borderColor = 'border-l-[#ff8a00]'; // default warning
                                if ($incident->type === 'speeding') $borderColor = 'border-l-[#ef4444]';
                                elseif ($incident->type === 'geofence_breach') $borderColor = 'border-l-[#ff8a00]';
                                elseif ($incident->type === 'idle') $borderColor = 'border-l-[#eab308]'; // yellow

                                $status = $incident->resolved_at ? 'Resolved' : 'Unresolved';
                                $statusColor = $status === 'Resolved' ? 'bg-[#10b981]/10 text-[#10b981]' : 'bg-[#ef4444]/10 text-[#ef4444]';
                            @endphp
                            <tr class="hover:bg-white/[0.02] transition-colors {{ $loop->even ? 'bg-[#0d0d0d]' : 'bg-[#111111]' }} border-l-4 {{ $borderColor }}">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-white">{{ $typeLabel }}</div>
                                    <div class="text-xs text-[#555] mt-1">{{ isset($incident->details['speed']) ? $incident->details['speed'] . ' KM/H' : '' }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm font-bold text-[#ef4444]">-{{ $incident->impact_score }}</td>
                                <td class="px-6 py-4">
                                    <span class="text-[9px] px-2 py-1 rounded uppercase font-bold {{ $statusColor }}">
                                        {{ $status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-white">{{ $incident->occurred_at->format('M d, Y') }}</div>
                                    <div class="text-xs text-[#555] font-mono">{{ $incident->occurred_at->format('H:i:s') }}</div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            @if($incidents->hasPages())
            <div class="p-4 bg-[#0a0a0a] border-t border-[#1a1a1a]">
                {{ $incidents->appends(request()->query())->links() }}
            </div>
            @endif
            
            <div class="p-4 bg-[#0a0a0a] border-t border-[#1a1a1a]">
                {{-- RBAC: alert resolution is Fleet Manager only - AlertController --}}
                <p class="text-xs text-[#555] italic text-center">Alert resolution is managed by your assigned Fleet Manager.</p>
            </div>
        @else
            <!-- Empty State -->
            <div class="flex flex-col items-center justify-center py-16">
                <div class="w-16 h-16 rounded-full bg-[#10b981]/10 flex items-center justify-center mb-6">
                    <svg class="w-8 h-8 text-[#10b981]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                </div>
                <span class="text-[12px] font-black uppercase tracking-widest text-[#444] mb-2">No Incidents Recorded</span>
                <span class="text-sm text-[#555]">Your fleet is operating within normal parameters.</span>
            </div>
        @endif
    </div>
</div>
@endsection
