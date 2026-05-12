@props(['isOnDuty' => false, 'speedLimit' => 100])

<div x-data="{
        isOnDuty: {{ $isOnDuty ? 'true' : 'false' }},
        speedLimit: {{ $speedLimit }},
        speedKmh: 0,
        isSpeeding: false,
        watchId: null,
        wakeLock: null,
        noSleep: null,
        lastPing: 0,
        
        async init() {
            if (this.isOnDuty) {
                this.startTracking();
            }
        },
        
        async startTracking() {
            // Request Wake Lock natively if supported
            if ('wakeLock' in navigator) {
                try {
                    this.wakeLock = await navigator.wakeLock.request('screen');
                    // Re-acquire if visibility changes (e.g. minimizing and returning to app)
                    document.addEventListener('visibilitychange', async () => {
                        if (this.wakeLock !== null && document.visibilityState === 'visible') {
                            this.wakeLock = await navigator.wakeLock.request('screen');
                        }
                    });
                } catch (err) {
                    this.fallbackNoSleep();
                }
            } else {
                this.fallbackNoSleep();
            }
            
            // Start GPS Watch
            if ('geolocation' in navigator) {
                this.watchId = navigator.geolocation.watchPosition(
                    position => this.handlePosition(position),
                    err => console.error('GPS Error:', err),
                    { enableHighAccuracy: true, maximumAge: 0, timeout: 5000 }
                );
            }
        },
        
        fallbackNoSleep() {
            if (window.NoSleep) {
                this.noSleep = new window.NoSleep();
                // NoSleep requires a user interaction to start playing a dummy video
                document.addEventListener('click', function enableNoSleep() {
                    document.removeEventListener('click', enableNoSleep, false);
                    this.noSleep.enable();
                }.bind(this), false);
            }
        },
        
        stopTracking() {
            if (this.watchId) navigator.geolocation.clearWatch(this.watchId);
            if (this.wakeLock) {
                this.wakeLock.release().then(() => this.wakeLock = null);
            }
            if (this.noSleep) this.noSleep.disable();
        },
        
        handlePosition(position) {
            // speed is in m/s, convert to km/h
            const speedMs = position.coords.speed || 0;
            this.speedKmh = Math.round(speedMs * 3.6);

            // Dispatch global event for the route card map
            window.dispatchEvent(new CustomEvent('gps-update', { 
                detail: { 
                    lat: position.coords.latitude, 
                    lng: position.coords.longitude 
                } 
            }));
            
            // Speed Alert Logic
            if (this.speedKmh > this.speedLimit) {
                this.isSpeeding = true;
                if ('vibrate' in navigator) navigator.vibrate(200);
            } else {
                this.isSpeeding = false;
            }
            
            // Ping every 4 seconds
            const now = Date.now();
            if (now - this.lastPing >= 4000) {
                this.lastPing = now;
                this.sendTelemetry(position);
            }
        },
        
        sendTelemetry(position) {
            fetch('{{ route('driver.telemetry.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    latitude: position.coords.latitude,
                    longitude: position.coords.longitude,
                    speed_kmh: this.speedKmh,
                    heading: position.coords.heading,
                    // Use actual timestamp from the device
                    captured_at: new Date(position.timestamp || Date.now()).toISOString()
                })
            }).catch(e => console.error('Telemetry post failed:', e));
        }
    }"
    class="bg-white/5 rounded-3xl p-6 border border-white/10 shadow-lg relative overflow-hidden flex flex-col items-center justify-center min-h-[250px] transition-colors duration-500"
    :class="isSpeeding ? 'bg-red-500/20 border-red-500/50 shadow-[0_0_30px_rgba(239,68,68,0.3)]' : ''"
>
    <!-- Speedometer SVG -->
    <div class="relative w-48 h-48 flex items-center justify-center">
        <!-- Background ring -->
        <svg viewBox="0 0 100 100" class="absolute inset-0 w-full h-full transform -rotate-90">
            <circle cx="50" cy="50" r="45" fill="none" class="stroke-white/10" stroke-width="4"/>
            <!-- Value ring (282.74 is 2*pi*45) -->
            <circle cx="50" cy="50" r="45" fill="none" 
                :class="isSpeeding ? 'stroke-red-500' : 'stroke-emerald-400'" 
                stroke-width="6" 
                stroke-dasharray="282.74"
                :stroke-dashoffset="282.74 - (282.74 * Math.min(speedKmh, 160) / 160)"
                class="transition-all duration-300 ease-out"
                stroke-linecap="round"/>
        </svg>
        
        <div class="flex flex-col items-center z-10">
            <span class="text-6xl font-extrabold font-mono tracking-tighter" :class="isSpeeding ? 'text-red-400' : 'text-white'" x-text="speedKmh">0</span>
            <span class="text-xs font-bold text-white/40 uppercase tracking-widest mt-1">KM/H</span>
        </div>
    </div>
    
    <div class="mt-4 flex items-center gap-2">
        <span class="text-xs font-bold text-white/50 uppercase">Limit: {{ $speedLimit }}</span>
        <div x-show="isSpeeding" style="display: none;" class="px-2 py-1 bg-red-500 rounded text-[10px] font-bold text-white uppercase animate-pulse">Overspeed</div>
    </div>

    <div x-show="!isOnDuty" class="absolute inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-20">
        <span class="text-white/80 font-bold uppercase tracking-widest text-sm">Offline</span>
    </div>
</div>

@pushOnce('scripts')
<script src="https://cdn.jsdelivr.net/npm/nosleep.js@0.12.0/dist/NoSleep.min.js"></script>
@endPushOnce
