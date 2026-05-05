@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-black text-[9px] text-zinc-500 uppercase tracking-[0.4em] mb-3']) }}>
    {{ $value ?? $slot }}
</label>
