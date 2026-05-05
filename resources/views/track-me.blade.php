<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Fleetco | Mobile Uplink</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap');
        
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #09090b;
            color: #fafafa;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            width: 100vw;
            overflow: hidden;
            touch-action: manipulation;
        }

        .glass-card {
            background: rgba(24, 24, 27, 0.8);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 32px;
            padding: 40px 32px;
            width: 85%;
            max-width: 380px;
            text-align: center;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .status-badge {
            background: rgba(255, 255, 255, 0.05);
            padding: 10px 20px;
            border-radius: 99px;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 0.05em;
            display: inline-flex;
            align-items: center;
            margin-bottom: 32px;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .pulse {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 10px;
            transition: background 0.3s;
        }

        .pulse-active {
            background: #22c55e !important;
            box-shadow: 0 0 15px rgba(34, 197, 94, 0.5);
            animation: pulse-green 2s infinite;
        }

        @keyframes pulse-green {
            0% { transform: scale(0.9); opacity: 0.8; }
            50% { transform: scale(1.1); opacity: 1; }
            100% { transform: scale(0.9); opacity: 0.8; }
        }

        h1 {
            margin: 0 0 12px 0;
            font-size: 32px;
            font-weight: 700;
            letter-spacing: -0.02em;
        }

        p {
            color: #a1a1aa;
            margin: 0 0 40px 0;
            font-size: 16px;
            line-height: 1.5;
        }

        input {
            width: 100%;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 20px;
            color: white;
            font-size: 18px;
            margin-bottom: 24px;
            box-sizing: border-box;
            outline: none;
            transition: all 0.3s;
            text-align: center;
            font-family: inherit;
        }

        input:focus {
            border-color: #3b82f6;
            background: rgba(255, 255, 255, 0.06);
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.1);
        }

        .btn-action {
            width: 100%;
            padding: 20px;
            border-radius: 16px;
            font-size: 18px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            outline: none;
        }

        .btn-inactive {
            background: #fafafa;
            color: #09090b;
        }

        .btn-inactive:active {
            transform: scale(0.98);
            background: #e4e4e7;
        }

        .btn-active {
            background: #ef4444;
            color: white;
            box-shadow: 0 10px 20px rgba(239, 68, 68, 0.2);
        }

        .footer-text {
            margin-top: 32px;
            font-size: 12px;
            color: #52525b;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            font-weight: 600;
        }

        #error-msg {
            color: #ef4444;
            font-size: 14px;
            margin-top: 16px;
            display: none;
        }
    </style>
</head>
<body>
    <div class="glass-card">
        <div class="status-badge">
            <span id="status-dot" class="pulse" style="background: #52525b;"></span>
            <span id="status-text">SYSTEM OFFLINE</span>
        </div>

        <h1>Mobile Uplink</h1>
        <p>Telemetry stream active via Neural Matrix.</p>

        <input type="text" id="uplink-token" placeholder="Uplink Token" autocomplete="off" spellcheck="false">
        
        <button id="toggle-btn" class="btn-action btn-inactive" onclick="toggleTracking()">
            Initialize Uplink
        </button>

        <div id="error-msg"></div>

        <div class="footer-text">
            PostGIS Encryption Enabled
        </div>
    </div>

    <script>
        let isTracking = false;
        let watchId = null;
        let lastPing = 0;
        let wakeLock = null;

        // Register Service Worker
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js').catch(console.error);
            });
        }

        async function toggleTracking() {
            const btn = document.getElementById('toggle-btn');
            const dot = document.getElementById('status-dot');
            const text = document.getElementById('status-text');
            const tokenInput = document.getElementById('uplink-token');
            const errorMsg = document.getElementById('error-msg');

            if (!isTracking) {
                if (!tokenInput.value) {
                    errorMsg.innerText = "Please enter your token";
                    errorMsg.style.display = "block";
                    return;
                }
                errorMsg.style.display = "none";

                if ("geolocation" in navigator) {
                    try {
                        if ('wakeLock' in navigator) {
                            wakeLock = await navigator.wakeLock.request('screen');
                        }
                    } catch (err) {
                        console.warn('Wake Lock failed:', err);
                    }

                    isTracking = true;
                    btn.innerText = "Terminate Uplink";
                    btn.classList.replace('btn-inactive', 'btn-active');
                    dot.classList.add('pulse-active');
                    text.innerText = "STREAMING ACTIVE";
                    tokenInput.disabled = true;

                    watchId = navigator.geolocation.watchPosition(
                        (pos) => {
                            const now = Date.now();
                            if (now - lastPing > 3000) {
                                sendTelemetry(
                                    pos.coords.latitude, 
                                    pos.coords.longitude, 
                                    pos.coords.speed, 
                                    pos.coords.heading
                                );
                                lastPing = now;
                            }
                        },
                        (err) => {
                            console.error(err);
                            stopTracking();
                            errorMsg.innerText = "GPS Error: " + err.message;
                            errorMsg.style.display = "block";
                        },
                        { enableHighAccuracy: true, maximumAge: 0 }
                    );
                }
            } else {
                stopTracking();
            }
        }

        function stopTracking() {
            isTracking = false;
            if (watchId) navigator.geolocation.clearWatch(watchId);
            
            if (wakeLock) {
                wakeLock.release().then(() => { wakeLock = null; });
            }
            
            const btn = document.getElementById('toggle-btn');
            const dot = document.getElementById('status-dot');
            const text = document.getElementById('status-text');
            const tokenInput = document.getElementById('uplink-token');

            btn.innerText = "Initialize Uplink";
            btn.classList.replace('btn-active', 'btn-inactive');
            dot.classList.remove('pulse-active');
            text.innerText = "SYSTEM OFFLINE";
            tokenInput.disabled = false;
        }

        async function sendTelemetry(lat, lng, speed, heading) {
            const token = document.getElementById('uplink-token').value;
            
            const payload = {
                token: token,
                lat: lat,
                lng: lng,
                speed: speed || 0,
                heading: heading || 0
            };

            try {
                const response = await fetch('/api/telematics', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });

                if (!response.ok && navigator.serviceWorker.controller) {
                    throw new Error('Offline');
                }
            } catch (error) {
                if (navigator.serviceWorker.controller) {
                    navigator.serviceWorker.controller.postMessage({
                        type: 'QUEUE_TELEMETRY',
                        payload: payload
                    });
                }
            }
        }
    </script>
</body>
</html>
