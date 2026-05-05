<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>FleetCo | Neural Link</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { background-color: #020203; color: #ffffff; font-family: 'Outfit', sans-serif; }
        .pulse { animation: pulse 2s infinite; }
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(255, 138, 0, 0.4); }
            70% { box-shadow: 0 0 0 20px rgba(255, 138, 0, 0); }
            100% { box-shadow: 0 0 0 0 rgba(255, 138, 0, 0); }
        }
    </style>
</head>
<body class="flex flex-col items-center justify-center min-h-screen p-6 text-center">
    
    <div id="status-container" class="space-y-8 w-full max-w-sm">
        <div class="h-24 w-24 rounded-[2rem] bg-white/5 border border-white/10 flex items-center justify-center mx-auto mb-8 shadow-[0_0_50px_rgba(255,138,0,0.1)]">
            <svg id="signal-icon" class="w-10 h-10 text-zinc-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0" />
            </svg>
        </div>

        <div>
            <h1 class="text-2xl font-black uppercase tracking-widest text-white italic">Neural Link</h1>
            <p id="status-text" class="text-[10px] font-mono text-zinc-500 uppercase tracking-[0.4em] mt-2">Awaiting Authorization</p>
        </div>

        <div id="stats" class="grid grid-cols-2 gap-4 opacity-20">
            <div class="p-4 rounded-2xl bg-white/5 border border-white/5">
                <div class="text-[8px] text-zinc-600 uppercase font-bold tracking-widest mb-1">LATITUDE</div>
                <div id="lat" class="text-xs font-mono text-white">00.0000</div>
            </div>
            <div class="p-4 rounded-2xl bg-white/5 border border-white/5">
                <div class="text-[8px] text-zinc-600 uppercase font-bold tracking-widest mb-1">LONGITUDE</div>
                <div id="lng" class="text-xs font-mono text-white">00.0000</div>
            </div>
        </div>

        <button id="start-btn" class="w-full py-5 bg-white text-black rounded-full text-[10px] font-black uppercase tracking-[0.3em] shadow-[0_10px_40px_rgba(255,255,255,0.1)] active:scale-95 transition-all">
            Initialize Uplink
        </button>

        <p class="text-[9px] text-zinc-700 uppercase tracking-widest leading-loose">
            Keep this window open to maintain<br>Sovereign Intelligence sync.
        </p>
    </div>

    <script>
        const vehicleId = "{{ $vehicle->id }}";
        const licensePlate = "{{ $vehicle->license_plate }}";
        const token = "{{ $token }}";
        let watchId = null;

        document.getElementById('start-btn').addEventListener('click', function() {
            if (!navigator.geolocation) {
                alert("This device does not support GPS Uplink.");
                return;
            }

            this.innerHTML = "Syncing...";
            this.classList.add('opacity-50');
            this.disabled = true;

            watchId = navigator.geolocation.watchPosition(
                sendPosition, 
                handleError, 
                { enableHighAccuracy: true }
            );
        });

        async function sendPosition(position) {
            const { latitude, longitude, speed, heading } = position.coords;

            // Update UI
            document.getElementById('lat').innerText = latitude.toFixed(4);
            document.getElementById('lng').innerText = longitude.toFixed(4);
            document.getElementById('status-text').innerText = "Signal Active";
            document.getElementById('status-text').classList.replace('text-zinc-500', 'text-primary');
            document.getElementById('signal-icon').classList.replace('text-zinc-700', 'text-primary');
            document.getElementById('stats').classList.remove('opacity-20');
            document.getElementById('signal-icon').parentElement.classList.add('pulse');

            try {
                const response = await fetch('/api/telematics', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        vehicle_id: vehicleId,
                        license_plate: licensePlate,
                        latitude: latitude,
                        longitude: longitude,
                        speed: (speed || 0) * 3.6, // m/s to km/h
                        heading: heading || 0,
                        fuel_level: 85,
                        engine_status: 'running'
                    })
                });
                
                const data = await response.json();
                console.log('Telemetry sent:', data);
            } catch (err) {
                console.error('Uplink failed:', err);
                document.getElementById('status-text').innerText = "Sync Error: Check Connection";
            }
        }

        function handleError(error) {
            console.error('GPS Error:', error);
            document.getElementById('status-text').innerText = "Link Lost: " + error.message;
        }
    </script>
</body>
</html>
