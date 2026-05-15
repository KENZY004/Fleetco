@props(['anomalies'])

<div class="flex flex-col h-full overflow-hidden" x-data="alertFeed()">
    <div class="py-4 md:py-6 px-6 md:px-8 border-b border-white/5 bg-white/[0.02] flex justify-between items-center shrink-0">
        <h3 class="font-heading text-xs md:text-sm font-bold text-white tracking-tight">Security Alerts</h3>
        <div class="flex items-center gap-2 md:gap-4">
            <button 
                x-show="Notification.permission !== 'granted'" 
                @click="Notification.requestPermission().then(() => $el.remove())"
                class="text-[8px] md:text-[9px] font-bold text-orange-500 uppercase tracking-widest hover:text-orange-400 transition-colors"
            >
                Notify
            </button>
            <button 
                @click="resolveAll()" 
                x-show="alerts.length > 0"
                class="text-[8px] md:text-[9px] font-bold text-zinc-500 uppercase tracking-widest hover:text-white transition-colors border border-white/10 px-2 py-1 rounded-md bg-white/5"
            >
                Clear All
            </button>
            <div class="flex items-center gap-1.5 md:gap-2 px-2 py-0.5 md:px-2.5 md:py-1 bg-emerald-500/10 border border-emerald-500/20 rounded-lg">
                <div class="h-1 w-1 md:h-1.5 md:w-1.5 bg-emerald-500 rounded-full" :class="isScanning ? 'animate-pulse' : ''"></div>
                <span class="text-[8px] md:text-[10px] font-bold text-emerald-500 uppercase tracking-wider italic">Scanning</span>
            </div>
        </div>
    </div>
    <div class="flex-1 overflow-y-auto custom-scrollbar">
        <div class="divide-y divide-white/5">
            <template x-for="alert in alerts" :key="alert.id">
                <div class="p-6 hover:bg-white/[0.04] transition-colors flex flex-col gap-4 group">
                    <div class="flex items-center gap-5">
                        <div class="h-10 w-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center flex-shrink-0 group-hover:border-red-500/50 transition-colors">
                            <svg class="w-5 h-5 text-zinc-500 group-hover:text-red-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-baseline mb-1">
                                <h4 class="text-sm font-bold text-white tracking-tight truncate" x-text="formatType(alert.type, alert.details)"></h4>
                                <span class="text-[10px] font-medium text-zinc-600" x-text="timeAgo(alert.occurred_at)"></span>
                            </div>
                            <p class="text-xs text-zinc-500 font-medium">
                                Unit <span class="text-zinc-300 font-bold tracking-wider" x-text="alert.vehicle?.license_plate"></span> • Impact: -<span x-text="alert.impact_score"></span>
                            </p>
                        </div>
                    </div>
                    
                    {{-- Admin Actions --}}
                    <div class="flex flex-wrap items-center gap-2 md:pl-15">
                        <template x-if="alert.driver?.phone_number">
                            <a :href="'tel:' + alert.driver.phone_number" class="flex-1 sm:flex-none px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-[10px] font-bold text-zinc-400 uppercase tracking-widest hover:bg-white/10 hover:text-white transition-all flex items-center justify-center gap-2">
                                <svg class="w-3 h-3 text-emerald-500" fill="currentColor" viewBox="0 0 24 24"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/></svg>
                                Contact
                            </a>
                        </template>
                        <button @click="window.dispatchEvent(new CustomEvent('inspect-alert', { detail: alert }))" class="flex-1 sm:flex-none px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-[10px] font-bold text-zinc-400 uppercase tracking-widest hover:bg-white/10 hover:text-white transition-all flex items-center justify-center gap-2">
                            <svg class="w-3 h-3 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            Inspect
                        </button>
                        <button @click="dismissAlert(alert.id)" class="flex-1 sm:flex-none px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-[10px] font-bold text-zinc-400 uppercase tracking-widest hover:bg-white/10 hover:text-white transition-all text-center">
                            Resolve
                        </button>
                    </div>
                </div>
            </template>

            <template x-if="alerts.length === 0">
                <div class="p-12 text-center">
                    <p class="text-xs text-zinc-500 font-medium tracking-tight">No critical alerts detected</p>
                </div>
            </template>
        </div>
    </div>
</div>

<script>
    function alertFeed() {
        return {
            alerts: @json($anomalies),
            isScanning: true,
            
            init() {
                // Initialize lastAlertId from existing data to prevent 'new alert' triggers on load
                if (this.alerts.length > 0) {
                    this.lastAlertId = Math.max(...this.alerts.map(a => a.id));
                }
                // Wait 5 seconds before starting to poll for NEW alerts
                setTimeout(() => {
                    setInterval(() => this.pollAlerts(), 5000);
                }, 5000);
            },

            async pollAlerts() {
                try {
                    const response = await fetch('/api/alerts');
                    const data = await response.json();
                    
                    // Check for truly new alerts to trigger notifications
                    if (this.lastAlertId && data.length > 0) {
                        data.forEach(alert => {
                            if (alert.id > this.lastAlertId) {
                                window.dispatchEvent(new CustomEvent('new-alert-detected', { detail: alert }));
                                this.showBrowserNotification(alert);
                            }
                        });
                    }
                    
                    this.alerts = data;
                    if (data.length > 0) {
                        this.lastAlertId = Math.max(...data.map(a => a.id));
                    }
                } catch (e) {
                    console.error('Alert poll failed', e);
                }
            },

            async dismissAlert(alertId) {
                try {
                    await fetch(`/api/alerts/${alertId}/resolve`, {
                        method: 'POST',
                        headers: { 
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    });
                    this.alerts = this.alerts.filter(a => a.id !== alertId);
                } catch (e) {
                    console.error('Resolve failed', e);
                }
            },

            async resolveAll() {
                if (!confirm('Resolve all active security alerts?')) return;
                try {
                    await fetch(`/api/alerts/resolve-all`, {
                        method: 'POST',
                        headers: { 
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    });
                    this.alerts = [];
                } catch (e) {
                    console.error('Bulk resolve failed', e);
                }
            },

            showBrowserNotification(alert) {
                if (!("Notification" in window)) return;
                
                if (Notification.permission === "granted") {
                    const title = alert.type === 'speeding' ? '🚨 Speeding Alert' : '🚩 Geofence Breach';
                    const body = `Unit ${alert.vehicle?.license_plate}: ${this.formatType(alert.type, alert.details)}`;
                    
                    new Notification(title, {
                        body: body,
                        icon: '/favicon.ico'
                    });
                } else if (Notification.permission !== "denied") {
                    Notification.requestPermission();
                }
            },

            formatType(type, details) {
                if (type === 'speeding') return 'High Speed Detected';
                if (type === 'geofence_breach') {
                    if (details.breach_type === 'route_deviation') return 'Route Deviation';
                    return 'Unauthorized Entry';
                }
                return 'Security Alert';
            },

            timeAgo(dateString) {
                const date = new Date(dateString);
                const seconds = Math.floor((new Date() - date) / 1000);
                if (seconds < 60) return 'just now';
                const minutes = Math.floor(seconds / 60);
                if (minutes < 60) return minutes + 'm';
                const hours = Math.floor(minutes / 60);
                return hours + 'h';
            }
        }
    }
</script>
