<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center w-full px-8 py-4 bg-white text-black border border-transparent rounded-full font-black text-[10px] uppercase tracking-[0.3em] hover:bg-white/90 active:bg-white focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 focus:ring-offset-black transition ease-in-out duration-150 shadow-[0_10px_40px_rgba(255,255,255,0.1)] active:scale-95']) }}>
    {{ $slot }}
</button>
