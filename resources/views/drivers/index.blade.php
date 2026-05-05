@extends('layouts.app')

@section('content')
<div class="space-y-8" x-data="driverManager()">

    {{-- Page Header --}}
    <div class="flex items-center justify-between">
        <div>
            <div class="text-[10px] font-bold tracking-widest text-orange-500 uppercase mb-2">Fleet Intelligence</div>
            <h1 class="font-heading text-3xl font-bold tracking-tight">Driver Management</h1>
            <p class="text-zinc-500 text-sm mt-1">Register, assign and monitor all fleet operators.</p>
        </div>
        <button
            @click="showAddModal = true"
            class="flex items-center gap-3 px-6 py-3 bg-white text-black rounded-full text-[11px] font-bold uppercase tracking-wider hover:bg-white/90 transition-all shadow-lg active:scale-95"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            Register Driver
        </button>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm font-medium flex items-center gap-3">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- Driver Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse($drivers as $driver)
        <div class="fleetco-card rounded-[2rem] p-8 flex flex-col gap-6 relative overflow-hidden">
            {{-- Risk Score Ring --}}
            <div class="flex items-start justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-2xl font-black text-primary">
                        {{ strtoupper(substr($driver->name, 0, 1)) }}
                    </div>
                    <div>
                        <h3 class="font-heading text-lg font-bold text-white">{{ $driver->name }}</h3>
                        <div class="text-[10px] text-zinc-500 font-medium mt-1">
                            {{ $driver->user ? $driver->user->email : 'No Account Linked' }}
                        </div>
                    </div>
                </div>

                {{-- Risk Badge --}}
                <div class="text-right">
                    <div class="text-[9px] text-zinc-500 uppercase font-bold tracking-wider mb-1">Safety Score</div>
                    <div class="font-heading text-2xl font-bold {{ $driver->risk_score >= 80 ? 'text-emerald-400' : ($driver->risk_score >= 50 ? 'text-orange-400' : 'text-red-400') }}">
                        {{ number_format($driver->risk_score, 0) }}<span class="text-sm text-zinc-500">%</span>
                    </div>
                </div>
            </div>

            {{-- Stats Row --}}
            <div class="grid grid-cols-2 gap-4">
                <div class="p-4 rounded-xl bg-white/5">
                    <div class="text-[9px] text-zinc-500 uppercase font-bold tracking-wider mb-1">Total Pings</div>
                    <div class="text-lg font-bold text-white">{{ $driver->telematics_logs_count }}</div>
                </div>
                <div class="p-4 rounded-xl bg-white/5">
                    <div class="text-[9px] text-zinc-500 uppercase font-bold tracking-wider mb-1">Risk Events</div>
                    <div class="text-lg font-bold {{ $driver->riskEvents->count() > 0 ? 'text-red-400' : 'text-emerald-400' }}">
                        {{ $driver->riskEvents->count() }}
                    </div>
                </div>
            </div>

            {{-- Assigned Vehicle --}}
            @php $assignedVehicle = $vehicles->where('current_driver_id', $driver->id)->first(); @endphp
            <div class="flex items-center justify-between p-4 rounded-xl border border-white/5 bg-white/3">
                <div>
                    <div class="text-[9px] text-zinc-500 uppercase font-bold tracking-wider mb-1">Assigned Vehicle</div>
                    @if($assignedVehicle)
                        <div class="text-sm font-bold text-white">{{ $assignedVehicle->name }}</div>
                        <div class="text-[10px] text-primary">{{ $assignedVehicle->license_plate }}</div>
                    @else
                        <div class="text-sm text-zinc-600 font-medium">Unassigned</div>
                    @endif
                </div>
                <div class="h-2 w-2 rounded-full {{ $assignedVehicle ? 'bg-primary shadow-[0_0_10px_#ff8a00]' : 'bg-zinc-700' }}"></div>
            </div>

            {{-- Actions --}}
            <div class="flex gap-3">
                <button
                    @click="openEditModal({{ $driver->toJson() }}, {{ $assignedVehicle ? $assignedVehicle->id : 'null' }})"
                    class="flex-1 py-3 border border-white/10 rounded-full text-[10px] font-bold uppercase tracking-wider text-zinc-400 hover:border-white/30 hover:text-white transition-all"
                >
                    Edit Driver
                </button>
                <form method="POST" action="{{ route('drivers.reset-score', $driver) }}" class="flex-1">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="w-full py-3 border border-emerald-500/20 rounded-full text-[10px] font-bold uppercase tracking-wider text-emerald-500 hover:bg-emerald-500/10 transition-all">
                        Reset Score
                    </button>
                </form>
                <form method="POST" action="{{ route('drivers.destroy', $driver) }}" onsubmit="return confirm('Remove {{ $driver->name }} from the fleet?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-3 border border-red-500/20 rounded-full text-red-500 hover:bg-red-500/10 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="col-span-3 fleetco-card rounded-[2rem] p-16 text-center">
            <div class="w-20 h-20 rounded-full border border-white/5 flex items-center justify-center mx-auto mb-6 opacity-20">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <div class="text-[11px] text-zinc-600 uppercase font-bold tracking-wider">No drivers registered</div>
            <p class="text-zinc-700 text-sm mt-2">Click "Register Driver" to add your first fleet operator.</p>
        </div>
        @endforelse
    </div>

    {{-- ADD DRIVER MODAL --}}
    <div
        x-show="showAddModal"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        class="fixed inset-0 z-[9999] flex items-center justify-center p-6 bg-black/70 backdrop-blur-sm"
        @click.self="showAddModal = false"
    >
        <div class="glass-obsidian rounded-[2rem] p-10 w-full max-w-md border border-white/10 shadow-2xl">
            <div class="text-[10px] text-orange-500 uppercase font-bold tracking-widest mb-2">Fleet Operations</div>
            <h2 class="font-heading text-2xl font-bold mb-8">Register New Driver</h2>

            <form method="POST" action="{{ route('drivers.store') }}" class="space-y-6">
                @csrf
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-wider text-zinc-500 block mb-2">Driver Name</label>
                    <input type="text" name="name" required placeholder="e.g. John Reyes" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-zinc-600 text-sm outline-none focus:border-primary/50 transition-colors">
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-wider text-zinc-500 block mb-2">Phone Number</label>
                    <input type="text" name="phone_number" placeholder="e.g. +63 917 123 4567" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-zinc-600 text-sm outline-none focus:border-primary/50 transition-colors">
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-wider text-zinc-500 block mb-2">License Number</label>
                    <input type="text" name="license_number" placeholder="e.g. N01-23-456789" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-zinc-600 text-sm outline-none focus:border-primary/50 transition-colors">
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-wider text-zinc-500 block mb-2">Link to User Account <span class="text-zinc-700">(Optional)</span></label>
                    <select name="user_id" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm outline-none focus:border-primary/50 transition-colors">
                        <option value="">— No account linked —</option>
                        @foreach($unlinkedUsers as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-4 pt-2">
                    <button type="button" @click="showAddModal = false" class="flex-1 py-4 border border-white/10 rounded-full text-[10px] font-bold uppercase tracking-wider text-zinc-400 hover:text-white transition-all">Cancel</button>
                    <button type="submit" class="flex-1 py-4 bg-white text-black rounded-full text-[10px] font-bold uppercase tracking-wider hover:bg-white/90 transition-all">Register Driver</button>
                </div>
            </form>
        </div>
    </div>

    {{-- EDIT DRIVER MODAL --}}
    <div
        x-show="showEditModal"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        class="fixed inset-0 z-[9999] flex items-center justify-center p-6 bg-black/70 backdrop-blur-sm"
        @click.self="showEditModal = false"
    >
        <div class="glass-obsidian rounded-[2rem] p-10 w-full max-w-md border border-white/10 shadow-2xl">
            <div class="text-[10px] text-orange-500 uppercase font-bold tracking-widest mb-2">Editing Driver</div>
            <h2 class="font-heading text-2xl font-bold mb-8" x-text="editDriver.name"></h2>

            <form :action="`/drivers/${editDriver.id}`" method="POST" class="space-y-6">
                @csrf
                @method('PATCH')
                <input type="hidden" name="_method" value="PATCH">
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-wider text-zinc-500 block mb-2">Driver Name</label>
                    <input type="text" name="name" :value="editDriver.name" required class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm outline-none focus:border-primary/50 transition-colors">
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-wider text-zinc-500 block mb-2">Phone Number</label>
                    <input type="text" name="phone_number" :value="editDriver.phone_number" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm outline-none focus:border-primary/50 transition-colors">
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-wider text-zinc-500 block mb-2">License Number</label>
                    <input type="text" name="license_number" :value="editDriver.license_number" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm outline-none focus:border-primary/50 transition-colors">
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-wider text-zinc-500 block mb-2">Assign Vehicle</label>
                    <select name="vehicle_id" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm outline-none focus:border-primary/50 transition-colors">
                        <option value="">— Unassign —</option>
                        @foreach($vehicles as $vehicle)
                            <option :value="{{ $vehicle->id }}" :selected="editVehicleId == {{ $vehicle->id }}">{{ $vehicle->name }} ({{ $vehicle->license_plate }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-4 pt-2">
                    <button type="button" @click="showEditModal = false" class="flex-1 py-4 border border-white/10 rounded-full text-[10px] font-bold uppercase tracking-wider text-zinc-400 hover:text-white transition-all">Cancel</button>
                    <button type="submit" class="flex-1 py-4 bg-white text-black rounded-full text-[10px] font-bold uppercase tracking-wider hover:bg-white/90 transition-all">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    function driverManager() {
        return {
            showAddModal: false,
            showEditModal: false,
            editDriver: {},
            editVehicleId: null,

            openEditModal(driver, vehicleId) {
                this.editDriver = driver;
                this.editVehicleId = vehicleId;
                this.showEditModal = true;
            }
        }
    }
</script>
@endpush
