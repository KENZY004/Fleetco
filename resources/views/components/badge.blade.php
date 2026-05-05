@props(['type' => 'neutral'])

@php
    $colors = [
        'success' => 'bg-emerald-500/10 border-emerald-500/30 text-emerald-400',
        'warning' => 'bg-amber-500/10 border-amber-500/30 text-amber-400',
        'danger' => 'bg-red-500/10 border-red-500/30 text-red-400',
        'info' => 'bg-blue-500/10 border-blue-500/30 text-blue-400',
        'neutral' => 'bg-zinc-800 border-zinc-700 text-zinc-500',
    ];
    $colorClass = $colors[$type] ?? $colors['neutral'];
@endphp

<div {{ $attributes->merge(['class' => "px-3 py-1 rounded-full text-[9px] font-bold uppercase tracking-wider border $colorClass"]) }}>
    {{ $slot }}
</div>
