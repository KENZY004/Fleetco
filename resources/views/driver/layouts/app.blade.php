<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Driver Co-Pilot</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
    @stack('styles')
</head>
<body class="bg-[#020202] text-white antialiased h-screen flex flex-col md:flex-row overflow-hidden">
    <!-- Slim Operations Sidebar (Desktop) -->
    <aside class="hidden md:flex w-24 flex-shrink-0 glass-obsidian border-r border-white/5 flex-col items-center py-4 z-[3000]">
        <!-- Brand -->
        <div class="mb-12 group">
            <div class="w-12 h-12 rounded-2xl bg-orange-500 flex items-center justify-center shadow-lg shadow-orange-500/20 transition-transform hover:scale-105">
                <svg class="w-7 h-7 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 flex flex-col gap-4 w-full items-center overflow-y-auto custom-scrollbar px-2 py-4">
            {{-- HQ --}}
            <a href="{{ route('driver.dashboard') }}" class="w-full flex flex-col items-center gap-1 group">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center transition-all duration-300 {{ request()->routeIs('driver.dashboard') ? 'bg-orange-500 text-black shadow-lg shadow-orange-500/20' : 'text-zinc-500 hover:text-white hover:bg-white/5 border border-transparent hover:border-white/10' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                </div>
                <span class="text-[9px] font-bold uppercase tracking-[0.2em] {{ request()->routeIs('driver.dashboard') ? 'text-orange-500' : 'text-zinc-600 group-hover:text-zinc-400' }}">HQ</span>
            </a>

            {{-- Vehicle --}}
            <a href="{{ route('driver.vehicle') }}" class="w-full flex flex-col items-center gap-1 group">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center transition-all duration-300 {{ request()->routeIs('driver.vehicle') ? 'bg-orange-500 text-black shadow-lg shadow-orange-500/20' : 'text-zinc-500 hover:text-white hover:bg-white/5 border border-transparent hover:border-white/10' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                </div>
                <span class="text-[9px] font-bold uppercase tracking-[0.2em] {{ request()->routeIs('driver.vehicle') ? 'text-orange-500' : 'text-zinc-600 group-hover:text-zinc-400' }}">Fleet</span>
            </a>

            {{-- Trips --}}
            <a href="{{ route('driver.trips') }}" class="w-full flex flex-col items-center gap-1 group">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center transition-all duration-300 {{ request()->routeIs('driver.trips') ? 'bg-orange-500 text-black shadow-lg shadow-orange-500/20' : 'text-zinc-500 hover:text-white hover:bg-white/5 border border-transparent hover:border-white/10' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                </div>
                <span class="text-[9px] font-bold uppercase tracking-[0.2em] {{ request()->routeIs('driver.trips') ? 'text-orange-500' : 'text-zinc-600 group-hover:text-zinc-400' }}">Trips</span>
            </a>

            {{-- Risk --}}
            <a href="{{ route('driver.risk') }}" class="w-full flex flex-col items-center gap-1 group">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center transition-all duration-300 {{ request()->routeIs('driver.risk') ? 'bg-orange-500 text-black shadow-lg shadow-orange-500/20' : 'text-zinc-500 hover:text-white hover:bg-white/5 border border-transparent hover:border-white/10' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <span class="text-[9px] font-bold uppercase tracking-[0.2em] {{ request()->routeIs('driver.risk') ? 'text-orange-500' : 'text-zinc-600 group-hover:text-zinc-400' }}">Risk</span>
            </a>
        </nav>

        <!-- Profile / Logout -->
        <div class="mt-auto flex flex-col gap-6 items-center" x-data="{ open: false }">
            <div class="relative">
                <button @click="open = !open" class="w-12 h-12 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center hover:border-orange-500/50 transition-colors">
                    <svg class="w-5 h-5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </button>
                <div x-show="open" @click.away="open = false" style="display: none;" class="absolute bottom-0 left-16 w-48 glass-obsidian rounded-2xl p-2 shadow-2xl border border-white/10" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-x-4">
                    <div class="px-4 py-3 border-b border-white/5 mb-1">
                        <p class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Signed in as</p>
                        <p class="text-xs font-bold text-white truncate">{{ Auth::user()->name }}</p>
                    </div>
                    <a href="{{ route('driver.profile') }}" class="block px-4 py-2 text-[10px] font-bold uppercase tracking-widest text-zinc-400 hover:text-white hover:bg-white/5 rounded-lg transition-colors">Profile Settings</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-[10px] font-bold uppercase tracking-widest text-red-500 hover:bg-red-500/10 rounded-lg">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 relative min-w-0 flex flex-col h-screen overflow-hidden">
        <!-- Top Bar -->
        <header class="h-16 md:h-20 glass-obsidian border-b border-white/5 flex items-center justify-between px-4 md:px-8 shrink-0 z-20">
            <div>
                <div class="text-[8px] md:text-[10px] text-orange-500 font-bold uppercase tracking-[0.3em] mb-0.5 md:mb-1">Driver Intelligence</div>
                <h1 class="text-lg md:text-2xl font-bold text-white tracking-tight font-heading">Co-Pilot</h1>
            </div>
            
            <div class="flex items-center gap-3 md:gap-6">
                @php
                    $logStatus = \App\Models\DutyLog::where('driver_id', auth()->id())->whereNull('ended_at')->first()?->status;
                    $statusColor = $logStatus === 'on_duty' ? 'bg-[#10b981]' : ($logStatus === 'break' ? 'bg-orange-500' : 'bg-zinc-500');
                    $statusText = str_replace('_', ' ', $logStatus ?? 'off_duty');
                @endphp
                
                {{-- Status Badge --}}
                <div class="flex items-center gap-2 md:gap-3 px-3 md:px-4 py-1.5 md:py-2 rounded-full bg-white/5 border border-white/10">
                    <div class="relative flex items-center justify-center">
                        <div class="w-2 md:w-2.5 h-2 md:h-2.5 rounded-full {{ $statusColor }} {{ $logStatus === 'on_duty' ? 'animate-pulse shadow-[0_0_12px_rgba(16,185,129,0.4)]' : '' }}"></div>
                        @if($logStatus === 'on_duty')
                            <div class="absolute w-3 md:w-4 h-3 md:h-4 rounded-full border border-[#10b981]/30 animate-ping"></div>
                        @endif
                    </div>
                    <span class="text-[8px] md:text-[10px] font-bold uppercase tracking-widest text-zinc-400">{{ $statusText }}</span>
                </div>
                
                {{-- User Profile (Avatar only on mobile) --}}
                <div class="flex items-center gap-3 pl-3 md:pl-6 border-l border-white/10">
                    <div class="text-right hidden sm:block">
                        <div class="text-[11px] font-bold text-white tracking-tight">{{ Auth::user()->name }}</div>
                        <div class="text-[9px] font-bold text-zinc-500 uppercase tracking-widest">Operator</div>
                    </div>
                    <div class="w-8 h-8 md:w-10 md:h-10 rounded-lg md:rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-xs md:text-sm font-bold shadow-lg">
                        {{ substr(Auth::user()->name ?? 'D', 0, 1) }}
                    </div>
                </div>
            </div>
        </header>

        <!-- Scrollable Dashboard -->
        <div class="flex-1 overflow-y-auto p-4 md:p-8 pb-24 md:pb-8 custom-scrollbar bg-[#020202]">
            @yield('content')
        </div>

        <!-- Mobile Bottom Navigation -->
        <nav class="md:hidden fixed bottom-0 left-0 right-0 h-16 glass-obsidian border-t border-white/10 flex items-center justify-around px-4 z-[4000] pb-safe">
            <a href="{{ route('driver.dashboard') }}" class="flex flex-col items-center gap-1 {{ request()->routeIs('driver.dashboard') ? 'text-orange-500' : 'text-zinc-500' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span class="text-[8px] font-bold uppercase tracking-wider">HQ</span>
            </a>
            <a href="{{ route('driver.vehicle') }}" class="flex flex-col items-center gap-1 {{ request()->routeIs('driver.vehicle') ? 'text-orange-500' : 'text-zinc-500' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                <span class="text-[8px] font-bold uppercase tracking-wider">Fleet</span>
            </a>
            <a href="{{ route('driver.trips') }}" class="flex flex-col items-center gap-1 {{ request()->routeIs('driver.trips') ? 'text-orange-500' : 'text-zinc-500' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                <span class="text-[8px] font-bold uppercase tracking-wider">Trips</span>
            </a>
            <a href="{{ route('driver.profile') }}" class="flex flex-col items-center gap-1 {{ request()->routeIs('driver.profile') ? 'text-orange-500' : 'text-zinc-500' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span class="text-[8px] font-bold uppercase tracking-wider">Me</span>
            </a>
        </nav>
    </main>

    @stack('scripts')
</body>
</html>

</html>
