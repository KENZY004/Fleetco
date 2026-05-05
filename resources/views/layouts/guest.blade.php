<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'FleetCo') }} | Security Terminal</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100;400;700;900&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">
    
    <!-- Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body { background-color: #020203; color: #ffffff; font-family: 'Outfit', sans-serif; }
        .glass-obsidian { background: rgba(5, 5, 8, 0.7); backdrop-filter: blur(25px); border: 1px solid rgba(255, 255, 255, 0.05); }
        .text-primary { color: #ff8a00; }
        .bg-primary { background-color: #ff8a00; }
    </style>
</head>
<body class="antialiased selection:bg-primary selection:text-black">
    <div class="min-h-screen flex flex-col items-center justify-center p-6 bg-obsidian-950">
        
        <!-- Logo/Header -->
        <div class="mb-12 flex flex-col items-center text-center">
            <div class="h-20 w-20 rounded-[2rem] bg-white/5 border border-white/10 flex items-center justify-center mb-6 shadow-[0_0_50px_rgba(255,138,0,0.1)]">
                <svg class="w-10 h-10 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 00-2 2zM7 11V7a5 5 0 0110 0v4" />
                </svg>
            </div>
            <span class="text-[14px] font-black tracking-[0.5em] text-white uppercase italic">FleetCo Security</span>
            <span class="text-[8px] font-mono font-bold text-primary uppercase tracking-[0.5em] mt-2">Identity Verification Required</span>
        </div>

        <!-- Auth Card -->
        <div class="w-full sm:max-w-md p-10 glass-obsidian rounded-[2.5rem] border border-white/5 shadow-2xl relative overflow-hidden group">
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-primary/50 to-transparent opacity-50"></div>
            {{ $slot }}
        </div>

        <div class="mt-12 text-[10px] font-mono text-zinc-600 uppercase tracking-[0.4em]">
            &copy; {{ date('Y') }} FleetCo Predictive Systems
        </div>
    </div>
</body>
</html>
