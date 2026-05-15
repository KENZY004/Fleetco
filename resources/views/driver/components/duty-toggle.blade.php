@props([
    'currentLog' => null,
    'previousSeconds' => 0,
    'currentSegmentStart' => null,
    'accumulatedSeconds' => 0
])

@php
    $status = $currentLog->status ?? 'off_duty';
@endphp

<div x-data="{ 
        status: '{{ $status }}', 
        previousSeconds: {{ (int) $previousSeconds }},
        segmentStart: {{ $currentSegmentStart ? "new Date('$currentSegmentStart').getTime()" : 'null' }},
        accumulated: {{ (int) $accumulatedSeconds }},
        timer: '00:00:00',
        updateTimer() {
            if (this.status === 'on_duty' && this.segmentStart) {
                let liveSeconds = Math.floor((Date.now() - this.segmentStart) / 1000);
                let total = this.previousSeconds + liveSeconds;
                this.format(total);
            } else {
                this.format(this.accumulated);
            }
        },
        format(total) {
            let h = Math.floor(total / 3600);
            let m = Math.floor((total % 3600) / 60);
            let s = total % 60;
            this.timer = [h, m, s].map(v => v < 10 ? '0'+v : v).join(':');
        }
    }" 
    x-init="setInterval(() => updateTimer(), 100); updateTimer();"
    class="w-full">

    <div class="grid grid-cols-3 gap-3 mb-8">
        <!-- On Duty -->
        <form action="{{ route('driver.duty.on') }}" method="POST" class="w-full">
            @csrf
            <button type="submit" 
                @click="window.dispatchEvent(new CustomEvent('duty-starting')); triggerInstantUplink();"
                class="w-full py-3 rounded-xl font-black text-[9px] uppercase tracking-[0.2em] transition-all duration-300 border"
                :class="status === 'on_duty' ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-400' : 'bg-white/5 border-white/5 text-zinc-600 hover:text-white'">
                On Duty
            </button>
        </form>

        <!-- Break -->
        <form action="{{ route('driver.duty.break') }}" method="POST" class="w-full">
            @csrf
            <button type="submit" 
                class="w-full py-3 rounded-xl font-black text-[9px] uppercase tracking-[0.2em] transition-all duration-300 border"
                :class="status === 'break' ? 'bg-orange-500/10 border-orange-500/30 text-orange-400' : 'bg-white/5 border-white/5 text-zinc-600 hover:text-white'">
                Break
            </button>
        </form>

        <!-- Off Duty -->
        <form action="{{ route('driver.duty.off') }}" method="POST" class="w-full" onsubmit="return confirm('Are you sure you want to go OFF DUTY? This will end your current shift.');">
            @csrf
            <button type="submit" 
                class="w-full py-3 rounded-xl font-black text-[9px] uppercase tracking-[0.2em] transition-all duration-300 border"
                :class="status === 'off_duty' ? 'bg-rose-500/10 border-rose-500/30 text-rose-400' : 'bg-white/5 border-white/5 text-zinc-600 hover:text-white'">
                Off Duty
            </button>
        </form>
    </div>

    <div class="flex flex-col items-center gap-1">
        <span class="text-[8px] font-black text-zinc-700 uppercase tracking-[0.4em]">Shift Chronometer</span>
        <span class="font-heading text-4xl font-black tracking-tight leading-none" :class="status === 'on_duty' ? 'text-white' : 'text-zinc-800'" x-text="timer">00:00:00</span>
    </div>
</div>
