@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">

    {{-- Page Header --}}
    <div>
        <div class="text-[10px] font-bold tracking-widest text-orange-500 uppercase mb-2">Command Center</div>
        <h1 class="font-heading text-3xl font-bold tracking-tight">Profile Settings</h1>
        <p class="text-zinc-500 text-sm mt-1">Manage your identity, security and system preferences.</p>
    </div>

    {{-- Forms Grid --}}
    <div class="space-y-8">
        <x-fleet-card>
            @include('profile.partials.update-profile-information-form')
        </x-fleet-card>

        <x-fleet-card>
            @include('profile.partials.update-password-form')
        </x-fleet-card>

        <x-fleet-card class="border-red-500/20">
            @include('profile.partials.delete-user-form')
        </x-fleet-card>
    </div>
</div>
@endsection

