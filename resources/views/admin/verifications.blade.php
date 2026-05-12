@extends('layouts.app')

@section('content')
<div class="space-y-8">

    {{-- Page Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="text-[10px] font-bold tracking-widest text-orange-500 uppercase mb-2">Super Admin</div>
            <h1 class="font-heading text-3xl font-bold tracking-tight">Pending Verifications</h1>
            <p class="text-zinc-500 text-sm mt-1">Fleet managers awaiting email verification.</p>
        </div>
        <div class="px-4 py-2 rounded-full bg-white/5 border border-white/10 text-sm font-bold text-zinc-400">
            {{ $pendingUsers->count() }} Pending
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm font-medium flex items-center gap-3">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- Verifications Table --}}
    <div class="fleetco-card rounded-[2rem] overflow-hidden">
        <div class="py-5 px-8 border-b border-white/5 flex justify-between items-center">
            <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-500">Unverified Fleet Managers</span>
        </div>

        @if($pendingUsers->isEmpty())
            <div class="flex flex-col items-center justify-center py-20 opacity-30">
                <svg class="w-10 h-10 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-xs text-zinc-600 uppercase tracking-widest">All clear — no pending verifications</p>
            </div>
        @else
            <div class="divide-y divide-white/5">
                @foreach($pendingUsers as $user)
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-6 hover:bg-white/[0.02] transition-colors">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center">
                            <svg class="w-6 h-6 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <div>
                            <div class="font-bold text-white">{{ $user->name }}</div>
                            <div class="text-sm text-zinc-500">{{ $user->email }}</div>
                        </div>
                    </div>

                    <div class="flex items-center gap-6">
                        <div class="text-right">
                            <div class="text-[9px] text-zinc-600 uppercase font-bold tracking-widest mb-1">Fleet</div>
                            <div class="text-sm text-white font-medium">{{ $user->fleet->name ?? '—' }}</div>
                        </div>
                        <div class="text-right">
                            <div class="text-[9px] text-zinc-600 uppercase font-bold tracking-widest mb-1">Registered</div>
                            <div class="text-sm text-zinc-400">{{ $user->created_at->diffForHumans() }}</div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="px-3 py-1 rounded-full bg-yellow-500/10 text-yellow-500 text-[9px] font-bold uppercase tracking-wider">Unverified</span>
                            <form method="POST" action="{{ route('admin.verifications.force', $user) }}">
                                @csrf
                                @method('POST')
                                <button type="submit"
                                    onclick="return confirm('Force verify {{ $user->email }}? This bypasses email confirmation.')"
                                    class="px-5 py-2.5 bg-white text-black rounded-full text-[10px] font-bold uppercase tracking-wider hover:bg-zinc-200 transition-all active:scale-95"
                                >
                                    Force Verify
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>

</div>
@endsection
