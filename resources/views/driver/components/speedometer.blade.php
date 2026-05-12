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
    }">
    <div class="relative w-44 h-44 flex items-center justify-center mx-auto">
        <!-- Digital Gauge Background -->
        <svg viewBox="0 0 100 100" class="absolute inset-0 w-full h-full transform -rotate-90 overflow-visible">
            <!-- Subtle Ticks -->
            @for ($i = 0; $i < 30; $i++)
                <line x1="50" y1="5" x2="50" y2="8" 
                      stroke="rgba(255,255,255,0.05)" 
                      stroke-width="0.5" 
                      transform="rotate({{ $i * 12 }} 50 50)" />
            @endfor

            <!-- Track -->
            <circle cx="50" cy="50" r="44" fill="none" class="stroke-white/[0.02]" stroke-width="1"/>
            
            <!-- Glow Value Ring -->
            <circle cx="50" cy="50" r="44" fill="none" 
                :class="isSpeeding ? 'stroke-rose-500' : 'stroke-orange-500'" 
                stroke-width="2" 
                stroke-dasharray="276"
                :stroke-dashoffset="276 - (276 * Math.min(speedKmh, 160) / 160)"
                class="transition-all duration-1000 cubic-bezier(0.4, 0, 0.2, 1)"
                stroke-linecap="round"
                style="filter: drop-shadow(0 0 12px currentColor); opacity: 0.8;"/>
        </svg>
        
        <div class="flex flex-col items-center z-10">
            <div class="flex items-baseline">
                <span class="font-heading text-7xl font-extrabold tracking-tighter transition-colors duration-500 leading-none" :class="isSpeeding ? 'text-rose-500' : 'text-white'" x-text="speedKmh">0</span>
            </div>
            <span class="text-[9px] font-black text-zinc-600 uppercase tracking-[0.4em] mt-1">KM/H</span>
        </div>
        
        <!-- Professional Offline State -->
        <div x-show="!isOnDuty" x-transition class="absolute inset-0 flex items-center justify-center z-20">
            <div class="bg-black/40 backdrop-blur-md px-5 py-2.5 rounded-2xl border border-white/5 text-[10px] font-black text-zinc-500 uppercase tracking-[0.3em] shadow-2xl">
                Offline
            </div>
        </div>
    </div>
    
    <div class="mt-8 flex flex-col items-center">
        <div class="flex items-center gap-4">
            <div class="flex flex-col items-center">
                <span class="text-[8px] font-black text-zinc-700 uppercase tracking-[0.2em] mb-1">Limit</span>
                <span class="text-xs font-bold text-zinc-400 font-heading tracking-tight">{{ $speedLimit }}</span>
            </div>
            
            <div class="w-px h-6 bg-white/5"></div>
            
            <div x-show="isSpeeding" style="display: none;" class="flex items-center gap-2">
                <div class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-ping"></div>
                <span class="text-[9px] font-black text-rose-500 uppercase tracking-[0.2em]">Breach</span>
            </div>
            <div x-show="!isSpeeding" class="flex items-center gap-2">
                <div class="w-1.5 h-1.5 rounded-full bg-emerald-500/50"></div>
                <span class="text-[9px] font-black text-zinc-700 uppercase tracking-[0.2em]">Optimal</span>
            </div>
        </div>
    </div>
</div>

@pushOnce('scripts')
<script src="https://cdn.jsdelivr.net/npm/nosleep.js@0.12.0/dist/NoSleep.min.js"></script>
@endPushOnce
