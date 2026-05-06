<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Fleetco | Mobile Uplink</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background: #09090b;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            touch-action: manipulation;
            margin: 0;
        }

        /* Ambient background glow */
        .ambient {
            position: fixed;
            inset: 0;
            pointer-events: none;
            background:
                radial-gradient(ellipse 60% 40% at 50% 100%, rgba(255, 138, 0, 0.06) 0%, transparent 70%),
                radial-gradient(ellipse 40% 30% at 80% 20%, rgba(255, 138, 0, 0.03) 0%, transparent 60%);
        }

        .uplink-card {
            position: relative;
            width: 90%;
            max-width: 400px;
            background: rgba(18, 18, 20, 0.85);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.07);
            border-radius: 36px;
            padding: 48px 36px 44px;
            box-shadow: 0 40px 80px rgba(0,0,0,0.6), 0 0 0 1px rgba(255,255,255,0.03) inset;
        }

        .brand-mark {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 40px;
        }

        .brand-dot {
            width: 28px;
            height: 28px;
            background: #ff8a00;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(255,138,0,0.4);
        }

        .brand-name {
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.35em;
            text-transform: uppercase;
            color: rgba(255,255,255,0.5);
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 18px;
            border-radius: 99px;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.07);
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            color: rgba(255,255,255,0.4);
            margin-bottom: 28px;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #3f3f46;
            transition: background 0.4s, box-shadow 0.4s;
            flex-shrink: 0;
        }

        .status-dot.active {
            background: #22c55e;
            box-shadow: 0 0 10px rgba(34,197,94,0.6);
            animation: pulse-dot 2s infinite;
        }

        @keyframes pulse-dot {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.3); opacity: 0.8; }
        }

        h1 {
            font-size: 36px;
            font-weight: 800;
            letter-spacing: -0.03em;
            color: #fff;
            margin: 0 0 10px;
            line-height: 1;
        }

        .subtitle {
            font-size: 13px;
            color: rgba(255,255,255,0.35);
            font-weight: 400;
            margin: 0 0 40px;
            line-height: 1.5;
        }

        .input-group {
            position: relative;
            margin-bottom: 20px;
        }

        .input-label {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            color: rgba(255,255,255,0.3);
            display: block;
            margin-bottom: 10px;
        }

        .token-input {
            width: 100%;
            box-sizing: border-box;
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 16px;
            padding: 18px 20px;
            color: #fff;
            font-size: 15px;
            font-family: 'Outfit', monospace;
            font-weight: 600;
            letter-spacing: 0.05em;
            outline: none;
            transition: border-color 0.3s, box-shadow 0.3s;
            text-align: center;
        }

        .token-input::placeholder { color: rgba(255,255,255,0.2); font-weight: 400; }

        .token-input:focus {
            border-color: rgba(255,138,0,0.4);
            box-shadow: 0 0 0 4px rgba(255,138,0,0.06);
        }

        .token-input:disabled {
            opacity: 0.4;
            cursor: not-allowed;
        }

        .btn-uplink {
            width: 100%;
            padding: 20px;
            border-radius: 16px;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            cursor: pointer;
            border: none;
            outline: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-family: 'Outfit', sans-serif;
            background: #fff;
            color: #09090b;
            box-shadow: 0 8px 30px rgba(255,255,255,0.08);
        }

        .btn-uplink:hover { background: rgba(255,255,255,0.92); transform: translateY(-1px); }
        .btn-uplink:active { transform: scale(0.98); }

        .btn-uplink.streaming {
            background: #ef4444;
            color: #fff;
            box-shadow: 0 8px 30px rgba(239,68,68,0.25);
        }

        .btn-uplink.streaming:hover { background: #dc2626; }

        #error-msg {
            font-size: 12px;
            color: #f87171;
            font-weight: 600;
            margin-top: 14px;
            text-align: center;
            min-height: 18px;
            display: none;
        }

        .divider {
            height: 1px;
            background: rgba(255,255,255,0.05);
            margin: 32px 0;
        }

        .meta-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .meta-label {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            color: rgba(255,255,255,0.2);
        }

        .meta-value {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: rgba(255,255,255,0.3);
        }

        .ping-counter {
            font-variant-numeric: tabular-nums;
            color: #ff8a00;
            transition: color 0.3s;
        }
    </style>
