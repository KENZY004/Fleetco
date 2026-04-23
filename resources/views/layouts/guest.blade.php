<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>FleetCo | Access</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { background-color: #020202; font-family: 'Inter', sans-serif; color: white; }
        .font-heading { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass-obsidian { background: rgba(8, 8, 12, 0.8); backdrop-filter: blur(24px); border: 1px solid rgba(255, 255, 255, 0.08); }
    </style>
</head>
<body class="antialiased min-h-screen flex flex-col items-center justify-center p-6">
    <!-- Background Accents -->
    <div class="fixed inset-0 z-0 overflow-hidden pointer-events-none">
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-orange-500/5 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-blue-500/5 rounded-full blur-[120px]"></div>
    </div>

    <div class="w-full max-w-5xl relative z-10">
        <div class="glass-obsidian rounded-[2rem] overflow-hidden shadow-[0_0_100px_rgba(0,0,0,0.5)]">
            {{ $slot }}
        </div>

        <div class="mt-8 text-center">
            <a href="/" class="text-xs font-medium text-zinc-500 hover:text-white transition-colors">← Back to homepage</a>
        </div>
    </div>
</body>
</html>
