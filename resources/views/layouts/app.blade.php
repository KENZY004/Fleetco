<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'FleetCo') }} | Operations Hub</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .font-heading { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="antialiased min-h-screen flex flex-col bg-[#020202] text-white">

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
                <a href="{{ route('trips.index') }}" class="text-sm font-medium {{ request()->routeIs('trips.*') ? 'text-white' : 'text-zinc-500 hover:text-white' }} transition-colors">Trips</a>
                <a href="{{ route('alerts.index') }}" class="text-sm font-medium {{ request()->routeIs('alerts.*') ? 'text-white' : 'text-zinc-500 hover:text-white' }} transition-colors">Alerts</a>
                <a href="{{ route('geofences.index') }}" class="text-sm font-medium {{ request()->routeIs('geofences.*') ? 'text-white' : 'text-zinc-500 hover:text-white' }} transition-colors">Geofences</a>
                <a href="{{ route('settings.index') }}" class="text-sm font-medium {{ request()->routeIs('settings.*') ? 'text-white' : 'text-zinc-500 hover:text-white' }} transition-colors">Settings</a>
                <a href="{{ route('track-me') }}" class="text-sm font-medium {{ request()->routeIs('track-me') ? 'text-white' : 'text-zinc-500 hover:text-white' }} transition-colors">Mobile Link</a>
            </div>
        </div>

        <div class="flex items-center gap-6" x-data="{ open: false }">
            @auth
            <div class="text-right hidden sm:block">
                <div class="text-[9px] font-black uppercase tracking-[0.3em] text-zinc-500">Authorized_User</div>
                <div class="text-[11px] font-bold uppercase tracking-widest">{{ Auth::user()->name }}</div>
            </div>
            
            <div class="relative">
                <button @click="open = !open" class="w-10 h-10 rounded-full border border-white/10 flex items-center justify-center hover:border-orange-500/50 transition-colors">
                    <svg class="w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </button>
                
                <div x-show="open" @click.away="open = false" class="absolute right-0 mt-4 w-48 glass-obsidian rounded-2xl p-2 shadow-2xl overflow-hidden border border-white/10" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95">
                    <a href="{{ route('profile.edit') }}" class="block px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-zinc-400 hover:bg-white/5 hover:text-white rounded-xl transition-all">Profile_Settings</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-red-500 hover:bg-red-500/10 rounded-xl transition-all">Terminate_Session</button>
                    </form>
                </div>
            </div>
            @endauth
        </div>
    </nav>

    <!-- Main Content Matrix -->
    <main class="flex-1 p-8 overflow-y-auto custom-scrollbar">
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