</head>
<body>
    <div class="ambient"></div>

    <div class="uplink-card">
        <!-- Brand -->
        <div class="brand-mark">
            <div class="brand-dot"></div>
            <span class="brand-name">Fleetco</span>
        </div>

        <!-- Status -->
        <div style="text-align:center; margin-bottom: 28px;">
            <span class="status-pill">
                <span id="status-dot" class="status-dot"></span>
                <span id="status-text">System Offline</span>
            </span>
        </div>

        <!-- Title -->
        <div style="text-align:center; margin-bottom: 40px;">
            <h1>Mobile Uplink</h1>
            <p class="subtitle">Enter your vehicle token to begin<br>streaming telemetry to fleet command.</p>
        </div>

        <!-- Input -->
        <div class="input-group">
            <label class="input-label" for="uplink-token">Vehicle Uplink Token</label>
            <input
                id="uplink-token"
                type="text"
                class="token-input"
                placeholder="FLT-XXXXXXXX"
                autocomplete="off"
                spellcheck="false"
                autocapitalize="characters"
            >
        </div>

        <!-- Action Button -->
        <button id="toggle-btn" class="btn-uplink" onclick="toggleTracking()">
            Initialize Uplink
        </button>

        <!-- Error -->
        <div id="error-msg"></div>

        <!-- Divider -->
        <div class="divider"></div>

        <!-- Meta -->
        <div class="meta-row">
            <span class="meta-label">Pings Sent</span>
            <span class="meta-value ping-counter" id="ping-count">0</span>
        </div>
        <div class="meta-row" style="margin-top: 10px;">
            <span class="meta-label">Encryption</span>
            <span class="meta-value">PostGIS · Active</span>
        </div>
    </div>

    <script>
        let isTracking = false;
        let watchId = null;
        let lastPing = 0;
        let wakeLock = null;
        let pingCount = 0;

        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js').catch(() => {});
            });
        }

        async function toggleTracking() {
            const btn = document.getElementById('toggle-btn');
            const dot = document.getElementById('status-dot');
            const text = document.getElementById('status-text');
            const tokenInput = document.getElementById('uplink-token');
            const errorMsg = document.getElementById('error-msg');

            if (!isTracking) {
                if (!tokenInput.value.trim()) {
                    errorMsg.innerText = "Please enter your vehicle uplink token.";
                    errorMsg.style.display = "block";
                    return;
                }
                errorMsg.style.display = "none";

                if (!("geolocation" in navigator)) {
                    errorMsg.innerText = "GPS is not available on this device.";
                    errorMsg.style.display = "block";
                    return;
                }

                try {
                    if ('wakeLock' in navigator) {
                        wakeLock = await navigator.wakeLock.request('screen');
                    }
                } catch (e) {}

                isTracking = true;
                btn.innerText = "Terminate Uplink";
                btn.classList.add('streaming');
                dot.classList.add('active');
                text.innerText = "Streaming Live";
                tokenInput.disabled = true;

                watchId = navigator.geolocation.watchPosition(
                    (pos) => {
                        const now = Date.now();
                        const accuracy = pos.coords.accuracy; // meters

                        // Only send if GPS accuracy is good (< 30m) and throttle to 1 ping per 4s
                        if (accuracy > 30) {
                            text.innerText = `Weak GPS (±${Math.round(accuracy)}m)`;
                            dot.style.background = '#f59e0b';
                            return; // Skip this reading — too inaccurate
                        }

                        // Good GPS signal
                        text.innerText = `Streaming Live (±${Math.round(accuracy)}m)`;
                        dot.style.background = '#22c55e';

                        if (now - lastPing > 4000) {
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
                        stopTracking();
                        errorMsg.innerText = "GPS Error: " + err.message;
                        errorMsg.style.display = "block";
                    },
                    { enableHighAccuracy: true, maximumAge: 0, timeout: 10000 }
                );
            } else {
                await stopTracking();
            }
        }

        async function stopTracking() {
            isTracking = false;
            if (watchId !== null) navigator.geolocation.clearWatch(watchId);
            if (wakeLock) { wakeLock.release().then(() => { wakeLock = null; }); }

            const btn = document.getElementById('toggle-btn');
            const dot = document.getElementById('status-dot');
            const text = document.getElementById('status-text');
            const tokenInput = document.getElementById('uplink-token');

            btn.innerText = "Initialize Uplink";
            btn.classList.remove('streaming');
            dot.classList.remove('active');
            text.innerText = "System Offline";
            tokenInput.disabled = false;

            // Notify server that we are going offline
            const token = tokenInput.value.trim();
            if (token) {
                try {
                    await fetch('/api/telematics/stop', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ token })
                    });
                } catch (e) {
                    console.error("Failed to notify server of disconnect", e);
                }
            }
        }

        async function sendTelemetry(lat, lng, speed, heading) {
            const token = document.getElementById('uplink-token').value.trim();
            const payload = { token, lat, lng, speed: speed || 0, heading: heading || 0 };

            try {
                const res = await fetch('/api/telematics', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });

                if (res.ok) {
                    pingCount++;
                    document.getElementById('ping-count').innerText = pingCount;
                } else if (res.status === 401) {
                    stopTracking();
                    const errorMsg = document.getElementById('error-msg');
                    errorMsg.innerText = "Invalid token. Check your vehicle's uplink token.";
                    errorMsg.style.display = "block";
                }
            } catch (e) {
                if (navigator.serviceWorker?.controller) {
                    navigator.serviceWorker.controller.postMessage({ type: 'QUEUE_TELEMETRY', payload });
                }
            }
        }
    </script>
</body>
</html>
