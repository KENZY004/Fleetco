@extends('layouts.app')

@section('content')
<div class="space-y-8" x-data="vehicleManager()">

    {{-- Page Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="text-[10px] font-bold tracking-widest text-orange-500 uppercase mb-2">Fleet Operations</div>
            <h1 class="font-heading text-3xl font-bold tracking-tight">Vehicle Management</h1>
            <p class="text-zinc-500 text-sm mt-1">Add, configure and assign all fleet vehicles.</p>
        </div>
        <button
            @click="showAddModal = true"
            class="flex items-center gap-3 px-6 py-3 bg-white text-black rounded-full text-[11px] font-bold uppercase tracking-wider hover:bg-white/90 transition-all shadow-lg active:scale-95"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            Add Vehicle
        </button>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm font-medium flex items-center gap-3">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- Vehicle Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse($vehicles as $vehicle)
        <x-fleet-card>
            {{-- Header --}}
            <div class="flex items-start justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center">
                        <svg class="w-7 h-7 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                    </div>
                    <div>
                        <h3 class="font-heading text-lg font-bold text-white">{{ $vehicle->name }}</h3>
                        <div class="text-[11px] text-primary font-bold tracking-wider">{{ $vehicle->license_plate }}</div>
                    </div>
                </div>

                @php
                    $statusType = [
                        'active' => 'success',
                        'idle' => 'warning',
                        'maintenance' => 'info',
                        'offline' => 'neutral',
                    ][$vehicle->status] ?? 'neutral';
                @endphp
                <x-badge :type="$statusType">{{ $vehicle->status }}</x-badge>
            </div>

            {{-- Stats --}}
            <div class="grid grid-cols-2 gap-4">
                <div class="p-4 rounded-xl bg-white/5">
                    <div class="text-[9px] text-zinc-500 uppercase font-bold tracking-wider mb-1">Total Pings</div>
                    <div class="text-lg font-bold text-white">{{ $vehicle->telematics_logs_count }}</div>
                </div>
                <div class="p-4 rounded-xl bg-white/5">
                    <div class="text-[9px] text-zinc-500 uppercase font-bold tracking-wider mb-1">Last Active</div>
                    <div class="text-sm font-bold text-white">
                        {{ $vehicle->latestTelematics ? $vehicle->latestTelematics->captured_at->diffForHumans() : '—' }}
                    </div>
                </div>
            </div>

            {{-- Driver Assignment --}}
            <div class="flex items-center justify-between p-4 rounded-xl border border-white/5 bg-white/3">
                <div>
                    <div class="text-[9px] text-zinc-500 uppercase font-bold tracking-wider mb-1">Assigned Driver</div>
                    @if($vehicle->driver)
                        <div class="text-sm font-bold text-white">{{ $vehicle->driver->name }}</div>
                        <div class="text-[10px] text-zinc-500">Safety: {{ number_format($vehicle->driver->risk_score, 0) }}%</div>
                    @else
                        <div class="text-sm text-zinc-600 font-medium">No driver assigned</div>
                    @endif
                </div>
                <div class="h-2 w-2 rounded-full {{ $vehicle->driver ? 'bg-primary shadow-[0_0_10px_#ff8a00]' : 'bg-zinc-700' }}"></div>
            </div>

            {{-- Telemetry Token --}}
            <div class="p-4 rounded-xl bg-black/30 border border-white/5 font-mono">
                <div class="text-[9px] text-zinc-500 uppercase font-bold tracking-wider mb-2">Uplink Token</div>
                <div class="flex items-center justify-between gap-3">
                    <span class="text-primary text-sm font-bold tracking-wider">{{ $vehicle->telemetry_token }}</span>
                    <form method="POST" action="{{ route('vehicles.regenerate-token', $vehicle) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit" title="Regenerate Token" class="text-zinc-600 hover:text-white transition-colors" onclick="return confirm('Regenerate token? The old token will stop working immediately.')">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        </button>
                    </form>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex gap-3">
                <a
                    :href="`/vehicles/${editVehicle.id || '{{ $vehicle->id }}'}/maintenance`"
                    class="flex-1 py-3 bg-white/5 border border-white/10 rounded-full text-[10px] font-bold uppercase tracking-wider text-zinc-400 hover:border-primary/50 hover:text-white transition-all text-center"
                >
                    Health & Maintenance
                </a>
                <button
                    @click="openEditModal({{ $vehicle->toJson() }}, {{ $vehicle->current_driver_id ?? 'null' }})"
                    class="px-4 py-3 border border-white/10 rounded-full text-zinc-400 hover:border-white/30 hover:text-white transition-all"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </button>
                <form method="POST" action="{{ route('vehicles.destroy', $vehicle) }}" onsubmit="return confirm('Remove {{ $vehicle->name }} from the fleet? All tracking data will be lost.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-3 border border-red-500/20 rounded-full text-red-500 hover:bg-red-500/10 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </form>
            </div>
        </x-fleet-card>
        @empty
        <div class="col-span-3 fleetco-card rounded-[2rem] p-16 text-center">
            <div class="w-20 h-20 rounded-full border border-white/5 flex items-center justify-center mx-auto mb-6 opacity-20">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
            </div>
            <div class="text-[11px] text-zinc-600 uppercase font-bold tracking-wider">No vehicles in fleet</div>
            <p class="text-zinc-700 text-sm mt-2">Click "Add Vehicle" to register your first vehicle.</p>
        </div>
        @endforelse
    </div>

    {{-- ADD VEHICLE MODAL --}}
    <x-fleet-modal name="showAddModal" title="Add New Vehicle">
        <form method="POST" action="{{ route('vehicles.store') }}" class="space-y-5">
            @csrf
            <div>
                <label class="text-[10px] font-bold uppercase tracking-wider text-zinc-500 block mb-2">Vehicle Name</label>
                <input type="text" name="name" required placeholder="e.g. Delivery Truck Alpha" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-zinc-600 text-sm outline-none focus:border-primary/50 transition-colors">
            </div>
            <div>
                <label class="text-[10px] font-bold uppercase tracking-wider text-zinc-500 block mb-2">License Plate</label>
                <input type="text" name="license_plate" required placeholder="e.g. MH-01-AB-1234" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-zinc-600 text-sm outline-none focus:border-primary/50 transition-colors uppercase">
            </div>
            <div>
                <label class="text-[10px] font-bold uppercase tracking-wider text-zinc-500 block mb-2">Initial Status</label>
                <select name="status" class="w-full bg-zinc-900 border border-white/10 rounded-xl px-4 py-3 text-white text-sm outline-none focus:border-primary/50 transition-colors">
                    <option value="offline">Offline</option>
                    <option value="idle">Idle</option>
                    <option value="active">Active</option>
                    <option value="maintenance">Maintenance</option>
                </select>
            </div>
            <div class="flex gap-4 pt-2">
                <button type="button" @click="showAddModal = false" class="flex-1 py-4 border border-white/10 rounded-full text-[10px] font-bold uppercase tracking-wider text-zinc-400 hover:text-white transition-all">Cancel</button>
                <button type="submit" class="flex-1 py-4 bg-white text-black rounded-full text-[10px] font-bold uppercase tracking-wider hover:bg-white/90 transition-all">Add to Fleet</button>
            </div>
        </form>
    </x-fleet-modal>

    {{-- EDIT VEHICLE MODAL --}}
    <x-fleet-modal name="showEditModal" title="Edit Vehicle" subtitle="Editing Vehicle">
        <form :action="`/vehicles/${editVehicle.id}`" method="POST" class="space-y-5">
            @csrf
            <input type="hidden" name="_method" value="PATCH">
            <div>
                <label class="text-[10px] font-bold uppercase tracking-wider text-zinc-500 block mb-2">Vehicle Name</label>
                <input type="text" name="name" :value="editVehicle.name" required class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm outline-none focus:border-primary/50 transition-colors">
            </div>
            <div>
                <label class="text-[10px] font-bold uppercase tracking-wider text-zinc-500 block mb-2">License Plate</label>
                <input type="text" name="license_plate" :value="editVehicle.license_plate" required class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm outline-none focus:border-primary/50 transition-colors uppercase">
            </div>
            <div>
                <label class="text-[10px] font-bold uppercase tracking-wider text-zinc-500 block mb-2">Status</label>
                <select name="status" class="w-full bg-zinc-900 border border-white/10 rounded-xl px-4 py-3 text-white text-sm outline-none focus:border-primary/50 transition-colors">
                    <option value="offline" :selected="editVehicle.status === 'offline'">Offline</option>
                    <option value="idle" :selected="editVehicle.status === 'idle'">Idle</option>
                    <option value="active" :selected="editVehicle.status === 'active'">Active</option>
                    <option value="maintenance" :selected="editVehicle.status === 'maintenance'">Maintenance</option>
                </select>
            </div>
            <div>
                <label class="text-[10px] font-bold uppercase tracking-wider text-zinc-500 block mb-2">Assign Driver</label>
                <select name="current_driver_id" class="w-full bg-zinc-900 border border-white/10 rounded-xl px-4 py-3 text-white text-sm outline-none focus:border-primary/50 transition-colors">
                    <option value="">— No driver —</option>
                    @foreach($allDrivers as $driver)
                        <option value="{{ $driver->id }}" :selected="editDriverId == {{ $driver->id }}">
                            {{ $driver->name }} 
                            @if($driver->vehicle)
                                — Currently with {{ $driver->vehicle->license_plate }}
                            @endif
                        </option>
                    @endforeach
                </select>
                <p class="text-[10px] text-zinc-600 mt-2 italic">Note: Drivers already assigned to other vehicles will be moved if selected.</p>
            </div>
            <div class="flex gap-4 pt-2">
                <button type="button" @click="showEditModal = false" class="flex-1 py-4 border border-white/10 rounded-full text-[10px] font-bold uppercase tracking-wider text-zinc-400 hover:text-white transition-all">Cancel</button>
                <button type="submit" class="flex-1 py-4 bg-white text-black rounded-full text-[10px] font-bold uppercase tracking-wider hover:bg-white/90 transition-all">Save Changes</button>
            </div>
        </form>
    </x-fleet-modal>

</div>
@endsection

@push('scripts')
<script>
    function vehicleManager() {
        return {
            showAddModal: false,
            showEditModal: false,
            editVehicle: {},
            editDriverId: null,

            openEditModal(vehicle, driverId) {
                this.editVehicle = vehicle;
                this.editDriverId = driverId;
                this.showEditModal = true;
            }
        }
    }
</script>
@endpush
