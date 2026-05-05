@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'bg-white/5 border-white/10 text-white focus:border-primary focus:ring-primary rounded-xl shadow-sm transition-all duration-300 placeholder:text-zinc-600']) !!}>
