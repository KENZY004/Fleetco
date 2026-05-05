@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-white tracking-tight">Vehicles</h1>
            <p class="text-[11px] text-zinc-500 mt-1 font-mono uppercase tracking-widest">Asset Registry &mdash; {{ $vehicles->total() }} Units</p>
        </div>
        @if(auth()->user()->is_admin ?? false)
        <a href="{{ route('vehicles.create') }}" class="flex items-center gap-2 px-5 py-2.5 bg-primary hover:bg-primary/90 text-black text-[10px] font-black uppercase tracking-widest rounded-lg transition-all shadow-lg shadow-primary/20">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Vehicle
        </a>
        @endif
    </div>

    <!-- Filters Bar -->
    <div class="flex items-center gap-4 flex-wrap">
        <div class="flex bg-white/5 rounded-lg border border-white/5 overflow-hidden">
            <a href="{{ route('vehicles.index') }}" class="px-4 py-2 text-[9px] font-bold uppercase tracking-widest {{ !request('status') ? 'text-primary bg-primary/10' : 'text-zinc-500 hover:text-white' }} border-r border-white/5 transition-all">All</a>
            <a href="{{ route('vehicles.index', ['status' => 'active']) }}" class="px-4 py-2 text-[9px] font-bold uppercase tracking-widest {{ request('status') === 'active' ? 'text-primary bg-primary/10' : 'text-zinc-500 hover:text-white' }} border-r border-white/5 transition-all">Active</a>
            <a href="{{ route('vehicles.index', ['status' => 'in_shop']) }}" class="px-4 py-2 text-[9px] font-bold uppercase tracking-widest {{ request('status') === 'in_shop' ? 'text-primary bg-primary/10' : 'text-zinc-500 hover:text-white' }} transition-all">In Shop</a>
        </div>
        <form action="{{ route('vehicles.index') }}" method="GET" class="relative flex-1 max-w-xs">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-4 w-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <input type="text" name="search" value="{{ request('search') }}" class="block w-full pl-10 pr-3 py-2 border border-white/5 rounded-lg bg-white/5 text-zinc-300 placeholder-zinc-600 focus:outline-none focus:border-white/20 text-[10px] font-mono transition-all" placeholder="Search vehicles...">
        </form>
    </div>

    <!-- Data Table -->
    <div class="fleetco-card rounded-xl overflow-hidden">
        <table class="w-full">
            <thead>
                <tr class="border-b border-white/5">
                    <th class="px-6 py-4 text-left text-[8px] font-black uppercase tracking-[0.2em] text-zinc-500">Name</th>
                    <th class="px-6 py-4 text-left text-[8px] font-black uppercase tracking-[0.2em] text-zinc-500">License Plate</th>
                    <th class="px-6 py-4 text-left text-[8px] font-black uppercase tracking-[0.2em] text-zinc-500">Status</th>
                    <th class="px-6 py-4 text-left text-[8px] font-black uppercase tracking-[0.2em] text-zinc-500">Odometer</th>
                    <th class="px-6 py-4 text-left text-[8px] font-black uppercase tracking-[0.2em] text-zinc-500">Next Service</th>
                    <th class="px-6 py-4 text-left text-[8px] font-black uppercase tracking-[0.2em] text-zinc-500">Issues</th>
                    <th class="px-6 py-4 text-left text-[8px] font-black uppercase tracking-[0.2em] text-zinc-500">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/[0.03]">
                @forelse($vehicles as $vehicle)
                <tr class="group hover:bg-white/[0.02] transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-primary/10 border border-primary/20 flex items-center justify-center">
                                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                            </div>
                            <div>
                                <span class="text-[11px] font-bold text-white">{{ $vehicle->name }}</span>
                                <p class="text-[9px] text-zinc-600 font-mono">ID: {{ $vehicle->id }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-[10px] font-mono text-zinc-400">{{ $vehicle->license_plate ?? '—' }}</span>
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $statusColor = match($vehicle->status ?? 'active') {
                                'active' => 'emerald',
                                'in_shop' => 'amber',
                                'inactive' => 'red',
                                default => 'zinc'
                            };
                        @endphp
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-{{ $statusColor }}-500/10 border border-{{ $statusColor }}-500/20 rounded-full">
                            <span class="w-1 h-1 rounded-full bg-{{ $statusColor }}-500"></span>
                            <span class="text-[8px] font-black text-{{ $statusColor }}-400 uppercase tracking-widest">{{ ucfirst(str_replace('_', ' ', $vehicle->status ?? 'active')) }}</span>
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-[10px] font-mono text-zinc-400">{{ number_format($vehicle->current_odometer ?? 0, 1) }} km</span>
                    </td>
                    <td class="px-6 py-4">
                        @if($vehicle->next_service_at)
                            @php $servicePercent = min(100, (($vehicle->current_odometer ?? 0) / $vehicle->next_service_at) * 100); @endphp
                            <div class="flex items-center gap-2">
                                <div class="w-16 h-1.5 bg-white/5 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full {{ $servicePercent > 90 ? 'bg-red-500' : ($servicePercent > 70 ? 'bg-amber-500' : 'bg-emerald-500') }}" style="width: {{ $servicePercent }}%"></div>
                                </div>
                                <span class="text-[9px] font-mono text-zinc-500">{{ number_format($vehicle->next_service_at) }} km</span>
                            </div>
                        @else
                            <span class="text-[9px] text-zinc-600">—</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if(($vehicle->issues_count ?? 0) > 0)
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-red-500/10 border border-red-500/20 text-[10px] font-bold text-red-400">{{ $vehicle->issues_count }}</span>
                        @else
                            <span class="text-[9px] text-zinc-600">0</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <a href="{{ route('vehicles.track', $vehicle) }}" target="_blank" class="text-[9px] font-bold text-primary hover:text-white uppercase tracking-widest transition-colors">Track →</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-16 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <svg class="w-10 h-10 text-zinc-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                            <p class="text-[11px] text-zinc-600">No vehicles registered yet.</p>
                            @if(auth()->user()->is_admin ?? false)
                            <a href="{{ route('vehicles.create') }}" class="text-[10px] font-bold text-primary hover:text-white transition-colors">+ Deploy First Unit</a>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        @if($vehicles->hasPages())
        <div class="px-6 py-4 border-t border-white/5 flex items-center justify-between">
            <span class="text-[9px] text-zinc-500 font-mono">Showing {{ $vehicles->firstItem() }}-{{ $vehicles->lastItem() }} of {{ $vehicles->total() }}</span>
            <div class="flex gap-1">
                @if($vehicles->onFirstPage())
                    <span class="px-3 py-1.5 bg-white/5 text-zinc-600 text-[9px] font-bold rounded cursor-not-allowed">Prev</span>
                @else
                    <a href="{{ $vehicles->previousPageUrl() }}" class="px-3 py-1.5 bg-white/5 hover:bg-white/10 text-zinc-400 text-[9px] font-bold rounded transition-colors">Prev</a>
                @endif
                @if($vehicles->hasMorePages())
                    <a href="{{ $vehicles->nextPageUrl() }}" class="px-3 py-1.5 bg-white/5 hover:bg-white/10 text-zinc-400 text-[9px] font-bold rounded transition-colors">Next</a>
                @else
                    <span class="px-3 py-1.5 bg-white/5 text-zinc-600 text-[9px] font-bold rounded cursor-not-allowed">Next</span>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
