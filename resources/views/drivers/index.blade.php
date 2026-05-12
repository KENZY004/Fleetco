@extends('layouts.app')

@section('content')
<div class="space-y-8" x-data="driverManager()">

    {{-- Page Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="text-[10px] font-bold tracking-widest text-orange-500 uppercase mb-2">Operator Management</div>
            <h1 class="font-heading text-3xl font-bold tracking-tight">Fleet Drivers</h1>
            <p class="text-zinc-500 text-sm mt-1">Manage personnel, safety scores and system access.</p>
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
    @if(session('error'))
    <div class="p-4 rounded-2xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm font-medium flex items-center gap-3">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        {{ session('error') }}
    </div>
    @endif

    {{-- INVITE DRIVER PANEL --}}
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

        {{-- Send Invite Form --}}
        <div class="lg:col-span-2 fleetco-card rounded-[2rem] p-8">
            <div class="text-[10px] font-bold tracking-widest text-orange-500 uppercase mb-2">Driver Onboarding</div>
            <h2 class="font-heading text-lg font-bold mb-1">Invite a Driver</h2>
            <p class="text-sm text-zinc-500 mb-6">Send a secure invite link via email. Links expire after 48 hours.</p>
            <form method="POST" action="{{ route('fleet.invite.send') }}" class="flex gap-3">
                @csrf
                <input
                    type="email"
                    name="email"
                    placeholder="driver@example.com"
                    required
                    class="flex-1 bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-zinc-600 text-sm outline-none focus:border-orange-500/50 transition-colors"
                >
                <button type="submit" class="px-6 py-3 bg-white text-black rounded-xl text-[10px] font-bold uppercase tracking-wider hover:bg-zinc-200 transition-all active:scale-95 whitespace-nowrap">
                    Send Invite
                </button>
            </form>
            @error('email')
                <p class="text-red-400 text-xs mt-2">{{ $message }}</p>
            @enderror
        </div>

        {{-- Pending Invites Table --}}
        <div class="lg:col-span-3 fleetco-card rounded-[2rem] p-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <div class="text-[10px] font-bold tracking-widest text-orange-500 uppercase mb-1">Pending</div>
                    <h2 class="font-heading text-lg font-bold">Active Invitations</h2>
                </div>
                @if($pendingInvites->count() > 0)
                    <span class="px-3 py-1 rounded-full bg-orange-500/10 text-orange-500 text-[10px] font-bold uppercase tracking-wider">
                        {{ $pendingInvites->count() }} pending
                    </span>
                @endif
            </div>

            @if($pendingInvites->isEmpty())
                <div class="flex flex-col items-center justify-center py-8 opacity-30">
                    <svg class="w-8 h-8 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <p class="text-xs text-zinc-600 uppercase tracking-widest">No pending invitations</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($pendingInvites as $invite)
                    <div class="flex items-center gap-4 p-4 rounded-xl bg-white/[0.02] border border-white/5">
                        <div class="w-8 h-8 rounded-full bg-orange-500/10 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-medium text-white truncate">{{ $invite->email }}</div>
                            <div class="text-[10px] text-zinc-500 mt-0.5">Expires {{ $invite->expires_at->diffForHumans() }}</div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-1 rounded-full bg-yellow-500/10 text-yellow-500 text-[9px] font-bold uppercase tracking-wider">Pending</span>
                            <form method="POST" action="{{ route('fleet.invite.send') }}">
                                @csrf
                                <input type="hidden" name="email" value="{{ $invite->email }}">
                                <button type="submit" class="px-3 py-1.5 border border-white/10 rounded-lg text-[9px] font-bold uppercase tracking-wider text-zinc-400 hover:text-white hover:border-white/20 transition-all">
                                    Resend
                                </button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Driver Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse($drivers as $driver)
        <x-fleet-card>
            {{-- Header --}}
            <div class="flex items-start justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center relative">
                        <svg class="w-7 h-7 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        @if($driver->user_id)
                            <div class="absolute -top-1 -right-1 w-4 h-4 bg-emerald-500 rounded-full border-2 border-[#020202] flex items-center justify-center" title="System Linked">
                                <svg class="w-2 h-2 text-black" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            </div>
                        @endif
                    </div>
                    <div>
                        <h3 class="font-heading text-lg font-bold text-white">{{ $driver->name }}</h3>
                        <div class="text-[11px] text-zinc-500 font-bold tracking-wider">{{ $driver->license_number ?? 'NO_LICENSE_RECORDED' }}</div>
                    </div>
                </div>

                @php
                    $riskType = $driver->risk_score >= 80 ? 'success' : ($driver->risk_score >= 50 ? 'warning' : 'danger');
                @endphp
                <x-badge :type="$riskType">Score: {{ number_format($driver->risk_score, 0) }}%</x-badge>
            </div>

            {{-- Stats --}}
            <div class="grid grid-cols-2 gap-4">
                <div class="p-4 rounded-xl bg-white/5">
                    <div class="text-[9px] text-zinc-500 uppercase font-bold tracking-wider mb-1">Total Trips</div>
                    <div class="text-lg font-bold text-white">{{ $driver->trips_count }}</div>
                </div>
                <div class="p-4 rounded-xl bg-white/5">
                    <div class="text-[9px] text-zinc-500 uppercase font-bold tracking-wider mb-1">Risk Events</div>
                    <div class="text-lg font-bold text-red-500">{{ $driver->risk_events_count }}</div>
                </div>
            </div>

            {{-- Contact Info --}}
            <div class="p-4 rounded-xl border border-white/5 bg-white/3">
                <div class="text-[9px] text-zinc-500 uppercase font-bold tracking-wider mb-2">Contact Details</div>
                <div class="flex items-center gap-3">
                    <svg class="w-4 h-4 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    <span class="text-sm font-medium text-white">{{ $driver->phone_number ?? 'Not provided' }}</span>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex gap-3">
                <a
                    :href="`/drivers/${editDriver.id || '{{ $driver->id }}'}`"
                    class="flex-1 py-3 bg-white/5 border border-white/10 rounded-full text-[10px] font-bold uppercase tracking-wider text-zinc-400 hover:border-primary/50 hover:text-white transition-all text-center"
                >
                    View Scorecard
                </a>
                <button
                    @click="openEditModal({{ $driver->toJson() }})"
                    class="px-4 py-3 border border-white/10 rounded-full text-zinc-400 hover:border-white/30 hover:text-white transition-all"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </button>
                <form method="POST" action="{{ route('drivers.reset-score', $driver) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" title="Reset Score" class="px-4 py-3 border border-emerald-500/20 rounded-full text-emerald-500 hover:bg-emerald-500/10 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    </button>
                </form>
                <form method="POST" action="{{ route('drivers.destroy', $driver) }}" onsubmit="return confirm('Remove driver from system?')">
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
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <div class="text-[11px] text-zinc-600 uppercase font-bold tracking-wider">No drivers registered</div>
            <p class="text-zinc-700 text-sm mt-2">Click "Register Driver" to add your first operator.</p>
        </div>
        @endforelse
    </div>

    {{-- ADD DRIVER MODAL --}}
    <x-fleet-modal name="showAddModal" title="Register New Driver" subtitle="Operator Management">
        <form method="POST" action="{{ route('drivers.store') }}" class="space-y-5">
            @csrf
            <div>
                <label class="text-[10px] font-bold uppercase tracking-wider text-zinc-500 block mb-2">Full Name</label>
                <input type="text" name="name" required placeholder="e.g. John Doe" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-zinc-600 text-sm outline-none focus:border-primary/50 transition-colors">
            </div>
            <div>
                <label class="text-[10px] font-bold uppercase tracking-wider text-zinc-500 block mb-2">Phone Number</label>
                <input type="text" name="phone_number" placeholder="+1 (555) 000-0000" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-zinc-600 text-sm outline-none focus:border-primary/50 transition-colors">
            </div>
            <div>
                <label class="text-[10px] font-bold uppercase tracking-wider text-zinc-500 block mb-2">License Number</label>
                <input type="text" name="license_number" placeholder="DL-123456789" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-zinc-600 text-sm outline-none focus:border-primary/50 transition-colors uppercase">
            </div>
            <div>
                <label class="text-[10px] font-bold uppercase tracking-wider text-zinc-500 block mb-2">Link User Account</label>
                <select name="user_id" class="w-full bg-zinc-900 border border-white/10 rounded-xl px-4 py-3 text-white text-sm outline-none focus:border-primary/50 transition-colors">
                    <option value="">— Don't link —</option>
                    @foreach($unlinkedUsers as $user)
                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                    @endforeach
                </select>
                <p class="text-[10px] text-zinc-600 mt-2">Only accounts with role 'driver' are shown.</p>
            </div>
            <div class="flex gap-4 pt-2">
                <button type="button" @click="showAddModal = false" class="flex-1 py-4 border border-white/10 rounded-full text-[10px] font-bold uppercase tracking-wider text-zinc-400 hover:text-white transition-all">Cancel</button>
                <button type="submit" class="flex-1 py-4 bg-white text-black rounded-full text-[10px] font-bold uppercase tracking-wider hover:bg-white/90 transition-all">Register</button>
            </div>
        </form>
    </x-fleet-modal>

    {{-- EDIT DRIVER MODAL --}}
    <x-fleet-modal name="showEditModal" title="Edit Driver Profile" subtitle="Operator Management">
        <form :action="`/drivers/${editDriver.id}`" method="POST" class="space-y-5">
            @csrf
            <input type="hidden" name="_method" value="PATCH">
            <div>
                <label class="text-[10px] font-bold uppercase tracking-wider text-zinc-500 block mb-2">Full Name</label>
                <input type="text" name="name" :value="editDriver.name" required class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm outline-none focus:border-primary/50 transition-colors">
            </div>
            <div>
                <label class="text-[10px] font-bold uppercase tracking-wider text-zinc-500 block mb-2">Phone Number</label>
                <input type="text" name="phone_number" :value="editDriver.phone_number" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm outline-none focus:border-primary/50 transition-colors">
            </div>
            <div>
                <label class="text-[10px] font-bold uppercase tracking-wider text-zinc-500 block mb-2">License Number</label>
                <input type="text" name="license_number" :value="editDriver.license_number" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm outline-none focus:border-primary/50 transition-colors uppercase">
            </div>
            <div>
                <label class="text-[10px] font-bold uppercase tracking-wider text-zinc-500 block mb-2">Vehicle Assignment</label>
                <select name="vehicle_id" class="w-full bg-zinc-900 border border-white/10 rounded-xl px-4 py-3 text-white text-sm outline-none focus:border-primary/50 transition-colors">
                    <option value="">— Not Assigned —</option>
                    @foreach($vehicles as $vehicle)
                        <option value="{{ $vehicle->id }}" :selected="editDriver.vehicle?.id == {{ $vehicle->id }}">{{ $vehicle->name }} ({{ $vehicle->license_plate }})</option>
                    @endforeach
                </select>
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
    function driverManager() {
        return {
            showAddModal: false,
            showEditModal: false,
            editDriver: {},

            openEditModal(driver) {
                this.editDriver = driver;
                this.showEditModal = true;
            }
        }
    }
</script>
@endpush
