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

    <div class="hidden md:block glass-obsidian rounded-3xl border border-white/10 overflow-hidden">
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
                               ($route->status === 'completed' ? 'bg-blue-500/10 text-blue-500' : 
                               ($route->status === 'draft' ? 'bg-orange-500/10 text-orange-500' : 'bg-zinc-500/10 text-zinc-500')) }}">
                            {{ $route->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-zinc-400">
                            {{ $route->scheduled_for ? $route->scheduled_for->format('M d, H:i') : 'Immediate' }}
                        </div>
                    </td>
                    <td class="px-6 py-4 text-right flex justify-end gap-2">
                        <a href="{{ route('fleet.routes.show', $route->id) }}" class="p-2 text-zinc-600 hover:text-white transition-colors inline-block" title="View Details">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </a>
                        <form method="POST" action="{{ route('fleet.routes.destroy', $route->id) }}" onsubmit="return confirm('Permanently delete this route? This cannot be undone.')" class="inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 text-zinc-600 hover:text-red-500 transition-colors" title="Delete Route">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-zinc-500 italic">No routes found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Mobile Card List --}}
    <div class="md:hidden space-y-4">
        @forelse($routes as $route)
            <div class="p-6 rounded-[2rem] bg-zinc-950 border border-white/5 space-y-4">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-lg font-bold text-white mb-1">{{ $route->name }}</h3>
                        <div class="text-[10px] text-zinc-500 uppercase tracking-widest font-bold">{{ count($route->waypoints) }} Waypoints</div>
                    </div>
                    <span class="text-[9px] px-2 py-1 rounded uppercase font-black tracking-wider
                        {{ $route->status === 'active' ? 'bg-emerald-500/10 text-emerald-500' : 
                           ($route->status === 'completed' ? 'bg-blue-500/10 text-blue-500' : 
                           ($route->status === 'draft' ? 'bg-orange-500/10 text-orange-500' : 'bg-zinc-500/10 text-zinc-500')) }}">
                        {{ $route->status }}
                    </span>
                </div>
                
                <div class="grid grid-cols-2 gap-4 pt-2">
                    <div>
                        <div class="text-[8px] text-zinc-500 uppercase font-black tracking-widest mb-1">Driver</div>
                        <div class="text-xs text-zinc-300 font-bold truncate">{{ $route->driver?->name ?? 'Unassigned' }}</div>
                    </div>
                    <div>
                        <div class="text-[8px] text-zinc-500 uppercase font-black tracking-widest mb-1">Vehicle</div>
                        <div class="text-xs text-zinc-300 font-bold truncate">{{ $route->vehicle?->license_plate ?? '—' }}</div>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-white/5">
                    <div class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest">
                        {{ $route->scheduled_for ? $route->scheduled_for->format('M d, H:i') : 'Immediate' }}
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('fleet.routes.show', $route->id) }}" class="p-3 bg-white/5 border border-white/10 rounded-xl text-zinc-400">
                             <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </a>
                        <form method="POST" action="{{ route('fleet.routes.destroy', $route->id) }}" onsubmit="return confirm('Delete this route?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-3 bg-red-500/5 border border-red-500/10 rounded-xl text-red-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="p-12 rounded-[2rem] bg-zinc-950 border border-white/5 text-center text-zinc-500 italic">No routes found.</div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $routes->links() }}
    </div>
</div>
@endsection
