@props(['vehicle' => null])

@php
    $isLive = false;
    if ($vehicle && $vehicle->latestTelematics) {
        $lastPing = $vehicle->latestTelematics->captured_at;
        if ($lastPing && $lastPing->diffInSeconds(now()) <= 60) {
            $isLive = true;
        }
    }
@endphp

@if($vehicle)
<div x-data="{ 
        modalOpen: false, 
        submitting: false, 
        toastOpen: false, 
        issueType: 'Engine', 
        description: '',
        submitForm() {
            this.submitting = true;
            fetch('{{ route('driver.maintenance.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    issue_type: this.issueType,
                    description: this.description
                })
            })
            .then(res => res.json())
            .then(data => {
                this.submitting = false;
                this.modalOpen = false;
                this.issueType = 'Engine';
                this.description = '';
                this.toastOpen = true;
                setTimeout(() => this.toastOpen = false, 3000);
            })
            .catch(err => {
                this.submitting = false;
                alert('Failed to submit report.');
            });
        }
    }" 
    class="bg-white/5 rounded-3xl p-6 border border-white/10 shadow-lg relative">

    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-4">
            <div class="p-3 bg-white/5 rounded-2xl">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-accent-orange"><path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/><path d="M15 18H9"/><path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14"/><circle cx="17" cy="18" r="2"/><circle cx="7" cy="18" r="2"/></svg>
            </div>
            <div>
                <h3 class="font-bold text-lg text-white">{{ $vehicle->name }}</h3>
                <p class="text-xs text-white/40 uppercase tracking-widest font-mono">{{ $vehicle->license_plate }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2 px-3 py-1 bg-black/30 rounded-full border border-white/5">
            @if($isLive)
                <div class="w-2 h-2 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.8)] animate-pulse"></div>
                <span class="text-[10px] text-emerald-400 font-bold uppercase tracking-wider">Live</span>
            @else
                <div class="w-2 h-2 rounded-full bg-white/20"></div>
                <span class="text-[10px] text-white/40 font-bold uppercase tracking-wider">Offline</span>
            @endif
        </div>
    </div>

    <button @click="modalOpen = true" class="w-full py-3 rounded-xl bg-white/5 border border-white/10 text-xs font-bold uppercase tracking-wider flex items-center justify-center gap-2 hover:bg-white/10 transition-colors text-white">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-orange-400"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        Report Mechanical Issue
    </button>

    <!-- Modal -->
    <div x-show="modalOpen" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" x-transition.opacity>
        <div @click.away="modalOpen = false" class="bg-[#111] border border-white/10 rounded-3xl w-full max-w-md p-6 shadow-2xl relative" x-transition.scale.origin.bottom>
            <button @click="modalOpen = false" class="absolute top-4 right-4 text-white/40 hover:text-white">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
            
            <h2 class="text-xl font-bold text-white mb-4">Report Issue</h2>
            <p class="text-sm text-white/50 mb-6">Submit a maintenance request for {{ $vehicle->license_plate }}.</p>
            
            <form @submit.prevent="submitForm">
                <div class="mb-4">
                    <label class="block text-xs font-bold text-white/60 uppercase tracking-wider mb-2">Issue Type</label>
                    <select x-model="issueType" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-accent-orange">
                        <option value="Engine">Engine</option>
                        <option value="Tyres">Tyres</option>
                        <option value="Brakes">Brakes</option>
                        <option value="Electrical">Electrical</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                
                <div class="mb-6">
                    <label class="block text-xs font-bold text-white/60 uppercase tracking-wider mb-2">Description</label>
                    <textarea x-model="description" required rows="3" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-accent-orange placeholder:text-white/20" placeholder="Describe the problem..."></textarea>
                </div>
                
                <button type="submit" :disabled="submitting" class="w-full py-4 rounded-xl font-bold text-sm uppercase tracking-wider transition-colors bg-accent-orange text-black hover:bg-orange-500 disabled:opacity-50">
                    <span x-show="!submitting">Submit Report</span>
                    <span x-show="submitting">Submitting...</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Toast Notification -->
    <div x-show="toastOpen" style="display: none;" class="fixed bottom-4 left-1/2 -translate-x-1/2 bg-emerald-500 text-white px-6 py-3 rounded-full font-bold text-sm shadow-xl flex items-center gap-2 z-50" x-transition.translate.y>
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
        Report submitted successfully
    </div>
</div>
@else
<div class="bg-white/5 rounded-3xl p-6 border border-white/10 shadow-lg flex items-center justify-center text-center">
    <div>
        <div class="w-12 h-12 rounded-full bg-white/5 flex items-center justify-center mx-auto mb-3">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white/30"><path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/><path d="M15 18H9"/><path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14"/><circle cx="17" cy="18" r="2"/><circle cx="7" cy="18" r="2"/></svg>
        </div>
        <p class="text-white/50 text-sm font-bold uppercase tracking-wider">No Vehicle Assigned</p>
    </div>
</div>
@endif
