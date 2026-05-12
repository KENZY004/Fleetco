import NoSleep from 'nosleep.js';

class DriverTracking {
    constructor() {
        this.noSleep = new NoSleep();
        this.watchId = null;
        this.lastPing = 0;
        this.pingInterval = 5000; // 5 seconds
        this.isOnDuty = false;
        this.token = document.querySelector('meta[name="telemetry-token"]')?.content;
    }

    start() {
        this.isOnDuty = true;
        this.noSleep.enable();
        
        if ("geolocation" in navigator) {
            this.watchId = navigator.geolocation.watchPosition(
                (position) => this.handleUpdate(position),
                (error) => console.error("GPS Error:", error),
                {
                    enableHighAccuracy: true,
                    maximumAge: 0,
                    timeout: 5000
                }
            );
        }
    }

    stop() {
        this.isOnDuty = false;
        this.noSleep.disable();
        if (this.watchId) {
            navigator.geolocation.clearWatch(this.watchId);
        }
    }

    handleUpdate(position) {
        if (!this.isOnDuty) return;

        const now = Date.now();
        if (now - this.lastPing < this.pingInterval) return;

        this.lastPing = now;
        
        const payload = {
            token: this.token,
            lat: position.coords.latitude,
            lng: position.coords.longitude,
            speed: position.coords.speed || 0,
            heading: position.coords.heading || 0,
            captured_at: new Date().toISOString()
        };

        fetch('/api/telematics', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
            },
            body: JSON.stringify(payload)
        }).catch(err => console.error("Ping failed:", err));
    }
}

window.DriverTracking = new DriverTracking();
