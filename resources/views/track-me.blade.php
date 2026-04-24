<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fleetco | Mobile Tracking</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #020202; color: white; }
        .font-heading { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(24px); border: 1px solid rgba(255, 255, 255, 0.08); }
        .glow-primary { box-shadow: 0 0 40px rgba(255, 138, 0, 0.15); }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-6 overflow-hidden">
    <!-- Background -->
    <div class="fixed inset-0 z-0 pointer-events-none">
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-orange-500/5 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-blue-500/5 rounded-full blur-[120px]"></div>
    </div>

    <div class="w-full max-w-md relative z-10">
        <div class="glass rounded-[2.5rem] p-10 text-center relative overflow-hidden">
            <!-- Header -->
            <div class="mb-10">
                <div class="text-[10px] font-bold tracking-widest text-orange-500 uppercase mb-4">Tracking Service</div>
                <h1 class="font-heading text-3xl font-bold tracking-tight mb-2">Mobile Uplink</h1>
                <p class="text-zinc-500 text-sm font-medium">Transmitting live telemetry to dashboard</p>
            </div>

            <!-- Status Ring -->
            <div class="relative w-48 h-48 mx-auto mb-10">
                <div class="absolute inset-0 rounded-full border-2 border-white/5"></div>
                <div id="ring-active" class="absolute inset-2 rounded-full border border-orange-500/20 scale-90 opacity-0 transition-all duration-700"></div>
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <div id="status-dot" class="w-4 h-4 rounded-full bg-zinc-800 transition-all duration-500"></div>
                    <div id="status-text" class="text-xs font-bold uppercase tracking-widest mt-4 text-zinc-600">Standby</div>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-2 gap-4 mb-10">
                <div class="bg-white/[0.03] border border-white/5 rounded-2xl p-4">
                    <div class="text-[9px] text-zinc-500 uppercase font-bold tracking-wider mb-1 text-left">Latitude</div>
                    <div id="lat" class="font-medium text-sm text-left text-white">--.----</div>
                </div>
                <div class="bg-white/[0.03] border border-white/5 rounded-2xl p-4">
                    <div class="text-[9px] text-zinc-500 uppercase font-bold tracking-wider mb-1 text-left">Longitude</div>
                    <div id="lng" class="font-medium text-sm text-left text-white">--.----</div>
                </div>
            </div>

            <!-- Controls -->
            <div class="space-y-6">
                <button 
                    id="toggle-btn"
                    onclick="toggleTracking()"
                    class="w-full py-5 bg-white text-black rounded-2xl font-bold text-sm transition-all active:scale-95 shadow-xl shadow-white/5"
                >
                    Initialize Uplink
                </button>

                <div class="grid grid-cols-2 gap-4">
                    <div class="relative">
                        <label class="absolute -top-2.5 left-4 bg-[#020202] px-2 text-[9px] font-bold text-zinc-500 uppercase tracking-widest">Vehicle Name</label>
                        <input 
                            type="text" 
                            id="vehicle-name" 
                            placeholder="e.g. Truck 01"
                            class="w-full bg-transparent border border-white/10 rounded-xl py-4 px-4 text-center text-xs font-bold tracking-wider focus:border-orange-500 outline-none text-white transition-colors"
                        >
                    </div>
                    <div class="relative">
                        <label class="absolute -top-2.5 left-4 bg-[#020202] px-2 text-[9px] font-bold text-zinc-500 uppercase tracking-widest">Plate/ID</label>
                        <input 
                            type="text" 
                            id="license-plate" 
                            placeholder="Unit ID (e.g. 01)"
                            class="w-full bg-transparent border border-white/10 rounded-xl py-4 px-4 text-center text-xs font-bold tracking-wider focus:border-orange-500 outline-none uppercase text-white transition-colors"
                            value=""
                        >
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let isTracking = false;
        let watchId = null;
        let lastPing = 0;
        const secret = 'fleetco_secret_2024';

        function toggleTracking() {
            const btn = document.getElementById('toggle-btn');
            const dot = document.getElementById('status-dot');
            const text = document.getElementById('status-text');
            const ring = document.getElementById('ring-active');

            if (!isTracking) {
                if ("geolocation" in navigator) {
                    isTracking = true;
                    btn.innerHTML = "Terminate Uplink";
                    btn.classList.replace('bg-white', 'bg-zinc-900');
                    btn.classList.add('text-white', 'border', 'border-white/10');
                    
                    dot.classList.replace('bg-zinc-800', 'bg-orange-500');
                    dot.classList.add('animate-pulse', 'glow-primary');
                    
                    ring.classList.replace('opacity-0', 'opacity-100');
                    ring.classList.add('scale-100', 'animate-pulse');

                    text.innerHTML = "Transmitting";
                    text.classList.replace('text-zinc-600', 'text-white');

                    watchId = navigator.geolocation.watchPosition(
                        (position) => {
                            const { latitude, longitude, speed, heading } = position.coords;
                            document.getElementById('lat').innerText = latitude.toFixed(6);
                            document.getElementById('lng').innerText = longitude.toFixed(6);

                            const now = Date.now();
                            if (now - lastPing > 3000) {
                                sendTelemetry(latitude, longitude, speed, heading);
                                lastPing = now;
                            }
                        },
                        (error) => {
                            console.error(error);
                            alert("GPS Error: " + error.message);
                            stopTracking();
                        },
                        { enableHighAccuracy: true }
                    );
                } else {
                    alert("Geolocation not supported.");
                }
            } else {
                stopTracking();
            }
        }

        function stopTracking() {
            isTracking = false;
            if (watchId) navigator.geolocation.clearWatch(watchId);
            
            const btn = document.getElementById('toggle-btn');
            const dot = document.getElementById('status-dot');
            const text = document.getElementById('status-text');
            const ring = document.getElementById('ring-active');

            btn.innerHTML = "Initialize Uplink";
            btn.classList.replace('bg-zinc-900', 'bg-white');
            btn.classList.remove('text-white', 'border', 'border-white/10');
            
            dot.classList.replace('bg-orange-500', 'bg-zinc-800');
            dot.classList.remove('animate-pulse', 'glow-primary');
            
            ring.classList.replace('opacity-100', 'opacity-0');
            ring.classList.remove('scale-100', 'animate-pulse');

            text.innerHTML = "Standby";
            text.classList.replace('text-white', 'text-zinc-600');
        }

        async function sendTelemetry(lat, lng, speed, heading) {
            const plate = document.getElementById('license-plate').value;
            const name = document.getElementById('vehicle-name').value;

            if (!plate) return; // Don't send if no ID provided
            
            try {
                const response = await fetch('/api/telematics', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        license_plate: plate,
                        vehicle_name: name,
                        latitude: lat,
                        longitude: lng,
                        speed: speed || 0,
                        heading: heading || 0,
                        secret: secret
                    })
                });
            } catch (error) {
                console.error('Network Error:', error);
            }
        }
    </script>
</body>
</html>
