@extends('layouts.app')

@section('content')
<div class="p-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-white tracking-tight">Fleet Routes</h1>
            <p class="text-zinc-500 text-sm">Manage and assign routes to your drivers.</p>
        </div>
        <a href="{{ route('fleet.routes.create') }}" class="px-6 py-3 bg-primary text-black font-bold rounded-xl hover:bg-orange-600 transition-all">
            Build New Route
        </a>
    </div>

    <div class="glass-obsidian rounded-3xl border border-white/10 overflow-hidden">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-white/5 border-b border-white/10 text-[10px] uppercase tracking-widest text-zinc-500 font-bold">
                    <th class="px-6 py-4">Route Name</th>
                    <th class="px-6 py-4">Driver</th>
                    <th class="px-6 py-4">Vehicle</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4">Scheduled For</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse($routes as $route)
                <tr class="hover:bg-white/[0.02] transition-colors group">
                    <td class="px-6 py-4">
                        <div class="text-sm font-bold text-white">{{ $route->name }}</div>
                        <div class="text-[10px] text-zinc-500">{{ count($route->waypoints) }} Waypoints</div>
                    </td>
                    <td class="px-6 py-4">
                        @if($route->driver)
                            <div class="text-sm text-zinc-300 font-medium">{{ $route->driver->name }}</div>
                        @else
                            <span class="text-[10px] px-2 py-0.5 rounded bg-zinc-800 text-zinc-500 uppercase font-bold">Unassigned</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if($route->vehicle)
                            <div class="text-sm text-zinc-300 font-medium">{{ $route->vehicle->license_plate }}</div>
                        @else
                            <span class="text-[10px] px-2 py-0.5 rounded bg-zinc-800 text-zinc-500 uppercase font-bold">—</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-[10px] px-2 py-0.5 rounded uppercase font-bold 
                            {{ $route->status === 'active' ? 'bg-emerald-500/10 text-emerald-500' : 
                               ($route->status === 'completed' ? 'bg-blue-500/10 text-blue-500' : 'bg-zinc-500/10 text-zinc-500') }}">
                            {{ $route->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-zinc-400">
                            {{ $route->scheduled_for ? $route->scheduled_for->format('M d, H:i') : 'Immediate' }}
                        </div>
                    </td>
                    <td class="px-6 py-4 text-right">
                        {{-- Actions like View/Edit can go here --}}
                        <button class="p-2 text-zinc-600 hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-zinc-500 italic">No routes found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4 border-t border-white/5">
            {{ $routes->links() }}
        </div>
    </div>
</div>
@endsection
