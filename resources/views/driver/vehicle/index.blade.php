@extends('driver.layouts.app')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-6">
        <div class="text-[10px] text-primary font-black uppercase tracking-[0.2em] mb-1">Fleet Operations</div>
        <h1 class="text-2xl font-bold text-white tracking-tight font-heading">My Vehicle</h1>
    </div>

    <!-- STAT ROW -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="fleetco-card rounded-xl p-4 flex flex-col justify-between">
            <span class="text-[10px] text-zinc-500 font-black uppercase tracking-widest mb-3">Plate Number</span>
            <span class="text-2xl font-mono font-bold text-white tracking-tight">{{ $vehicle ? $vehicle->license_plate : 'N/A' }}</span>
        </div>

        <div class="fleetco-card rounded-xl p-4 flex flex-col justify-between">
            <span class="text-[10px] text-zinc-500 font-black uppercase tracking-widest mb-3">Status</span>
            @php
                $statusColor = 'text-zinc-500';
                if ($vehicle) {
                    $statusColor = in_array($vehicle->status, ['active', 'idle']) ? 'text-[#10b981]' : 'text-primary';
                }
            @endphp
            <span class="text-2xl font-bold tracking-tight uppercase {{ $statusColor }}">{{ $vehicle ? $vehicle->status : 'Unassigned' }}</span>
        </div>

        <div class="fleetco-card rounded-xl p-4 flex flex-col justify-between">
            <span class="text-[10px] text-zinc-500 font-black uppercase tracking-widest mb-3">Vehicle Type</span>
            <span class="text-2xl font-bold text-white tracking-tight">{{ $vehicle ? $vehicle->name : '—' }}</span>
        </div>

        <div class="fleetco-card rounded-xl p-4 flex flex-col justify-between">
            <span class="text-[10px] text-zinc-500 font-black uppercase tracking-widest mb-3">Assigned Since</span>
            <span class="text-xl font-bold text-white tracking-tight">{{ $vehicle ? $vehicle->created_at->diffForHumans() : '—' }}</span>
        </div>
    </div>

    <!-- MAIN CARD -->
    <div class="fleetco-card rounded-xl p-6">
        <div class="flex justify-between items-center mb-6">
            <span class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Vehicle Details</span>
            @if($vehicle)
            <span class="text-[9px] px-2 py-0.5 rounded uppercase font-bold bg-[#10b981]/10 text-[#10b981]">Active</span>
            @endif
        </div>

        @if($vehicle)
            <!-- Details List -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12">
                <!-- Column 1 -->
                <div class="flex flex-col">
                    <div class="flex justify-between py-4 border-b border-white/5">
                        <span class="text-xs text-zinc-500 font-medium">Model / Name</span>
                        <span class="text-xs text-white font-bold">{{ $vehicle->name }}</span>
                    </div>
                    <div class="flex justify-between py-4 border-b border-white/5">
                        <span class="text-xs text-zinc-500 font-medium">License Plate</span>
                        <span class="text-xs text-white font-bold font-mono">{{ $vehicle->license_plate }}</span>
                    </div>
                    <div class="flex justify-between py-4 border-b border-white/5">
                        <span class="text-xs text-zinc-500 font-medium">Current Status</span>
                        <span class="text-xs text-white font-bold uppercase">{{ $vehicle->status }}</span>
                    </div>
                </div>
                <!-- Column 2 -->
                <div class="flex flex-col">
                    <div class="flex justify-between py-4 border-b border-white/5">
                        <span class="text-xs text-zinc-500 font-medium">Odometer</span>
                        <span class="text-xs text-white font-bold">{{ number_format($vehicle->odometer) }} KM</span>
                    </div>
                    <div class="flex justify-between py-4 border-b border-white/5">
                        <span class="text-xs text-zinc-500 font-medium">Telemetry Token</span>
                        <span class="text-xs text-white font-bold font-mono">{{ substr($vehicle->telemetry_token, 0, 8) }}***</span>
                    </div>
                    <div class="flex justify-between py-4 border-b border-white/5">
                        <span class="text-xs text-zinc-500 font-medium">Assignment Date</span>
                        <span class="text-xs text-white font-bold">{{ $vehicle->created_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>
            
            <div class="mt-8">
                {{-- RBAC: vehicle editing is Fleet Manager only --}}
                <p class="text-xs text-zinc-600 italic">Vehicle assignments and profile edits are managed by the Fleet Administrator.</p>
            </div>
        @else
            <!-- Empty State -->
            <div class="flex flex-col items-center justify-center py-16">
                <div class="w-16 h-16 rounded-[1.2rem] bg-white/5 flex items-center justify-center mb-6">
                    <svg class="w-8 h-8 text-zinc-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                </div>
                <span class="text-[12px] font-black uppercase tracking-widest text-zinc-500 mb-2">No Vehicle Assigned</span>
                <span class="text-sm text-zinc-600">Contact your fleet manager to get assigned.</span>
            </div>
        @endif
    </div>
</div>
@endsection
