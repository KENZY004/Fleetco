@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-white tracking-tight">Issues</h1>
            <p class="text-[11px] text-zinc-500 mt-1 font-mono uppercase tracking-widest">Maintenance &amp; Heuristic Alerts &mdash; {{ $issues->total() }} Tickets</p>
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="flex items-center gap-4 flex-wrap">
        <div class="flex bg-white/5 rounded-lg border border-white/5 overflow-hidden">
            <a href="{{ route('issues.index') }}" class="px-4 py-2 text-[9px] font-bold uppercase tracking-widest {{ !request('status') ? 'text-primary bg-primary/10' : 'text-zinc-500 hover:text-white' }} border-r border-white/5 transition-all">All</a>
            <a href="{{ route('issues.index', ['status' => 'open']) }}" class="px-4 py-2 text-[9px] font-bold uppercase tracking-widest {{ request('status') === 'open' ? 'text-primary bg-primary/10' : 'text-zinc-500 hover:text-white' }} border-r border-white/5 transition-all">Open</a>
            <a href="{{ route('issues.index', ['status' => 'critical']) }}" class="px-4 py-2 text-[9px] font-bold uppercase tracking-widest {{ request('status') === 'critical' ? 'text-primary bg-primary/10' : 'text-zinc-500 hover:text-white' }} border-r border-white/5 transition-all">Critical</a>
            <a href="{{ route('issues.index', ['status' => 'resolved']) }}" class="px-4 py-2 text-[9px] font-bold uppercase tracking-widest {{ request('status') === 'resolved' ? 'text-primary bg-primary/10' : 'text-zinc-500 hover:text-white' }} transition-all">Resolved</a>
        </div>
        <form action="{{ route('issues.index') }}" method="GET" class="relative flex-1 max-w-xs">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-4 w-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <input type="text" name="search" value="{{ request('search') }}" class="block w-full pl-10 pr-3 py-2 border border-white/5 rounded-lg bg-white/5 text-zinc-300 placeholder-zinc-600 focus:outline-none focus:border-white/20 text-[10px] font-mono transition-all" placeholder="Search issues...">
        </form>
    </div>

    <!-- Data Table -->
    <div class="fleetco-card rounded-xl overflow-hidden">
        <table class="w-full">
            <thead>
                <tr class="border-b border-white/5">
                    <th class="px-6 py-4 text-left text-[8px] font-black uppercase tracking-[0.2em] text-zinc-500">Priority</th>
                    <th class="px-6 py-4 text-left text-[8px] font-black uppercase tracking-[0.2em] text-zinc-500">Title</th>
                    <th class="px-6 py-4 text-left text-[8px] font-black uppercase tracking-[0.2em] text-zinc-500">Vehicle</th>
                    <th class="px-6 py-4 text-left text-[8px] font-black uppercase tracking-[0.2em] text-zinc-500">Status</th>
                    <th class="px-6 py-4 text-left text-[8px] font-black uppercase tracking-[0.2em] text-zinc-500">Created</th>
                    <th class="px-6 py-4 text-left text-[8px] font-black uppercase tracking-[0.2em] text-zinc-500">Description</th>
                    <th class="px-6 py-4 text-left text-[8px] font-black uppercase tracking-[0.2em] text-zinc-500">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/[0.03]">
                @forelse($issues as $issue)
                <tr class="group hover:bg-white/[0.02] transition-colors">
                    <td class="px-6 py-4">
                        @php
                            $priorityConfig = match($issue->priority) {
                                'critical' => ['color' => 'red', 'icon' => '🔴'],
                                'high' => ['color' => 'orange', 'icon' => '🟠'],
                                'medium' => ['color' => 'amber', 'icon' => '🟡'],
                                'low' => ['color' => 'emerald', 'icon' => '🟢'],
                                default => ['color' => 'zinc', 'icon' => '⚪'],
                            };
                        @endphp
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-{{ $priorityConfig['color'] }}-500/10 border border-{{ $priorityConfig['color'] }}-500/20 rounded-full">
                            <span class="text-[8px]">{{ $priorityConfig['icon'] }}</span>
                            <span class="text-[8px] font-black text-{{ $priorityConfig['color'] }}-400 uppercase tracking-widest">{{ ucfirst($issue->priority) }}</span>
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-[11px] font-bold text-white">{{ $issue->title }}</span>
                    </td>
                    <td class="px-6 py-4">
                        @if($issue->vehicle)
                            <span class="text-[10px] font-mono text-zinc-400">{{ $issue->vehicle->name }}</span>
                        @else
                            <span class="text-[9px] text-zinc-600">—</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $statusColor = match($issue->status) {
                                'open' => 'amber',
                                'in_progress' => 'blue',
                                'resolved' => 'emerald',
                                'closed' => 'zinc',
                                default => 'zinc',
                            };
                        @endphp
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-{{ $statusColor }}-500/10 border border-{{ $statusColor }}-500/20 rounded-full">
                            <span class="w-1 h-1 rounded-full bg-{{ $statusColor }}-500"></span>
                            <span class="text-[8px] font-black text-{{ $statusColor }}-400 uppercase tracking-widest">{{ ucfirst(str_replace('_', ' ', $issue->status)) }}</span>
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-[10px] font-mono text-zinc-500">{{ $issue->created_at->diffForHumans() }}</span>
                    </td>
                    <td class="px-6 py-4 max-w-xs">
                        <p class="text-[10px] text-zinc-500 truncate">{{ $issue->description }}</p>
                    </td>
                    <td class="px-6 py-4">
                        @if($issue->status !== 'resolved')
                        <form action="{{ route('issues.resolve', $issue) }}" method="POST">
                            @csrf
                            <button type="submit" class="text-[9px] font-bold text-emerald-500 hover:text-white uppercase tracking-widest transition-colors">Resolve ✓</button>
                        </form>
                        @else
                        <span class="text-[9px] font-bold text-zinc-700 uppercase tracking-widest">Completed</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-16 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <svg class="w-10 h-10 text-zinc-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="text-[11px] text-zinc-600">All systems nominal. No issues detected.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        @if($issues->hasPages())
        <div class="px-6 py-4 border-t border-white/5 flex items-center justify-between">
            <span class="text-[9px] text-zinc-500 font-mono">Showing {{ $issues->firstItem() }}-{{ $issues->lastItem() }} of {{ $issues->total() }}</span>
            <div class="flex gap-1">
                @if($issues->onFirstPage())
                    <span class="px-3 py-1.5 bg-white/5 text-zinc-600 text-[9px] font-bold rounded cursor-not-allowed">Prev</span>
                @else
                    <a href="{{ $issues->previousPageUrl() }}" class="px-3 py-1.5 bg-white/5 hover:bg-white/10 text-zinc-400 text-[9px] font-bold rounded transition-colors">Prev</a>
                @endif
                @if($issues->hasMorePages())
                    <a href="{{ $issues->nextPageUrl() }}" class="px-3 py-1.5 bg-white/5 hover:bg-white/10 text-zinc-400 text-[9px] font-bold rounded transition-colors">Next</a>
                @else
                    <span class="px-3 py-1.5 bg-white/5 text-zinc-600 text-[9px] font-bold rounded cursor-not-allowed">Next</span>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
