@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'bg-white/5 border-white/10 focus:border-orange-500 focus:ring-orange-500 rounded-xl shadow-sm text-white placeholder-zinc-600 font-bold text-sm py-4']) }}>
