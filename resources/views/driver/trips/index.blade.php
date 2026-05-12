@extends('driver.layouts.app')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-6">
        <div class="text-[10px] text-[#ff8a00] font-black uppercase tracking-[0.2em] mb-1">Fleet Intelligence</div>
        <h1 class="text-2xl font-bold text-white tracking-tight">Trip History</h1>
    </div>

    <!-- STAT ROW -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-[#111] border border-[#1a1a1a] rounded-xl p-4 flex flex-col justify-between">
            <span class="text-[10px] text-[#555] font-black uppercase tracking-widest mb-3">Total Trips</span>
            <span class="text-3xl font-bold text-white tracking-tight">{{ number_format($totalTrips) }}</span>
        </div>

        <div class="bg-[#111] border border-[#1a1a1a] rounded-xl p-4 flex flex-col justify-between">
            <span class="text-[10px] text-[#555] font-black uppercase tracking-widest mb-3">Total Distance</span>
            <span class="text-3xl font-bold tracking-tight text-[#ff8a00]">{{ number_format($totalDistance, 1) }} KM</span>
        </div>

        <div class="bg-[#111] border border-[#1a1a1a] rounded-xl p-4 flex flex-col justify-between">
            <span class="text-[10px] text-[#555] font-black uppercase tracking-widest mb-3">Avg Safety Score</span>
            @php
                $scoreColor = $avgSafetyScore >= 80 ? 'text-[#10b981]' : ($avgSafetyScore >= 60 ? 'text-[#ff8a00]' : 'text-[#ef4444]');
            @endphp
            <span class="text-3xl font-bold tracking-tight {{ $scoreColor }}">{{ round($avgSafetyScore) }}</span>
        </div>

        <div class="bg-[#111] border border-[#1a1a1a] rounded-xl p-4 flex flex-col justify-between">
            <span class="text-[10px] text-[#555] font-black uppercase tracking-widest mb-3">Last Trip</span>
            <span class="text-xl font-bold text-white tracking-tight">{{ $lastTrip }}</span>
        </div>
    </div>

    <!-- MAIN CARD -->
    <div class="bg-[#111] border border-[#1a1a1a] rounded-xl overflow-hidden">
        <div class="p-6 border-b border-[#1a1a1a] flex justify-between items-center bg-[#111]">
            <span class="text-[10px] text-[#555] font-black uppercase tracking-widest">Recent Trips</span>
        </div>

        @if($trips->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-[#0a0a0a] border-b border-[#1a1a1a]">
                            <th class="px-6 py-4 text-[10px] font-black text-[#555] uppercase tracking-widest">Started</th>
                            <th class="px-6 py-4 text-[10px] font-black text-[#555] uppercase tracking-widest">Duration</th>
                            <th class="px-6 py-4 text-[10px] font-black text-[#555] uppercase tracking-widest">Distance</th>
                            <th class="px-6 py-4 text-[10px] font-black text-[#555] uppercase tracking-widest">Avg Speed</th>
                            <th class="px-6 py-4 text-[10px] font-black text-[#555] uppercase tracking-widest">Status</th>
                            <th class="px-6 py-4 text-[10px] font-black text-[#555] uppercase tracking-widest"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#1a1a1a]">
                        @foreach($trips as $trip)
                            @php
                                $status = $trip->end_time ? 'Completed' : 'Incomplete';
                                $statusColor = $status === 'Completed' ? 'bg-[#10b981]/10 text-[#10b981]' : 'bg-[#ff8a00]/10 text-[#ff8a00]';
                                
                                $duration = '—';
                                if ($trip->start_time && $trip->end_time) {
                                    $duration = $trip->start_time->diffInHours($trip->end_time) . 'h ' . ($trip->start_time->diffInMinutes($trip->end_time) % 60) . 'm';
                                } elseif ($trip->start_time) {
                                    $duration = $trip->start_time->diffInHours(now()) . 'h ' . ($trip->start_time->diffInMinutes(now()) % 60) . 'm';
                                }
                            @endphp
                            <tr class="hover:bg-white/[0.02] transition-colors {{ $loop->even ? 'bg-[#0d0d0d]' : 'bg-[#111111]' }}">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-white">{{ $trip->start_time ? $trip->start_time->format('M d, Y') : '—' }}</div>
                                    <div class="text-xs text-[#555]">{{ $trip->start_time ? $trip->start_time->format('H:i') : '—' }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-white font-mono">{{ $duration }}</td>
                                <td class="px-6 py-4 text-sm text-[#ff8a00] font-bold">{{ number_format($trip->distance, 1) }} KM</td>
                                <td class="px-6 py-4 text-sm text-white">{{ number_format($trip->average_speed, 1) }} KM/H</td>
                                <td class="px-6 py-4">
                                    <span class="text-[9px] px-2 py-1 rounded uppercase font-bold {{ $statusColor }}">
                                        {{ $status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="text-[#555] hover:text-white transition-colors cursor-pointer">
                                        <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-4 bg-[#0a0a0a] border-t border-[#1a1a1a]">
                {{-- RBAC: trip deletion restricted to Admin --}}
                <p class="text-xs text-[#555] italic text-center">Historical trip data is managed by the Fleet Administrator.</p>
            </div>
        @else
            <!-- Empty State -->
            <div class="flex flex-col items-center justify-center py-16">
                <div class="w-16 h-16 rounded-[1.2rem] bg-[#1a1a1a] flex items-center justify-center mb-6">
                    <svg class="w-8 h-8 text-[#555]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                </div>
                <span class="text-[12px] font-black uppercase tracking-widest text-[#444] mb-2">No Trips Recorded</span>
                <span class="text-sm text-[#555]">Trips are created automatically when you go On Duty.</span>
            </div>
        @endif
    </div>
</div>
@endsection
