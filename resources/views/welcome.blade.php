<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FleetCo | Predictive Mobility Intelligence</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100;400;700;900&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body { background-color: #020203; color: #ffffff; font-family: 'Outfit', sans-serif; }
        .text-primary { color: #ff8a00; }
        .bg-primary { background-color: #ff8a00; }
        .hero-gradient { background: radial-gradient(circle at 50% 50%, rgba(255, 138, 0, 0.05) 0%, transparent 70%); }
    </style>
</head>
<body class="antialiased overflow-hidden selection:bg-primary selection:text-black">
    <div class="relative min-h-screen flex flex-col items-center justify-center overflow-hidden">
        
        <!-- Background Accents -->
        <div class="absolute inset-0 hero-gradient"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] border border-white/5 rounded-full opacity-20"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] border border-white/5 rounded-full opacity-30"></div>

        <!-- Content -->
        <div class="relative z-10 flex flex-col items-center text-center px-6">
            <div class="mb-12">
                <div class="flex flex-col items-center">
                    <span class="text-[24px] font-black tracking-[1em] text-white uppercase italic ml-4">FleetCo</span>
                    <span class="text-[10px] font-mono font-bold text-primary uppercase tracking-[0.6em] mt-4">Sovereign Predictive Intelligence</span>
                </div>
            </div>

            <h1 class="text-5xl md:text-7xl font-black text-white tracking-tighter mb-8 leading-tight">
                THE FUTURE OF <br> 
                <span class="text-primary italic">AUTONOMOUS FLOW.</span>
            </h1>

            <p class="max-w-xl text-zinc-500 text-lg mb-12 font-medium leading-relaxed uppercase tracking-widest text-[11px]">
                Real-time spatial telemetry, predictive risk heuristics, and neural fleet synchronization. The next generation of mobility starts here.
            </p>

            <div class="flex flex-col sm:flex-row gap-6 w-full max-w-sm">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="flex-1 py-5 bg-white text-black rounded-full text-[10px] font-black uppercase tracking-[0.3em] hover:bg-white/90 transition-all text-center">Enter Command Hub</a>
                    @else
                        <a href="{{ route('login') }}" class="flex-1 py-5 bg-white text-black rounded-full text-[10px] font-black uppercase tracking-[0.3em] hover:bg-white/90 transition-all text-center">Access Terminal</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="flex-1 py-5 border border-white/10 text-white rounded-full text-[10px] font-black uppercase tracking-[0.3em] hover:bg-white/5 transition-all text-center">Initialize Identity</a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>

        <!-- Footnote -->
        <div class="absolute bottom-12 left-12 flex items-center gap-4">
            <div class="h-1.5 w-1.5 rounded-full bg-primary animate-pulse"></div>
            <span class="text-[9px] font-mono text-zinc-700 uppercase tracking-[0.5em]">System Status: Operational</span>
        </div>
    </div>
</body>
</html>
