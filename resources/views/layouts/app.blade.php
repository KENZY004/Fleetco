<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>FleetCo | Operations Hub</title>
    
    <!-- Scripts & Styles -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <style>
        body { background-color: #020202; font-family: 'Inter', sans-serif; color: white; }
        .font-heading { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass-obsidian { background: rgba(10, 10, 15, 0.8); backdrop-filter: blur(24px); border: 1px solid rgba(255, 255, 255, 0.08); }
        .fleetco-card { background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.06); transition: all 0.3s ease; }
        .fleetco-card:hover { border-color: rgba(255, 138, 0, 0.3); background: rgba(255, 255, 255, 0.04); }
        .text-primary { color: #ff8a00; }
        .bg-primary { background-color: #ff8a00; }
        
        /* Custom Scrollbar */
        .custom-scrollbar::-webkit-scrollbar { width: 5px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.1); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(255, 138, 0, 0.3); }
    </style>
</head>
<body class="antialiased min-h-screen flex flex-col">

    <!-- Top Navigation Matrix -->
    <nav class="glass-obsidian sticky top-0 z-[2000] px-8 py-4 border-b border-white/5 flex justify-between items-center">
        <div class="flex items-center gap-8">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                <div class="w-8 h-8 rounded-xl bg-orange-500 flex items-center justify-center shadow-lg shadow-orange-500/20 transition-transform group-hover:scale-105">
                    <svg class="w-5 h-5 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <span class="font-heading text-lg font-bold tracking-tight text-white">Fleetco</span>
            </a>
            
            <div class="h-6 w-[1px] bg-white/10 mx-2"></div>
            
            <div class="hidden md:flex gap-8">
                <a href="{{ route('dashboard') }}" class="text-sm font-medium {{ request()->routeIs('dashboard') ? 'text-white' : 'text-zinc-500 hover:text-white' }} transition-colors">Dashboard</a>
                <a href="{{ route('drivers.index') }}" class="text-sm font-medium {{ request()->routeIs('drivers.*') ? 'text-white' : 'text-zinc-500 hover:text-white' }} transition-colors">Drivers</a>
                <a href="{{ route('vehicles.index') }}" class="text-sm font-medium {{ request()->routeIs('vehicles.*') ? 'text-white' : 'text-zinc-500 hover:text-white' }} transition-colors">Vehicles</a>
                <a href="{{ route('geofences.index') }}" class="text-sm font-medium {{ request()->routeIs('geofences.*') ? 'text-white' : 'text-zinc-500 hover:text-white' }} transition-colors">Geofences</a>
                <a href="/track-me" class="text-sm font-medium {{ request()->url() == url('/track-me') ? 'text-white' : 'text-zinc-500 hover:text-white' }} transition-colors">Mobile Link</a>
            </div>
        </div>

        <div class="flex items-center gap-6" x-data="{ open: false }">
            <div class="text-right hidden sm:block">
                <div class="text-[9px] font-black uppercase tracking-[0.3em] text-zinc-500">Authorized_User</div>
                <div class="text-[11px] font-bold uppercase tracking-widest">{{ Auth::user()->name }}</div>
            </div>
            
            <div class="relative">
                <button @click="open = !open" class="w-10 h-10 rounded-full border border-white/10 flex items-center justify-center hover:border-primary/50 transition-colors">
                    <svg class="w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </button>
                
                <div x-show="open" @click.away="open = false" class="absolute right-0 mt-4 w-48 glass-obsidian rounded-2xl p-2 shadow-2xl overflow-hidden border border-white/10">
                    <a href="{{ route('profile.edit') }}" class="block px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-zinc-400 hover:bg-white/5 hover:text-white rounded-xl transition-all">Profile_Settings</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-red-500 hover:bg-red-500/10 rounded-xl transition-all">Terminate_Session</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content Matrix -->
    <main class="flex-1 p-8 overflow-y-auto">
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
