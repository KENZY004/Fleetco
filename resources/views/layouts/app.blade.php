<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FleetCo | Operations Command</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100;400;700;900&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

    <!-- Styles -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    },
                    colors: {
                        primary: '#ff8a00',
                        obsidian: {
                            950: '#020203',
                            900: '#09090b',
                            800: '#18181b',
                        },
                        border: 'rgba(255, 255, 255, 0.05)',
                    }
                }
            }
        }
    </script>
    
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
    <div class="flex flex-col h-screen w-full bg-obsidian-950 px-8 py-6">
        
        <!-- FleetCo Command Header -->
        <header class="flex items-center justify-between px-10 py-6 mb-8 rounded-[2rem] border border-white/5 bg-obsidian-900/50 backdrop-blur-3xl z-[2000]">
            <div class="flex items-center gap-10">
                <div class="flex flex-col">
                    <span class="text-[14px] font-black tracking-[0.5em] text-white uppercase italic">FleetCo</span>
                    <span class="text-[8px] font-mono font-bold text-primary uppercase tracking-[0.5em] mt-1">Sovereign Predictive Intelligence</span>
                </div>
                <div class="h-8 w-[1px] bg-white/5"></div>
                <nav class="flex gap-10">
                    <a href="{{ route('dashboard') }}" class="text-[10px] font-black uppercase tracking-[0.4em] text-white">Operations Command Hub</a>
                    <a href="#" class="text-[10px] font-black uppercase tracking-[0.4em] text-zinc-600 hover:text-white transition-colors">Neural Intelligence</a>
                </nav>
            </div>
            
            <div class="flex items-center gap-8">
                <div class="flex items-center gap-3 px-5 py-2.5 bg-emerald-500/5 border border-emerald-500/10 rounded-full">
                    <div class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse shadow-[0_0_8px_#10b981]"></div>
                    <span class="text-[9px] font-black text-emerald-500 uppercase tracking-[0.2em]">Signal Stable</span>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center group hover:bg-primary transition-all duration-500 cursor-pointer">
                    <svg class="h-5 w-5 text-zinc-500 group-hover:text-black transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
            </div>
        </header>

        <!-- Main Mission Scape -->
        <main class="flex-1 overflow-auto custom-scrollbar rounded-[2.5rem] relative">
            @yield('content')
        </main>
    </div>

    <!-- Scripts -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script defer src="https://unpkg.com/alpinejs@3.13.3/dist/cdn.min.js"></script>
    @stack('scripts')
</body>
</html>
