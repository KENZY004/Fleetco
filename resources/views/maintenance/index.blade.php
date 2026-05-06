@extends('layouts.app')

@section('content')
<div class="space-y-8" x-data="{ showLogModal: false }">
    
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div class="flex items-center gap-6">
            <button onclick="history.back()" class="h-10 w-10 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-zinc-400 hover:text-white hover:border-white/20 transition-all flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </button>
            <div class="h-16 w-16 rounded-2xl bg-zinc-900 border border-white/10 flex items-center justify-center text-primary">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div>
                <div class="text-[10px] font-bold text-zinc-500 uppercase tracking-[0.3em] mb-2">Fleet_Maintenance</div>
                <h1 class="font-heading text-3xl font-bold text-white">{{ $vehicle->name }}</h1>
                <p class="text-zinc-500 text-sm mt-1">Health & Service Diagnostic Hub</p>
            </div>
        </div>

        <button @click="showLogModal = true" class="px-6 py-3 bg-white text-black rounded-full text-[11px] font-bold uppercase tracking-wider hover:bg-white/90 transition-all shadow-lg active:scale-95">
            Log New Service
        </button>
    </div>

    {{-- Health Dashboard --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        {{-- Odometer --}}
        <div class="p-8 rounded-[2.5rem] bg-zinc-950 border border-white/5 relative overflow-hidden group">
            <div class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-6">Current Mileage</div>
            <div class="text-5xl font-black text-white mb-2 tracking-tighter">{{ number_format($vehicle->odometer, 1) }}</div>
            <div class="text-[10px] text-zinc-500 font-bold uppercase tracking-wider">Total Kilometers</div>
            <div class="absolute bottom-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/></svg>
            </div>
        </div>

        {{-- Service Status --}}
        <div class="p-8 rounded-[2.5rem] bg-zinc-950 border border-white/5 flex flex-col justify-between">
            <div>
                <div class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-6">Service Health</div>
                @if($serviceDue)
                    <div class="flex items-center gap-3 text-red-500 mb-2">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                        <span class="text-xl font-black tracking-tight uppercase">Service Due</span>
                    </div>
                    <p class="text-[11px] text-zinc-600 uppercase font-bold">{{ number_format($kmSinceService, 0) }} km since last log</p>
                @else
                    <div class="flex items-center gap-3 text-emerald-500 mb-2">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/></svg>
                        <span class="text-xl font-black tracking-tight uppercase">Optimal</span>
                    </div>
                    <p class="text-[11px] text-zinc-600 uppercase font-bold">Next service in {{ number_format(5000 - $kmSinceService, 0) }} km</p>
                @endif
            </div>
            <div class="mt-4 h-2 w-full bg-white/5 rounded-full overflow-hidden">
                <div class="h-full {{ $serviceDue ? 'bg-red-500' : 'bg-emerald-500' }}" style="width: {{ min(100, ($kmSinceService/5000)*100) }}%"></div>
            </div>
        </div>

        {{-- Financial Impact --}}
        <div class="p-8 rounded-[2.5rem] bg-zinc-950 border border-white/5">
            <div class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-6">Lifetime Maintenance</div>
            <div class="text-4xl font-black text-white mb-2 tracking-tighter">₹{{ number_format($records->sum('cost'), 0) }}</div>
            <div class="text-[10px] text-zinc-500 font-bold uppercase tracking-wider">Total Investment</div>
            <div class="mt-6 flex items-center justify-between text-[10px] font-bold uppercase tracking-widest">
                <span class="text-zinc-600">Avg Cost / Service</span>
                <span class="text-white">₹{{ $records->count() > 0 ? number_format($records->sum('cost') / $records->count(), 0) : 0 }}</span>
            </div>
        </div>
    </div>

    {{-- Maintenance Ledger --}}
    <div class="bg-zinc-950 border border-white/5 rounded-[2.5rem] overflow-hidden">
        <div class="px-8 py-6 border-b border-white/5 flex items-center justify-between bg-white/[0.02]">
            <h3 class="text-sm font-bold text-white uppercase tracking-widest">Service History</h3>
            <span class="px-3 py-1 rounded-full bg-white/5 text-[9px] font-black text-zinc-500 uppercase">{{ $records->count() }} Records</span>
        </div>
        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-white/5">
                    <th class="px-8 py-5 text-[9px] font-bold uppercase tracking-widest text-zinc-500">Service Date</th>
                    <th class="px-8 py-5 text-[9px] font-bold uppercase tracking-widest text-zinc-500">Service Type</th>
                    <th class="px-8 py-5 text-[9px] font-bold uppercase tracking-widest text-zinc-500">Odometer</th>
                    <th class="px-8 py-5 text-[9px] font-bold uppercase tracking-widest text-zinc-500 text-right">Cost</th>
                    <th class="px-8 py-5 text-[9px] font-bold uppercase tracking-widest text-zinc-500">Notes</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse($records as $record)
                    <tr class="hover:bg-white/[0.02] transition-colors">
                        <td class="px-8 py-6">
                            <div class="text-white font-bold text-sm">{{ $record->service_date->format('M d, Y') }}</div>
                        </td>
                        <td class="px-8 py-6">
                            <span class="px-3 py-1 rounded-lg bg-primary/10 border border-primary/20 text-primary text-[10px] font-bold uppercase tracking-wider">
                                {{ $record->service_type }}
                            </span>
                        </td>
                        <td class="px-8 py-6 font-mono text-zinc-400 text-xs">{{ number_format($record->odometer_reading, 0) }} km</td>
                        <td class="px-8 py-6 text-right font-black text-white">₹{{ number_format($record->cost, 0) }}</td>
                        <td class="px-8 py-6 text-zinc-500 text-xs max-w-xs truncate">{{ $record->notes ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-8 py-16 text-center">
                            <div class="text-zinc-600 italic text-sm mb-2">No maintenance records found for this vehicle.</div>
                            <button @click="showLogModal = true" class="text-primary text-[10px] font-bold uppercase tracking-widest hover:underline">Log your first service</button>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- LOG SERVICE MODAL --}}
    <x-fleet-modal name="showLogModal" title="Log Maintenance" subtitle="Health Diagnostic">
        <form action="{{ route('vehicles.maintenance.store', $vehicle->id) }}" method="POST" class="space-y-5">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-wider text-zinc-500 block mb-2">Service Type</label>
                    <input type="text" name="service_type" required placeholder="e.g. Oil Change" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm outline-none focus:border-primary/50 transition-colors">
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-wider text-zinc-500 block mb-2">Service Date</label>
                    <input type="date" name="service_date" required value="{{ date('Y-m-d') }}" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm outline-none focus:border-primary/50 transition-colors">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-wider text-zinc-500 block mb-2">Odometer Reading (KM)</label>
                    <input type="number" name="odometer_reading" required value="{{ $vehicle->odometer }}" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm outline-none focus:border-primary/50 transition-colors">
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-wider text-zinc-500 block mb-2">Cost (₹)</label>
                    <input type="number" name="cost" required placeholder="0.00" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm outline-none focus:border-primary/50 transition-colors">
                </div>
            </div>
            <div>
                <label class="text-[10px] font-bold uppercase tracking-wider text-zinc-500 block mb-2">Next Service At (KM)</label>
                <input type="number" name="next_service_at_km" value="{{ $vehicle->odometer + 5000 }}" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm outline-none focus:border-primary/50 transition-colors">
            </div>
            <div>
                <label class="text-[10px] font-bold uppercase tracking-wider text-zinc-500 block mb-2">Maintenance Notes</label>
                <textarea name="notes" rows="3" placeholder="Describe the service performed..." class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm outline-none focus:border-primary/50 transition-colors"></textarea>
            </div>
            <div class="flex gap-4 pt-2">
                <button type="button" @click="showLogModal = false" class="flex-1 py-4 border border-white/10 rounded-full text-[10px] font-bold uppercase tracking-wider text-zinc-400 hover:text-white transition-all">Cancel</button>
                <button type="submit" class="flex-1 py-4 bg-white text-black rounded-full text-[10px] font-bold uppercase tracking-wider hover:bg-white/90 transition-all">Save Record</button>
            </div>
        </form>
    </x-fleet-modal>

</div>
@endsection
