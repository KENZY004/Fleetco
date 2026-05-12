@props(['currentLog' => null])

@php
    $status = $currentLog->status ?? 'off_duty';
    // JavaScript uses milliseconds for timestamps
    $startTime = $currentLog ? $currentLog->started_at->timestamp * 1000 : 'null';
@endphp

<div x-data="{ 
        status: '{{ $status }}', 
        startTime: {{ $startTime }}, 
        timer: '00:00:00',
        updateTimer() {
            if (this.status === 'on_duty' && this.startTime) {
                let diff = Math.floor((Date.now() - this.startTime) / 1000);
                if (diff < 0) diff = 0; // Prevent negative timer if clocks slightly out of sync
                let h = Math.floor(diff / 3600);
                let m = Math.floor((diff % 3600) / 60);
                let s = diff % 60;
                this.timer = [h, m, s].map(v => v < 10 ? '0'+v : v).join(':');
            } else {
                this.timer = '00:00:00';
            }
        }
    }" 
    x-init="setInterval(() => updateTimer(), 1000); updateTimer();"
    class="bg-white/5 rounded-3xl p-6 border border-white/10 shadow-lg">

    <div class="grid grid-cols-3 gap-3 mb-6">
        <!-- On Duty -->
        <form action="{{ route('driver.duty.on') }}" method="POST" class="w-full">
            @csrf
            <button type="submit" 
                class="w-full py-4 rounded-2xl font-bold text-xs uppercase tracking-wider transition-colors"
                :class="status === 'on_duty' ? 'bg-emerald-500 text-white shadow-[0_0_15px_rgba(16,185,129,0.3)]' : 'bg-white/10 text-white/50 hover:bg-white/20'">
                On Duty
            </button>
        </form>

        <!-- Break -->
        <form action="{{ route('driver.duty.break') }}" method="POST" class="w-full">
            @csrf
            <button type="submit" 
                class="w-full py-4 rounded-2xl font-bold text-xs uppercase tracking-wider transition-colors"
                :class="status === 'break' ? 'bg-orange-500 text-white shadow-[0_0_15px_rgba(249,115,22,0.3)]' : 'bg-white/10 text-white/50 hover:bg-white/20'">
                Break
            </button>
        </form>

        <!-- Off Duty -->
        <form action="{{ route('driver.duty.off') }}" method="POST" class="w-full" onsubmit="return confirm('Are you sure you want to go OFF DUTY? This will end your current shift.');">
            @csrf
            <button type="submit" 
                class="w-full py-4 rounded-2xl font-bold text-xs uppercase tracking-wider transition-colors"
                :class="status === 'off_duty' ? 'bg-red-500 text-white shadow-[0_0_15px_rgba(239,68,68,0.3)]' : 'bg-white/10 text-white/50 hover:bg-white/20'">
                Off Duty
            </button>
        </form>
    </div>

    <div class="flex justify-between items-center px-4 py-3 bg-black/30 rounded-xl border border-white/5">
        <span class="text-sm font-bold text-white/50 uppercase tracking-widest">Shift Timer</span>
        <span class="font-mono text-2xl font-bold" :class="status === 'on_duty' ? 'text-emerald-400' : 'text-white/30'" x-text="timer">00:00:00</span>
    </div>
</div>
