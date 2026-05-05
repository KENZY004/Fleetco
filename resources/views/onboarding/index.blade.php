<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FleetCo | Initialization</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { background-color: #020203; color: #ffffff; font-family: 'Outfit', sans-serif; overflow: hidden; }
        .text-primary { color: #ff8a00; }
        .bg-primary { background-color: #ff8a00; }
        .border-primary { border-color: #ff8a00; }
    </style>
</head>
<body class="antialiased" x-data="onboardingWizard()">
    <div class="h-screen flex bg-obsidian-950 text-white overflow-hidden">
        
        <!-- Left Side: Strategic Briefing Form -->
        <div class="w-full lg:w-[500px] p-8 lg:p-12 flex flex-col justify-between border-r border-white/5 bg-obsidian-900/20 backdrop-blur-3xl z-10 shrink-0">
            <div>
                <div class="mb-10">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="h-8 w-8 rounded-lg bg-primary/10 border border-primary/20 flex items-center justify-center">
                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <h2 class="text-[10px] font-black uppercase tracking-[0.5em] text-zinc-500 italic">Core Initialization</h2>
                    </div>
                    <div class="flex gap-1 h-1 w-full bg-white/5 rounded-full overflow-hidden">
                        <div class="h-full bg-primary transition-all duration-700" :style="'width: ' + ((step/4)*100) + '%'"></div>
                    </div>
                </div>

                <div class="min-h-[400px]">
                    <!-- Step 1: Operational Theater -->
                    <div x-show="step === 1" x-transition.opacity.duration.400ms>
                        <h1 class="text-3xl font-black uppercase tracking-tight mb-2 italic">Operational <span class="text-primary">Theater</span></h1>
                        <p class="text-zinc-500 text-[11px] mb-8 uppercase tracking-widest">Base of Command & Tactical Units</p>
                        
                        <div class="space-y-6">
                            <div>
                                <label class="text-[9px] font-bold uppercase tracking-widest text-zinc-600 block mb-2">Organization Name</label>
                                <input type="text" x-model="form.company_name" class="w-full bg-white/5 border-white/10 rounded-xl px-4 py-3 text-sm focus:border-primary focus:ring-0 transition-all text-white" placeholder="APEX LOGISTICS">
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-[9px] font-bold uppercase tracking-widest text-zinc-600 block mb-2">Command Region</label>
                                    <select x-model="form.region" class="w-full bg-white/5 border-white/10 rounded-xl px-4 py-3 text-[10px] uppercase font-bold focus:border-primary focus:ring-0 transition-all text-white">
                                        <option value="IN">India (IST)</option>
                                        <option value="US">USA (EST)</option>
                                        <option value="UK">UK (GMT)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="text-[9px] font-bold uppercase tracking-widest text-zinc-600 block mb-2">Metric Units</label>
                                    <div class="flex bg-white/5 rounded-xl p-1 border border-white/10">
                                        <button @click="form.units = 'km'" :class="form.units === 'km' ? 'bg-primary text-black' : 'text-zinc-500'" class="flex-1 py-2 rounded-lg text-[9px] font-black uppercase tracking-widest transition-all">KM</button>
                                        <button @click="form.units = 'miles'" :class="form.units === 'miles' ? 'bg-primary text-black' : 'text-zinc-500'" class="flex-1 py-2 rounded-lg text-[9px] font-black uppercase tracking-widest transition-all">MI</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Fleet Intelligence -->
                    <div x-show="step === 2" x-transition.opacity.duration.400ms>
                        <h1 class="text-3xl font-black uppercase tracking-tight mb-2 italic">Fleet <span class="text-primary">Intelligence</span></h1>
                        <p class="text-zinc-500 text-[11px] mb-8 uppercase tracking-widest">Asset Scale & Industry Matrix</p>
                        
                        <div class="space-y-6">
                            <div>
                                <label class="text-[9px] font-bold uppercase tracking-widest text-zinc-600 block mb-2">Asset Scale</label>
                                <div class="grid grid-cols-4 gap-2">
                                    <template x-for="scale in ['1-10', '11-50', '51-200', '200+']">
                                        <button @click="form.scale = scale" :class="form.scale === scale ? 'bg-primary text-black border-primary' : 'bg-white/5 border-white/10 text-zinc-500'" class="py-3 rounded-xl text-[9px] font-black uppercase tracking-widest border transition-all" x-text="scale"></button>
                                    </template>
                                </div>
                            </div>
                            <div>
                                <label class="text-[9px] font-bold uppercase tracking-widest text-zinc-600 block mb-2">Industry Sector</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <template x-for="sector in ['logistics', 'construction', 'services', 'delivery']">
                                        <button @click="form.industry = sector" :class="form.industry === sector ? 'bg-primary text-black border-primary' : 'bg-white/5 border-white/10 text-zinc-500'" class="py-3 rounded-xl text-[9px] font-black uppercase tracking-widest border transition-all text-center" x-text="sector"></button>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Strategic Focus -->
                    <div x-show="step === 3" x-transition.opacity.duration.400ms>
                        <h1 class="text-3xl font-black uppercase tracking-tight mb-2 italic">Strategic <span class="text-primary">Focus</span></h1>
                        <p class="text-zinc-500 text-[11px] mb-8 uppercase tracking-widest">Primary Operational Objective</p>
                        
                        <div class="space-y-3">
                            <template x-for="goal in [
                                { id: 'uptime', label: 'Asset Uptime', desc: 'Maintenance & Health' },
                                { id: 'tracking', label: 'Live Command', desc: 'Safety & Accuracy' },
                                { id: 'cost', label: 'Efficiency', desc: 'Fuel & Routes' }
                            ]">
                                <button @click="form.goal = goal.id" :class="form.goal === goal.id ? 'border-primary bg-primary/10' : 'border-white/10 bg-white/5'" class="w-full text-left p-4 rounded-2xl border transition-all flex items-center justify-between">
                                    <div>
                                        <div class="text-[10px] font-black uppercase tracking-widest mb-1" :class="form.goal === goal.id ? 'text-primary' : 'text-zinc-400'" x-text="goal.label"></div>
                                        <div class="text-[9px] text-zinc-600 uppercase" x-text="goal.desc"></div>
                                    </div>
                                    <div x-show="form.goal === goal.id" class="h-2 w-2 rounded-full bg-primary shadow-[0_0_10px_rgba(255,138,0,1)]"></div>
                                </button>
                            </template>
                        </div>
                    </div>

                    <!-- Step 4: Finalization -->
                    <div x-show="step === 4" x-transition.opacity.duration.400ms>
                        <h1 class="text-3xl font-black uppercase tracking-tight mb-2 italic">System <span class="text-primary">Ready</span></h1>
                        <p class="text-zinc-500 text-[11px] mb-8 uppercase tracking-widest">Initialization Sequence Confirmed</p>
                        
                        <div class="p-8 rounded-2xl bg-white/5 border border-white/10 border-dashed text-center flex flex-col items-center justify-center">
                            <div class="w-16 h-16 bg-primary/20 rounded-full flex items-center justify-center mb-4 ring-8 ring-primary/5 animate-pulse">
                                <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div class="text-[9px] font-mono text-zinc-400 uppercase tracking-[0.3em]">Neural Backbone Linked</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex gap-4">
                <button x-show="step > 1" @click="step--" class="flex-1 py-4 border border-white/10 rounded-2xl text-[9px] font-black uppercase tracking-widest hover:bg-white/5 transition-all italic">Back</button>
                <button x-show="step < 4" @click="step++" class="flex-[2] py-4 bg-white text-black rounded-2xl text-[9px] font-black uppercase tracking-widest hover:bg-primary transition-all italic">Next Phase</button>
                <button x-show="step === 4" @click="finish()" class="flex-[2] py-4 bg-primary text-black rounded-2xl text-[9px] font-black uppercase tracking-widest shadow-[0_0_30px_rgba(255,138,0,0.3)] transition-all italic">Launch Command</button>
            </div>
        </div>

        <!-- Right Side: Visual Data -->
        <div class="hidden lg:flex flex-1 relative bg-obsidian-950 items-center justify-center overflow-hidden">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,_var(--tw-gradient-stops))] from-primary/5 via-transparent to-transparent opacity-50"></div>
            <div class="absolute inset-0" style="background-image: radial-gradient(rgba(255,255,255,0.02) 1px, transparent 1px); background-size: 50px 50px;"></div>
            
            <div class="relative w-[600px] aspect-video glass-obsidian rounded-[3rem] border border-white/5 p-12 flex flex-col items-center justify-center text-center">
                <div x-show="step === 1" x-transition.scale.origin.bottom>
                    <div class="text-6xl mb-6">🌍</div>
                    <h3 class="text-xl font-black italic uppercase tracking-wider mb-2" x-text="form.company_name || 'IDENTIFYING ORG'"></h3>
                    <div class="text-[9px] font-mono text-primary uppercase tracking-[0.5em]" x-text="'LOCATING BASE: ' + form.region"></div>
                </div>
                <div x-show="step === 2" x-transition.scale.origin.bottom>
                    <div class="flex gap-4 mb-6">
                        <span class="text-4xl">🚛</span>
                        <span class="text-4xl animate-bounce">🏗️</span>
                        <span class="text-4xl">📦</span>
                    </div>
                    <h3 class="text-xl font-black italic uppercase tracking-wider mb-2">ASSET MAPPING</h3>
                    <div class="text-[9px] font-mono text-primary uppercase tracking-[0.5em]" x-text="'OPTIMIZING FOR ' + form.industry"></div>
                </div>
                <div x-show="step === 3" x-transition.scale.origin.bottom>
                    <div class="w-24 h-24 rounded-full border-4 border-primary border-t-transparent animate-spin mb-6 mx-auto"></div>
                    <h3 class="text-xl font-black italic uppercase tracking-wider mb-2">KPI ALIGNMENT</h3>
                    <div class="text-[9px] font-mono text-primary uppercase tracking-[0.5em]" x-text="'OBJECTIVE: ' + form.goal"></div>
                </div>
                <div x-show="step === 4" x-transition.scale.origin.bottom>
                    <div class="text-8xl font-black italic text-primary animate-pulse mb-4">100%</div>
                    <h3 class="text-xl font-black italic uppercase tracking-wider mb-2">SYSTEM ENGAGED</h3>
                    <div class="text-[9px] font-mono text-zinc-500 uppercase tracking-[0.5em]">SOVEREIGN CORE ONLINE</div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function onboardingWizard() {
            return {
                step: 1,
                form: {
                    company_name: '',
                    region: 'IN',
                    units: 'km',
                    scale: '1-10',
                    industry: 'logistics',
                    goal: 'tracking'
                },
                async finish() {
                    try {
                        const response = await fetch('{{ route("onboarding.store") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify(this.form)
                        });

                        if (response.ok) {
                            window.location.href = '{{ route("dashboard") }}';
                        }
                    } catch (error) {
                        console.error('Initialization failed:', error);
                    }
                }
            }
        }
    </script>
</body>
</html>
