@props(['name', 'title', 'subtitle' => 'Fleet Operations'])

<div
    x-show="{{ $name }}"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-[9999] flex items-center justify-center p-6 bg-black/70 backdrop-blur-sm"
    @click.self="{{ $name }} = false"
    style="display: none;"
>
    <div class="glass-obsidian rounded-[2rem] p-10 w-full max-w-md border border-white/10 shadow-2xl" x-transition:enter="transition ease-out duration-300 scale-95" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
        <div class="text-[10px] text-orange-500 uppercase font-bold tracking-widest mb-2">{{ $subtitle }}</div>
        <h2 class="font-heading text-2xl font-bold mb-8">{{ $title }}</h2>

        {{ $slot }}
    </div>
</div>
