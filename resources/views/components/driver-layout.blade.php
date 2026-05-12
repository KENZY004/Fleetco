<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="telemetry-token" content="{{ $telemetryToken ?? '' }}">

    <title>{{ config('app.name', 'Fleetco Co-Pilot') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Plus+Jakarta+Sans:wght@700;800&display=swap" rel="stylesheet">

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body {
            background-color: #020202;
            color: #ffffff;
            font-family: 'Inter', sans-serif;
            -webkit-tap-highlight-color: transparent;
        }
        .font-heading {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .accent-orange {
            color: #ff8a00;
        }
        .bg-accent-orange {
            background-color: #ff8a00;
        }
        .border-accent-orange {
            border-color: #ff8a00;
        }
    </style>
</head>
<body class="antialiased select-none">
    <div class="min-h-screen pb-20">
        {{ $slot }}
    </div>

    <!-- Mobile Bottom Navigation (Optional) -->
    <nav class="fixed bottom-0 left-0 right-0 glass-card border-t border-white/10 px-6 py-4 flex justify-around items-center z-50">
        <a href="{{ route('driver.dashboard') }}" class="accent-orange">
            <i data-lucide="layout-dashboard" class="w-6 h-6"></i>
        </a>
        <a href="#" class="text-white/40">
            <i data-lucide="map-pin" class="w-6 h-6"></i>
        </a>
        <a href="{{ route('profile.edit') }}" class="text-white/40">
            <i data-lucide="user" class="w-6 h-6"></i>
        </a>
    </nav>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();
    </script>
    @stack('scripts')
</body>
</html>
