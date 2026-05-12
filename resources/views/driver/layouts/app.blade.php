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
<body class="bg-[#020202] text-white antialiased h-screen flex overflow-hidden">
    <!-- Sidebar -->
    <aside class="hidden md:flex w-[64px] flex-shrink-0 glass-obsidian border-r border-white/5 flex-col items-center py-6 z-[3000]">
        <!-- Brand -->
        <div class="mb-10">
            <div class="w-10 h-10 rounded-xl bg-primary flex items-center justify-center shadow-lg shadow-orange-500/20 transition-transform hover:scale-105">
                <svg class="w-6 h-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 flex flex-col gap-6 w-full items-center">
            <!-- HQ (Active) -->
            <a href="{{ route('driver.dashboard') }}" class="relative group flex flex-col items-center gap-1 transition-all" title="HQ">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center transition-all duration-300 {{ request()->routeIs('driver.dashboard') ? 'bg-primary text-black shadow-lg shadow-orange-500/20' : 'text-zinc-500 hover:text-white hover:bg-white/5' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                </div>
                <span class="text-[9px] font-black uppercase tracking-widest {{ request()->routeIs('driver.dashboard') ? 'text-primary' : 'text-zinc-600 group-hover:text-zinc-400' }}">HQ</span>
                @if(request()->routeIs('driver.dashboard'))
                <div class="hidden md:block absolute -left-2 top-1/2 -translate-y-1/2 w-1 h-6 bg-primary rounded-r-full shadow-[0_0_10px_#ff8a00]"></div>
                @endif
            </a>
            
            <!-- Vehicle -->
            <a href="{{ route('driver.vehicle') }}" class="relative group flex flex-col items-center gap-1 transition-all" title="Vehicle">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center transition-all duration-300 {{ request()->routeIs('driver.vehicle') ? 'bg-primary text-black shadow-lg shadow-orange-500/20' : 'text-zinc-500 hover:text-white hover:bg-white/5' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                </div>
                <span class="text-[9px] font-black uppercase tracking-widest {{ request()->routeIs('driver.vehicle') ? 'text-primary' : 'text-zinc-600 group-hover:text-zinc-400' }}">Vehicle</span>
                @if(request()->routeIs('driver.vehicle'))
                <div class="hidden md:block absolute -left-2 top-1/2 -translate-y-1/2 w-1 h-6 bg-primary rounded-r-full shadow-[0_0_10px_#ff8a00]"></div>
                @endif
            </a>
            
            <!-- Trips -->
            <a href="{{ route('driver.trips') }}" class="relative group flex flex-col items-center gap-1 transition-all" title="Trips">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center transition-all duration-300 {{ request()->routeIs('driver.trips') ? 'bg-primary text-black shadow-lg shadow-orange-500/20' : 'text-zinc-500 hover:text-white hover:bg-white/5' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                </div>
                <span class="text-[9px] font-black uppercase tracking-widest {{ request()->routeIs('driver.trips') ? 'text-primary' : 'text-zinc-600 group-hover:text-zinc-400' }}">Trips</span>
                @if(request()->routeIs('driver.trips'))
                <div class="hidden md:block absolute -left-2 top-1/2 -translate-y-1/2 w-1 h-6 bg-primary rounded-r-full shadow-[0_0_10px_#ff8a00]"></div>
                @endif
            </a>
            
            <!-- Risk -->
            <a href="{{ route('driver.risk') }}" class="relative group flex flex-col items-center gap-1 transition-all" title="Risk">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center transition-all duration-300 {{ request()->routeIs('driver.risk') ? 'bg-primary text-black shadow-lg shadow-orange-500/20' : 'text-zinc-500 hover:text-white hover:bg-white/5' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <span class="text-[9px] font-black uppercase tracking-widest {{ request()->routeIs('driver.risk') ? 'text-primary' : 'text-zinc-600 group-hover:text-zinc-400' }}">Risk</span>
                @if(request()->routeIs('driver.risk'))
                <div class="hidden md:block absolute -left-2 top-1/2 -translate-y-1/2 w-1 h-6 bg-primary rounded-r-full shadow-[0_0_10px_#ff8a00]"></div>
                @endif
            </a>
        </nav>

        <!-- Profile / Logout -->
        <div class="mt-auto" x-data="{ open: false }">
            <div class="relative">
                <!-- Profile -->
                <a href="{{ route('driver.profile') }}" class="relative group flex flex-col items-center gap-1 transition-all mb-4" title="Profile">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center transition-all duration-300 {{ request()->routeIs('driver.profile') ? 'bg-primary text-black shadow-lg shadow-orange-500/20' : 'text-zinc-500 hover:text-white hover:bg-white/5' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    @if(request()->routeIs('driver.profile'))
                    <div class="hidden md:block absolute -left-2 top-1/2 -translate-y-1/2 w-1 h-6 bg-primary rounded-r-full shadow-[0_0_10px_#ff8a00]"></div>
                    @endif
                </a>

                <button @click="open = !open" class="w-10 h-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center hover:border-orange-500/50 transition-colors">
                    <span class="text-xs font-bold">{{ substr(Auth::user()->name ?? 'D', 0, 1) }}</span>
                </button>
                <div x-show="open" style="display: none;" @click.away="open = false" class="absolute bottom-0 left-14 w-48 glass-obsidian rounded-xl p-2 shadow-2xl border border-white/10" x-transition>
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
        <header class="h-20 glass-obsidian border-b border-white/5 flex items-center justify-between px-8 shrink-0 z-20">
            <div>
                <div class="text-[10px] text-primary font-black uppercase tracking-[0.2em] mb-1">Driver Intelligence</div>
                <h1 class="text-2xl font-extrabold text-white tracking-tight font-heading">Co-Pilot</h1>
            </div>
            
            <div class="flex items-center gap-4">
                @php
                    $logStatus = \App\Models\DutyLog::where('driver_id', auth()->id())->whereNull('ended_at')->first()?->status;
                    $statusColor = $logStatus === 'on_duty' ? 'bg-[#10b981] text-[#10b981]' : ($logStatus === 'break' ? 'bg-orange-500 text-orange-500' : 'bg-zinc-500 text-zinc-500');
                    $statusText = str_replace('_', ' ', $logStatus ?? 'off_duty');
                @endphp
                <div class="px-3 py-1.5 rounded-full bg-white/5 border border-white/10 flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full {{ $statusColor }} {{ $logStatus === 'on_duty' ? 'shadow-[0_0_8px_rgba(16,185,129,0.5)] animate-pulse' : '' }}"></div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-white/80">{{ $statusText }}</span>
                </div>
                
                <div class="w-10 h-10 rounded-full bg-white/10 border border-white/20 flex items-center justify-center text-sm font-bold">
                    {{ substr(Auth::user()->name ?? 'D', 0, 1) }}
                </div>
            </div>
        </header>

        <!-- Scrollable Dashboard -->
        <div class="flex-1 overflow-y-auto p-8 custom-scrollbar bg-[#020202]">
            @yield('content')
        </div>
    </main>

    @stack('scripts')
</body>
</html>
