<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-10 py-4 bg-white border border-transparent rounded-full font-black text-[10px] text-black uppercase tracking-[0.4em] hover:bg-zinc-200 focus:bg-zinc-200 active:bg-zinc-300 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
