@extends('layouts.app')

@section('content')
<div class="h-full flex items-center justify-center p-8">
    <div class="w-full max-w-2xl fleetco-card p-12 rounded-[2.5rem] relative overflow-hidden group">
        <!-- Accent Glow -->
        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-primary/50 to-transparent opacity-50"></div>
        
        <div class="flex items-center gap-6 mb-12">
            <div class="h-16 w-16 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-primary group-hover:scale-110 transition-transform duration-500 shadow-[0_0_40px_rgba(255,138,0,0.1)]">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <div>
                <div class="text-[10px] text-zinc-500 uppercase font-black tracking-[0.4em] mb-1">Fleet Expansion</div>
                <h2 class="text-3xl font-extrabold text-white tracking-tighter">Initialize Neural Unit</h2>
            </div>
        </div>

        <form action="{{ route('vehicles.store') }}" method="POST" class="space-y-8">
            @csrf

            <div class="grid grid-cols-2 gap-8">
                <div class="space-y-3">
                    <x-input-label for="name" :value="__('Unit Identifier')" />
                    <x-text-input id="name" name="name" type="text" class="w-full" placeholder="e.g. Scout-03" required />
                    <x-input-error :messages="$errors->get('name')" />
                </div>

                <div class="space-y-3">
                    <x-input-label for="license_plate" :value="__('Registry Plate')" />
                    <x-text-input id="license_plate" name="license_plate" type="text" class="w-full" placeholder="e.g. MH-01-AX-1234" required />
                    <x-input-error :messages="$errors->get('license_plate')" />
                </div>
            </div>

            <div class="grid grid-cols-2 gap-8">
                <div class="space-y-3">
                    <x-input-label for="model" :value="__('Unit Model')" />
                    <x-text-input id="model" name="model" type="text" class="w-full" placeholder="e.g. Tesla Semi" />
                    <x-input-error :messages="$errors->get('model')" />
                </div>

                <div class="space-y-3">
                    <x-input-label for="type" :value="__('Classification')" />
                    <select id="type" name="type" class="w-full bg-white/5 border-white/10 text-white focus:border-primary focus:ring-primary rounded-xl shadow-sm transition-all duration-300">
                        <option value="truck" class="bg-obsidian-900">Heavy Truck</option>
                        <option value="van" class="bg-obsidian-900">Light Van</option>
                        <option value="car" class="bg-obsidian-900">Command Car</option>
                        <option value="bike" class="bg-obsidian-900">Rapid Bike</option>
                    </select>
                    <x-input-error :messages="$errors->get('type')" />
                </div>
            </div>

            <div class="pt-8">
                <x-primary-button>
                    {{ __('Synchronize with Fleet') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</div>
@endsection
