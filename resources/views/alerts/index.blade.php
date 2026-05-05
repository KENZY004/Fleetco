@extends('layouts.app')

@section('content')
<div class="flex flex-col gap-8">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="text-[10px] font-bold tracking-widest text-red-500 uppercase mb-2">Security Archives</div>
            <h1 class="font-heading text-3xl font-bold tracking-tight">Incident History</h1>
            <p class="text-zinc-500 text-sm mt-1">Review and manage all recorded fleet anomalies.</p>
        </div>

        @if($alerts->total() > 0)
        <form method="POST" action="{{ route('alerts.clear') }}" onsubmit="return confirm('This will permanently delete all {{ $alerts->total() }} incident records. Continue?')">
            @csrf @method('DELETE')
            <button type="submit" class="px-6 py-3 border border-red-500/20 rounded-full text-[10px] font-bold uppercase tracking-wider text-red-400 hover:bg-red-500/10 transition-all">
                Clear All Logs
            </button>
        </form>
        @endif
    </div>

    {{-- Stats Row --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <x-fleet-card class="p-7">
            <div class="text-[9px] text-zinc-500 uppercase font-bold tracking-widest mb-3">Total Incidents</div>
            <div class="text-4xl font-bold text-white tracking-tighter">{{ $alerts->total() }}</div>
        </x-fleet-card>

        <x-fleet-card class="p-7">
            <div class="text-[9px] text-zinc-500 uppercase font-bold tracking-widest mb-3">Speeding Events</div>
            <div class="text-4xl font-bold text-orange-400 tracking-tighter">{{ $speedingCount }}</div>
        </x-fleet-card>

        <x-fleet-card class="p-7">
            <div class="text-[9px] text-zinc-500 uppercase font-bold tracking-widest mb-3">Geofence Breaches</div>
            <div class="text-4xl font-bold text-red-500 tracking-tighter">{{ $geofenceCount }}</div>
        </x-fleet-card>

        <x-fleet-card class="p-7">
            <div class="text-[9px] text-zinc-500 uppercase font-bold tracking-widest mb-3">Unresolved</div>
            <div class="flex items-center gap-3 mt-1">
                <div class="h-2 w-2 bg-red-500 rounded-full animate-pulse"></div>
                <span class="text-2xl font-bold text-white tracking-tighter">{{ $unresolvedCount }}</span>
            </div>
        </x-fleet-card>
    </div>

    {{-- Filter Bar --}}
    <form method="GET" action="{{ route('alerts.index') }}" class="flex flex-wrap items-center gap-3">
        <select name="type" onchange="this.form.submit()"
            class="bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-xs text-white outline-none focus:border-primary/50 transition-colors">
            <option value="">All Types</option>
            <option value="speeding" {{ request('type') === 'speeding' ? 'selected' : '' }}>Speeding</option>
            <option value="geofence_breach" {{ request('type') === 'geofence_breach' ? 'selected' : '' }}>Geofence Breach</option>
            <option value="geofence_entry" {{ request('type') === 'geofence_entry' ? 'selected' : '' }}>Geofence Entry</option>
        </select>

        <select name="status" onchange="this.form.submit()"
            class="bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-xs text-white outline-none focus:border-primary/50 transition-colors">
            <option value="">All Status</option>
            <option value="unresolved" {{ request('status') === 'unresolved' ? 'selected' : '' }}>Unresolved</option>
            <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
        </select>

        @if(request('type') || request('status'))
            <a href="{{ route('alerts.index') }}" class="text-[10px] font-bold uppercase tracking-wider text-zinc-500 hover:text-white transition-colors">
                Clear Filters
            </a>
        @endif
    </form>

    {{-- Success Flash --}}
    @if(session('success'))
        <div class="px-6 py-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl text-emerald-400 text-sm font-bold">
            {{ session('success') }}
        </div>
    @endif

    {{-- History Table --}}
    <x-fleet-card class="overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-white/[0.02] border-b border-white/5">
                        <th class="px-8 py-5 text-[9px] font-bold uppercase tracking-widest text-zinc-500">Incident</th>
                        <th class="px-8 py-5 text-[9px] font-bold uppercase tracking-widest text-zinc-500">Vehicle</th>
                        <th class="px-8 py-5 text-[9px] font-bold uppercase tracking-widest text-zinc-500">Driver</th>
                        <th class="px-8 py-5 text-[9px] font-bold uppercase tracking-widest text-zinc-500">Impact</th>
                        <th class="px-8 py-5 text-[9px] font-bold uppercase tracking-widest text-zinc-500">Status</th>
                        <th class="px-8 py-5 text-[9px] font-bold uppercase tracking-widest text-zinc-500">Timestamp</th>
                        <th class="px-8 py-5 text-[9px] font-bold uppercase tracking-widest text-zinc-500 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5 text-sm">
                    @forelse($alerts as $alert)
                        <tr class="hover:bg-white/[0.02] transition-colors group {{ $alert->resolved_at ? 'opacity-50' : '' }}">

                            {{-- Incident Type --}}
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="h-10 w-10 rounded-xl flex items-center justify-center shrink-0
                                        {{ $alert->type === 'speeding' ? 'bg-orange-500/10 text-orange-500' : 'bg-red-500/10 text-red-500' }}">
                                        @if($alert->type === 'speeding')
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                        @else
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="font-bold text-white tracking-tight">
                                            @switch($alert->type)
                                                @case('speeding') Speeding Violation @break
                                                @case('geofence_breach') Geofence Breach @break
                                                @case('geofence_entry') Geofence Entry @break
                                                @default {{ ucwords(str_replace('_', ' ', $alert->type)) }}
                                            @endswitch
                                        </div>
                                        @if(!empty($alert->details['landmark_name']))
                                            <div class="text-[10px] text-zinc-500 font-bold tracking-wider mt-0.5 uppercase">
                                                Zone: {{ $alert->details['landmark_name'] }}
                                            </div>
                                        @elseif(!empty($alert->details['speed']))
                                            <div class="text-[10px] text-zinc-500 font-bold tracking-wider mt-0.5">
                                                {{ $alert->details['speed'] }} km/h &nbsp;·&nbsp; Limit {{ $alert->details['limit'] ?? '—' }} km/h
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            {{-- Vehicle --}}
                            <td class="px-8 py-6 font-mono text-zinc-400">
                                {{ $alert->vehicle?->license_plate ?? '—' }}
                            </td>

                            {{-- Driver --}}
                            <td class="px-8 py-6">
                                <div class="text-white font-bold">{{ $alert->driver?->name ?? 'Unassigned' }}</div>
                            </td>

                            {{-- Impact --}}
                            <td class="px-8 py-6">
                                @if($alert->impact_score > 0)
                                    <span class="px-3 py-1 rounded-full bg-red-500/10 text-red-400 text-[10px] font-bold border border-red-500/20">
                                        -{{ $alert->impact_score }} PTS
                                    </span>
                                @else
                                    <span class="text-zinc-600 text-[10px] font-bold uppercase tracking-wider">Logged</span>
                                @endif
                            </td>

                            {{-- Status --}}
                            <td class="px-8 py-6">
                                @if($alert->resolved_at)
                                    <span class="flex items-center gap-2 text-[10px] font-bold uppercase tracking-wider text-emerald-500">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Resolved
                                    </span>
                                    @if($alert->resolution_note)
                                        <div class="text-[9px] text-zinc-600 mt-1 max-w-[120px] truncate">{{ $alert->resolution_note }}</div>
                                    @endif
                                @else
                                    <span class="flex items-center gap-2 text-[10px] font-bold uppercase tracking-wider text-red-400">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span> Unresolved
                                    </span>
                                @endif
                            </td>

                            {{-- Timestamp --}}
                            <td class="px-8 py-6 text-zinc-500 font-medium text-xs">
                                {{ $alert->occurred_at->format('M d, Y') }}<br>
                                <span class="text-zinc-700">{{ $alert->occurred_at->format('H:i:s') }}</span>
                            </td>

                            {{-- Actions --}}
                            <td class="px-8 py-6 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if(!$alert->resolved_at)
                                        <button
                                            onclick="openResolveModal({{ $alert->id }})"
                                            class="px-4 py-2 text-[9px] font-bold uppercase tracking-wider rounded-lg border border-emerald-500/20 text-emerald-500 hover:bg-emerald-500/10 transition-all">
                                            Resolve
                                        </button>
                                    @endif
                                    <form method="POST" action="{{ route('alerts.destroy', $alert->id) }}" class="inline-block" onsubmit="return confirm('Delete this incident record?')">
                                        @csrf @method('DELETE')
                                        <button class="p-2 text-zinc-600 hover:text-red-500 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-20 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-16 h-16 rounded-2xl bg-white/5 flex items-center justify-center text-zinc-700">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <div class="text-zinc-500 text-xs uppercase font-bold tracking-widest">No Incidents Recorded</div>
                                    <div class="text-zinc-700 text-xs">Your fleet is operating within normal parameters.</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($alerts->hasPages())
        <div class="px-8 py-6 bg-white/[0.01] border-t border-white/5">
            {{ $alerts->appends(request()->query())->links() }}
        </div>
        @endif
    </x-fleet-card>
</div>

{{-- Resolve Modal --}}
<x-fleet-modal name="showResolveModal" title="Resolve Incident" subtitle="Mark as Handled">
    <form id="resolveForm" method="POST" action="" class="space-y-6">
        @csrf @method('PATCH')
        <div>
            <label class="text-[10px] font-bold uppercase tracking-wider text-zinc-500 block mb-2">Resolution Note</label>
            <textarea name="note" rows="3" placeholder="e.g. Driver warned, GPS drift confirmed, false positive..."
                class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-zinc-700 text-sm outline-none focus:border-primary/50 transition-colors resize-none"></textarea>
        </div>
        <div class="flex gap-4 pt-2">
            <button type="button" onclick="showResolveModal = false" class="flex-1 py-4 border border-white/10 rounded-full text-[10px] font-bold uppercase tracking-wider text-zinc-400 hover:text-white transition-all">Cancel</button>
            <button type="submit" class="flex-1 py-4 bg-emerald-500 text-black rounded-full text-[10px] font-bold uppercase tracking-wider hover:bg-emerald-400 transition-all">Mark Resolved</button>
        </div>
    </form>
</x-fleet-modal>

<script>
    let showResolveModal = false;

    function openResolveModal(alertId) {
        document.getElementById('resolveForm').action = `/alerts/${alertId}/resolve`;
        showResolveModal = true;
        // Trigger Alpine if available
        if (window.Alpine) {
            document.querySelector('[x-data]')?.__x?.$data && (document.querySelector('[x-data]').__x.$data.showResolveModal = true);
        }
        // Fallback: show modal directly
        document.querySelector('[x-show="showResolveModal"]').style.display = 'flex';
    }
</script>
@endsection
