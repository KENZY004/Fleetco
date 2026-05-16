@extends('driver.layouts.app')

@section('content')
<div class="flex flex-col gap-6 w-full max-w-7xl mx-auto">
    
    {{-- Header --}}
    <div class="flex justify-between items-end">
        <div>
            <span class="text-[10px] text-orange-500 font-bold uppercase tracking-[0.3em] mb-1 block">Maintenance Intelligence</span>
            <h1 class="text-3xl font-bold text-white tracking-tight font-heading uppercase">Service History</h1>
        </div>
        <div class="px-4 py-2 rounded-xl bg-white/5 border border-white/10 text-[10px] font-bold text-zinc-500 uppercase tracking-widest">
            {{ $records->count() }} Reports Filed
        </div>
    </div>

    {{-- History Feed --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($records as $record)
            <div class="glass-obsidian rounded-[2rem] border border-white/10 p-6 flex flex-col justify-between hover:border-white/20 transition-all group">
                <div class="space-y-4">
                    <div class="flex justify-between items-start">
                        <div class="h-10 w-10 rounded-xl bg-white/5 flex items-center justify-center text-orange-500">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        @php
                            $statusColor = [
                                'reported' => 'text-yellow-500 bg-yellow-500/10 border-yellow-500/20',
                                'in_progress' => 'text-blue-500 bg-blue-500/10 border-blue-500/20',
                                'resolved' => 'text-emerald-500 bg-emerald-500/10 border-emerald-500/20',
                            ][$record->status] ?? 'text-zinc-500 bg-zinc-500/10 border-zinc-500/20';
                        @endphp
                        <span class="text-[9px] px-3 py-1 rounded-full uppercase font-black border {{ $statusColor }}">
                            {{ str_replace('_', ' ', $record->status) }}
                        </span>
                    </div>

                    <div>
                        <h3 class="text-xl font-bold text-white mb-2">{{ $record->service_type }}</h3>
                        <p class="text-xs text-zinc-500 leading-relaxed italic">"{{ $record->notes }}"</p>
                    </div>

                    @if($record->status === 'resolved')
                        <div class="p-4 rounded-2xl bg-emerald-500/[0.03] border border-emerald-500/10">
                            <div class="text-[8px] font-bold text-emerald-500 uppercase tracking-widest mb-1">Resolution Detail</div>
                            <div class="text-[10px] text-zinc-400 font-medium">Issue fixed by Maintenance Command. Odometer recorded at {{ number_format($record->odometer_reading, 0) }} KM.</div>
                        </div>
                    @endif
                </div>

                <div class="mt-8 pt-4 border-t border-white/5 flex items-center justify-between">
                    <div class="text-[10px] font-bold text-zinc-600 uppercase tracking-widest">{{ $record->created_at->format('M d, Y') }}</div>
                    <div class="text-[8px] font-black text-zinc-700 uppercase tracking-widest">Ticket #MNT-{{ str_pad($record->id, 4, '0', STR_PAD_LEFT) }}</div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-24 flex flex-col items-center justify-center opacity-40">
                <div class="w-24 h-24 rounded-full bg-white/5 flex items-center justify-center mb-6">
                    <svg class="w-10 h-10 text-zinc-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <h4 class="text-xs font-black uppercase tracking-[0.4em] text-zinc-500 mb-2">No Records Found</h4>
                <p class="text-[10px] text-zinc-600 font-medium uppercase tracking-widest text-center max-w-xs leading-relaxed">Your mechanical reporting history is currently empty. Communications verified.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
