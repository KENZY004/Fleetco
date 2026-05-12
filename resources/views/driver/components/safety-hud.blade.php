@props(['score' => 100])

@php
    $offset = 125.66 - (125.66 * $score / 100);
    $color = '#ef4444'; // Red below 60
    $statusText = 'At Risk';
    if ($score >= 80) {
        $color = '#10b981'; // Emerald
        $statusText = 'Optimal';
    } elseif ($score >= 60) {
        $color = '#ff8a00'; // Orange
        $statusText = 'Caution';
    }
@endphp

<div class="relative w-full max-w-[240px] mx-auto aspect-[2/1] overflow-hidden flex justify-center">
    <svg viewBox="0 0 100 50" class="w-full h-full overflow-visible">
        <!-- Track -->
        <path d="M 10 50 A 40 40 0 0 1 90 50" fill="none" stroke="rgba(255,255,255,0.02)" stroke-width="3" stroke-linecap="round"></path>
        
        <!-- Value -->
        <path d="M 10 50 A 40 40 0 0 1 90 50" fill="none" 
            stroke="{{ $score >= 80 ? '#10b981' : ($score >= 60 ? '#f59e0b' : '#f43f5e') }}" 
            stroke-width="3.5" 
            stroke-linecap="round"
            stroke-dasharray="125.66"
            style="--target-offset: {{ 125.66 - (125.66 * $score / 100) }}; stroke-dashoffset: 125.66; animation: fillGauge 1.5s cubic-bezier(0.4, 0, 0.2, 1) forwards; filter: drop-shadow(0 0 8px currentColor);">
        </path>
    </svg>

    <!-- Score Text -->
    <div class="absolute inset-0 flex items-end justify-center pb-1">
        <div class="text-center">
            <div class="font-heading text-5xl font-black tracking-tight text-white leading-none">{{ number_format($score) }}</div>
            <div class="text-[8px] font-black text-zinc-600 uppercase tracking-[0.3em] mt-1">Safety Index</div>
        </div>
    </div>
</div>

@pushOnce('styles')
<style>
    @keyframes fillGauge {
        to {
            stroke-dashoffset: var(--target-offset);
        }
    }
</style>
@endPushOnce
