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

<div class="bg-white/5 rounded-3xl p-6 relative overflow-hidden border border-white/10 shadow-lg flex flex-col items-center">
    <div class="w-full flex justify-between items-start mb-4">
        <h2 class="font-bold text-lg text-white">Safety Score</h2>
        <span class="text-xs px-2 py-1 rounded-full" style="background-color: {{ $color }}33; color: {{ $color }};">
            {{ $statusText }}
        </span>
    </div>

    <div class="relative w-full max-w-xs aspect-[2/1] overflow-hidden flex justify-center">
        <svg viewBox="0 0 100 50" class="w-full h-full overflow-visible">
            <!-- Track -->
            <circle cx="50" cy="50" r="40" fill="none" stroke="#333" stroke-width="8" stroke-dasharray="125.66 125.66" transform="rotate(180 50 50)" stroke-linecap="round"></circle>
            
            <!-- Value -->
            <circle cx="50" cy="50" r="40" fill="none" stroke="{{ $color }}" stroke-width="8" stroke-dasharray="125.66 125.66" stroke-linecap="round" transform="rotate(180 50 50)"
                style="--target-offset: {{ $offset }}; stroke-dashoffset: 125.66; animation: fillGauge 1.5s ease-out forwards 0.2s;">
            </circle>
        </svg>

        <!-- Score Text -->
        <div class="absolute bottom-0 left-0 right-0 flex justify-center items-end pb-2">
            <span class="text-5xl font-extrabold text-white">{{ $score }}</span>
        </div>
    </div>
</div>

@pushOnce('styles')
<style>
    @keyframes fillGauge {
        from {
            stroke-dashoffset: 125.66;
        }
        to {
            stroke-dashoffset: var(--target-offset);
        }
    }
</style>
@endPushOnce
