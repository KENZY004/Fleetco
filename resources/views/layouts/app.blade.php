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
<body class="antialiased h-screen flex bg-[#020202] text-white overflow-hidden">

    <!-- Slim Operations Sidebar (Desktop) -->
    <aside class="hidden md:flex w-24 flex-shrink-0 glass-obsidian border-r border-white/5 flex-col items-center py-4 z-[3000]">
        <!-- Brand -->
        <a href="{{ route('dashboard') }}" class="mb-12 group">
            <div class="w-12 h-12 rounded-2xl bg-orange-500 flex items-center justify-center shadow-lg shadow-orange-500/20 transition-transform group-hover:scale-105">
                <svg class="w-7 h-7 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
        </a>

        <!-- Navigation Links -->
        <nav class="flex-1 flex flex-col gap-4 w-full items-center overflow-y-auto custom-scrollbar px-2 py-4">
            <x-nav-link-sidebar icon="dashboard" route="dashboard" label="HQ" />
            <x-nav-link-sidebar icon="driver" route="drivers.index" label="Ops" />
            <x-nav-link-sidebar icon="vehicle" route="vehicles.index" label="Fleet" />
            {{-- TODO: Teammate to add Routes nav item here: <x-nav-link-sidebar icon="route" route="fleet.routes.index" label="Routes" /> --}}
            <x-nav-link-sidebar icon="trips" route="trips.index" label="Trips" />
            <x-nav-link-sidebar icon="alert" route="alerts.index" label="Risk" />
            <x-nav-link-sidebar icon="geofence" route="geofences.index" label="Zones" />
            <x-nav-link-sidebar icon="mobile" route="track-me" label="Link" />
        </nav>

        <!-- Profile / Logout -->
        <div class="mt-auto flex flex-col gap-6 items-center" x-data="{ open: false }">
            <div class="relative">
                <button @click="open = !open" class="w-12 h-12 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center hover:border-orange-500/50 transition-colors">
                    <svg class="w-5 h-5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </button>
                <div x-show="open" @click.away="open = false" class="absolute bottom-0 left-16 w-48 glass-obsidian rounded-2xl p-2 shadow-2xl border border-white/10" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-x-4">
                    <div class="px-4 py-2 border-b border-white/5 mb-2">
                        <div class="text-[8px] font-black uppercase tracking-[0.3em] text-zinc-500">System_Admin</div>
                        <div class="text-[10px] font-bold text-white truncate">{{ Auth::user()->name }}</div>
                    </div>
                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-[10px] font-bold uppercase tracking-widest text-zinc-400 hover:bg-white/5 hover:text-white rounded-xl">Settings</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-[10px] font-bold uppercase tracking-widest text-red-500 hover:bg-red-500/10 rounded-xl">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </aside>

    <!-- Mobile Bottom Navigation -->
    <nav class="md:hidden fixed bottom-0 left-0 right-0 glass-obsidian border-t border-white/5 h-20 flex items-center justify-around px-4 z-[4000] pb-safe">
        <x-nav-link-sidebar icon="dashboard" route="dashboard" label="HQ" />
        <x-nav-link-sidebar icon="driver" route="drivers.index" label="Ops" />
        <x-nav-link-sidebar icon="vehicle" route="vehicles.index" label="Fleet" />
        <x-nav-link-sidebar icon="trips" route="trips.index" label="Trips" />
        <x-nav-link-sidebar icon="alert" route="alerts.index" label="Risk" />
    </nav>

    <!-- Content Matrix -->
    <main class="flex-1 relative min-w-0 @yield('main-class', 'p-4 md:p-8 pb-24 md:pb-8 overflow-y-auto bg-[#020202] custom-scrollbar')">
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
