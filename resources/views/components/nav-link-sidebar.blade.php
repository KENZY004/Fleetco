@props(['icon', 'route', 'label'])

@php
    $active = request()->routeIs($route);
    $icons = [
        'dashboard' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
        'driver' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
        'vehicle' => 'M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0',
        'trips' => 'M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7',
        'alert' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
        'geofence' => 'M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7',
        'mobile' => 'M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z'
    ];
@endphp

<a href="{{ route($route) }}" 
   class="relative group flex flex-col items-center gap-1 transition-all"
   title="{{ $label }}">
    <div @class([
        'w-12 h-12 rounded-2xl flex items-center justify-center transition-all duration-300',
        'bg-primary text-black shadow-lg shadow-primary/20' => $active,
        'text-zinc-500 hover:text-white hover:bg-white/5' => !$active
    ])>
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $icons[$icon] }}"/>
        </svg>
    </div>
    <span @class([
        'text-[9px] font-black uppercase tracking-widest transition-colors',
        'text-primary' => $active,
        'text-zinc-600 group-hover:text-zinc-400' => !$active
    ])>{{ $label }}</span>
    
    @if($active)
        {{-- Desktop Indicator (Left) --}}
        <div class="hidden md:block absolute -left-3 top-1/2 -translate-y-1/2 w-1 h-8 bg-primary rounded-r-full shadow-[0_0_15px_#ff8a00]"></div>
        {{-- Mobile Indicator (Top) --}}
        <div class="md:hidden absolute -top-1 left-1/2 -translate-x-1/2 w-6 h-1 bg-primary rounded-b-full shadow-[0_0_15px_#ff8a00]"></div>
    @endif
</a>
