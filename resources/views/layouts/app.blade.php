<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'FleetCo') }} | Operations Command</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🚛</text></svg>">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100;400;700;900&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">
    
    <!-- Leaflet & Draw CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css"/>

    <!-- Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body { background-color: #020203; color: #ffffff; font-family: 'Outfit', sans-serif; }
        .glass-obsidian { background: rgba(5, 5, 8, 0.7); backdrop-filter: blur(25px); border: 1px solid rgba(255, 255, 255, 0.05); }
        .fleetco-card { background: #09090b; border: 1px solid rgba(255, 255, 255, 0.05); transition: all 0.5s ease; }
        .fleetco-card:hover { border-color: rgba(255, 138, 0, 0.3); box-shadow: 0 0 40px rgba(255, 138, 0, 0.05); }
        .text-primary { color: #ff8a00; }
        .bg-primary { background-color: #ff8a00; }
        .custom-scrollbar::-webkit-scrollbar { width: 2px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.1); }
        @keyframes pulse-fleetco {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(255, 138, 0, 0.3); }
            70% { transform: scale(1); box-shadow: 0 0 0 15px rgba(255, 138, 0, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(255, 138, 0, 0); }
        }
        .animate-fleetco-pulse { animation: pulse-fleetco 3s infinite; }
        .leaflet-container { background: #020203 !important; }
    </style>
    @stack('styles')
</head>
<body class="antialiased overflow-hidden selection:bg-primary selection:text-black">
    <div class="flex h-screen w-full bg-obsidian-950" x-data="{ sidebarOpen: true }">
        
        <!-- Sidebar -->
        <aside class="flex flex-col border-r border-white/5 bg-obsidian-900/50 backdrop-blur-3xl transition-all duration-300 z-[2000]" :class="sidebarOpen ? 'w-64' : 'w-20'">
            <!-- Logo / Toggle -->
            <div class="flex items-center justify-between p-6 border-b border-white/5">
                <a href="{{ route('dashboard') }}" class="flex flex-col group overflow-hidden" x-show="sidebarOpen">
                    <span class="text-[12px] font-black tracking-[0.5em] text-white uppercase italic group-hover:text-primary transition-colors">FleetCo</span>
                    <span class="text-[7px] font-mono font-bold text-zinc-500 uppercase tracking-[0.5em] mt-0.5">Neural Hub</span>
                </a>
                <button @click="sidebarOpen = !sidebarOpen" class="text-zinc-500 hover:text-white transition-colors" :class="sidebarOpen ? '' : 'mx-auto'">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 overflow-y-auto custom-scrollbar p-4 space-y-2">
                <div class="mb-4">
                    <span class="px-2 text-[8px] font-bold text-zinc-600 uppercase tracking-widest" x-show="sidebarOpen">Command</span>
                </div>
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('dashboard') ? 'bg-primary/10 text-primary border border-primary/20' : 'text-zinc-400 hover:bg-white/5 hover:text-white' }} transition-colors" title="Fleet Map">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                    <span class="text-[10px] font-bold uppercase tracking-widest whitespace-nowrap" x-show="sidebarOpen">Fleet Map</span>
                </a>
                
                <div class="mt-6 mb-4 pt-4 border-t border-white/5">
                    <span class="px-2 text-[8px] font-bold text-zinc-600 uppercase tracking-widest" x-show="sidebarOpen">Assets</span>
                </div>
                <!-- Vehicles Link -->
                <a href="/vehicles" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->is('vehicles') ? 'bg-primary/10 text-primary border border-primary/20' : 'text-zinc-400 hover:bg-white/5 hover:text-white' }} transition-colors" title="Vehicles">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                    <span class="text-[10px] font-bold uppercase tracking-widest whitespace-nowrap" x-show="sidebarOpen">Vehicles</span>
                </a>
                <!-- Issues Link -->
                <a href="/issues" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->is('issues') ? 'bg-primary/10 text-primary border border-primary/20' : 'text-zinc-400 hover:bg-white/5 hover:text-white' }} transition-colors" title="Issues">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span class="text-[10px] font-bold uppercase tracking-widest whitespace-nowrap" x-show="sidebarOpen">Issues</span>
                </a>
                
                @if(auth()->check() && auth()->user()->is_admin)
                <a href="{{ route('vehicles.create') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('vehicles.create') ? 'bg-primary/10 text-primary border border-primary/20' : 'text-zinc-400 hover:bg-white/5 hover:text-white' }} transition-colors" title="Add Unit">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"/></svg>
                    <span class="text-[10px] font-bold uppercase tracking-widest whitespace-nowrap" x-show="sidebarOpen">Add Unit</span>
                </a>
                @endif
            </nav>

            <!-- User Area -->
            <div class="p-4 border-t border-white/5">
                @auth
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-primary/20 border border-primary/50 flex items-center justify-center shrink-0">
                        <span class="text-primary text-[10px] font-bold">{{ substr(auth()->user()->name, 0, 1) }}</span>
                    </div>
                    <div class="flex flex-col overflow-hidden" x-show="sidebarOpen">
                        <span class="text-[10px] font-bold text-white truncate">{{ auth()->user()->name }}</span>
                        <span class="text-[8px] text-zinc-500 uppercase">Operator</span>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="mt-4" x-show="sidebarOpen">
                    @csrf
                    <button type="submit" class="w-full py-2 bg-white/5 hover:bg-white/10 text-zinc-400 hover:text-white rounded text-[9px] font-black uppercase tracking-[0.3em] transition-colors">Terminate</button>
                </form>
                @endauth
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex flex-col flex-1 min-w-0 h-screen overflow-hidden">
            
            <!-- Top Header -->
            <header class="h-16 border-b border-white/5 bg-obsidian-900/50 backdrop-blur-3xl flex items-center justify-between px-8 z-[1000] shrink-0">
                
                <!-- Search Box -->
                <div class="flex-1 max-w-xl relative hidden md:block">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" class="block w-full pl-10 pr-3 py-2 border border-white/5 rounded-lg leading-5 bg-white/5 text-zinc-300 placeholder-zinc-500 focus:outline-none focus:bg-white/10 focus:border-white/20 sm:text-[10px] font-mono transition-all" placeholder="Search Command Center...">
                </div>

                <!-- Header Actions -->
                <div class="flex items-center gap-6 ml-auto">
                    @auth
                    <div class="flex items-center gap-2.5 px-3 py-1.5 bg-emerald-500/5 border border-emerald-500/10 rounded-full">
                        <div class="h-1 w-1 rounded-full bg-emerald-500 animate-pulse"></div>
                        <span class="text-[8px] font-black text-emerald-500 uppercase tracking-widest hidden sm:inline-block">System Nominal</span>
                    </div>
                    
                    <div class="h-9 w-9 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center group hover:border-primary/30 transition-all duration-500 cursor-pointer relative">
                        <div class="absolute -top-1 -right-1 h-3 w-3 bg-primary rounded-full border-2 border-obsidian-950 flex items-center justify-center"></div>
                        <svg class="h-4 w-4 text-zinc-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    </div>
                    @endauth
                </div>
            </header>

            <!-- Main Mission Scape -->
            <main class="flex-1 overflow-auto custom-scrollbar p-6 relative bg-obsidian-950">
                {{ $slot ?? '' }}
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>
    @stack('scripts')
</body>
</html>
