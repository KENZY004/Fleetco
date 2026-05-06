<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FleetCo | Drive Tomorrow Today</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { background-color: #020202; font-family: 'Inter', sans-serif; overflow-x: hidden; }
        .font-heading { font-family: 'Plus Jakarta Sans', sans-serif; }
        .hero-image { transform: scale(1.1); filter: brightness(0.6); }
        .speed-gauge { stroke-dasharray: 440; stroke-dashoffset: 440; }
        @media (max-width: 768px) {
            body { overflow-y: auto; }
        }
    </style>
</head>
<body class="text-white antialiased">

    <!-- Loading Cinematic Interface -->
    <div id="loader" class="fixed inset-0 z-[100] bg-[#020202] flex flex-col items-center justify-center">
        <div class="relative w-[280px] md:w-[500px] h-[180px] md:h-[300px] flex items-center justify-center">
            <!-- The Gauge System -->
            <svg class="w-full h-full" viewBox="0 0 200 120">
                <path d="M40 100 A 60 60 0 0 1 160 100" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="12" stroke-dasharray="2.5 1" />
                <path id="gauge-circle" d="M40 100 A 60 60 0 0 1 160 100" fill="none" stroke="#ff8a00" stroke-width="12" stroke-dasharray="2.5 1" class="speed-gauge" />
                <path d="M50 100 A 50 50 0 0 1 150 100" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="1" stroke-dasharray="1 4.5" />
                <g class="text-[5px] font-mono fill-zinc-600 font-bold">
                    <text x="35" y="103">1</text>
                    <text x="45" y="75">2</text>
                    <text x="70" y="55">3</text>
                    <text x="100" y="48" text-anchor="middle">4</text>
                    <text x="130" y="55">5</text>
                    <text x="155" y="75">6</text>
                    <text x="165" y="103">7</text>
                </g>
            </svg>

            <!-- Center Digital Speed -->
            <div class="absolute bottom-6 md:bottom-10 flex flex-col items-center">
                <div class="flex items-baseline gap-1">
                    <span id="speed-value" class="text-3xl md:text-6xl font-black italic tracking-tighter tabular-nums">00</span>
                    <span class="text-[7px] md:text-[10px] font-mono font-bold text-zinc-500 italic opacity-50">km/h</span>
                </div>
            </div>
        </div>

        <div class="mt-4 flex flex-col items-center px-6 text-center">
            <div id="status-text" class="text-[7px] md:text-[9px] font-mono uppercase tracking-[0.6em] text-zinc-700 mb-4 transition-colors">Establishing Secure Link</div>
            <div class="w-40 md:w-64 h-[1px] bg-white/5 relative overflow-hidden">
                <div id="loader-bar" class="absolute inset-0 bg-orange-500 w-0 shadow-[0_0_10px_#ff8a00]"></div>
            </div>
        </div>
    </div>

    <!-- Main Hero Cinematic -->
    <main id="hero" class="relative min-h-screen opacity-0 pointer-events-none flex flex-col">
        <div class="absolute inset-0 overflow-hidden fixed">
            <img src="/fleet_hub.png" alt="FleetCo Fleet Hub" class="hero-image object-cover w-full h-full">
            <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-black/20"></div>
        </div>

        <nav class="relative z-10 flex flex-col md:flex-row justify-between items-center py-6 md:py-10 px-6 md:px-12 gap-6 md:gap-0">
            <div class="flex items-center gap-4">
                <div class="w-10 h-[1px] bg-white/20"></div>
                <span class="text-[9px] uppercase font-black tracking-[0.5em] text-zinc-400">Hub_v.04</span>
            </div>
            <div class="hidden md:flex items-center gap-3">
                <div class="h-2 w-2 rounded-full bg-orange-500 shadow-[0_0_8px_#ff8a00]"></div>
                <span class="text-[10px] font-black uppercase tracking-[0.4em]">Live State</span>
            </div>
            <div class="flex items-center gap-3 md:gap-6 scale-90 md:scale-100">
                @auth
                    <a href="{{ route('dashboard') }}" class="group relative px-6 md:px-10 py-3 md:py-4 overflow-hidden rounded-full border border-white/10 active:scale-95 transition-all">
                        <div class="absolute inset-0 bg-white translate-y-full group-hover:translate-y-0 transition-transform duration-500"></div>
                        <span class="relative z-10 text-[8px] md:text-[9px] font-black uppercase tracking-[0.4em] text-white group-hover:text-black transition-colors duration-500">Enter Hub</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="text-[8px] md:text-[9px] font-black uppercase tracking-[0.4em] text-zinc-400 hover:text-white transition-colors">Login</a>
                    <a href="{{ route('register') }}" class="group relative px-6 md:px-8 py-3 overflow-hidden rounded-full border border-orange-500/30 active:scale-95 transition-all">
                        <div class="absolute inset-0 bg-orange-500 translate-y-full group-hover:translate-y-0 transition-transform duration-500"></div>
                        <span class="relative z-10 text-[8px] md:text-[9px] font-black uppercase tracking-[0.4em] text-white group-hover:text-white transition-colors duration-500">Register</span>
                    </a>
                @endauth
            </div>
        </nav>

        <div class="relative z-10 flex-1 flex flex-col items-center justify-center text-center px-6 py-20 md:py-0">
            <span id="label" class="text-[8px] md:text-[10px] uppercase tracking-[0.4em] text-orange-500 mb-6 md:mb-8 opacity-0 translate-y-4">real-time vehicle intelligence</span>
            <h1 id="title" class="font-heading text-4xl md:text-[8rem] font-extrabold tracking-tight leading-[0.9] mb-8 md:mb-12 opacity-0">
                FLEET <br>
                COMMAND
            </h1>
            <p id="desc" class="max-w-2xl text-[8px] md:text-xs text-zinc-400 font-bold uppercase tracking-[0.4em] md:tracking-[0.5em] leading-loose opacity-0 translate-y-4 mb-12 md:mb-16">
                Monitor every movement with precision. Our tracking system provides <br class="hidden md:block">the data you need for safety and operational efficiency.
            </p>
            
            <div id="cta" class="opacity-0 translate-y-8 flex items-center gap-8">
                <a href="{{ route('dashboard') }}" class="group relative inline-flex items-center gap-4 md:gap-8 px-8 md:px-16 py-4 md:py-8 bg-white text-black rounded-full overflow-hidden shadow-[0_20px_60px_rgba(255,255,255,0.1)] hover:shadow-[0_30px_100px_rgba(255,255,255,0.3)] transition-all">
                    <span class="text-[9px] md:text-[10px] font-black uppercase tracking-[0.6em] ml-2">Establish Link</span>
                    <div class="h-4 md:h-5 w-[1px] bg-black/10"></div>
                    <svg class="w-4 h-4 md:w-5 md:h-5 transition-transform group-hover:translate-x-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>
        </div>

        <!-- Float Info Matrix -->
        <div class="hidden md:grid absolute bottom-12 md:bottom-16 left-0 md:left-16 right-0 md:right-16 grid-cols-1 md:grid-cols-3 gap-12 md:gap-20 z-10 px-8 pb-20 md:pb-0 bg-black md:bg-transparent">
            <div class="float-card opacity-0 translate-x-4">
                <div class="text-[8px] md:text-[10px] font-black text-orange-500 uppercase tracking-[0.4em] mb-2 md:mb-4">Metric_01</div>
                <div class="text-3xl md:text-5xl font-black text-white italic tracking-tighter">99.9 <span class="text-[10px] md:text-xs text-zinc-600 font-bold ml-1 tracking-widest uppercase">%</span></div>
                <div class="text-[7px] md:text-[8px] font-mono text-zinc-600 uppercase tracking-[0.4em] mt-2 md:mt-3 italic">Real-time Location Accuracy</div>
            </div>
            <div class="float-card opacity-0 translate-x-4 md:border-l border-white/5 md:pl-20">
                <div class="text-[8px] md:text-[10px] font-black text-orange-500 uppercase tracking-[0.4em] mb-2 md:mb-4">Metric_02</div>
                <div class="text-3xl md:text-5xl font-black text-white italic tracking-tighter">SECURE</div>
                <div class="text-[7px] md:text-[8px] font-mono text-zinc-600 uppercase tracking-[0.4em] mt-2 md:mt-3 italic">Encrypted Data Transmission</div>
            </div>
            <div class="float-card opacity-0 translate-x-4 md:border-l border-white/5 md:pl-20 text-left md:text-right">
                <div class="text-[8px] md:text-[10px] font-black text-orange-500 uppercase tracking-[0.4em] mb-2 md:mb-4">Fleet_Status</div>
                <div class="text-3xl md:text-5xl font-black text-white italic tracking-tighter">ACTIVE</div>
                <div class="text-[7px] md:text-[8px] font-mono text-zinc-600 uppercase tracking-[0.4em] mt-2 md:mt-3 italic">Monitoring Global Assets</div>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof gsap === 'undefined') return;
            
            const tl = gsap.timeline();
            const gaugeLength = 188.5;

            gsap.set("#gauge-circle", { strokeDasharray: gaugeLength, strokeDashoffset: gaugeLength });

            tl.to("#gauge-circle", {
                strokeDashoffset: 0,
                duration: 4,
                ease: "power2.inOut"
            });

            tl.to("#speed-value", {
                innerText: 240,
                duration: 4,
                snap: { innerText: 1 },
                ease: "power2.inOut",
                onUpdate: function() {
                    const val = parseInt(this.targets()[0].innerText);
                    if(val > 100) document.getElementById('status-text').innerText = "Syncing Neural Link";
                    if(val > 200) {
                        document.getElementById('status-text').innerText = "Link Stable";
                        document.getElementById('status-text').style.color = "#ff8a00";
                    }
                }
            }, 0);

            tl.to("#loader-bar", {
                width: "100%",
                duration: 4,
                ease: "power2.inOut"
            }, 0);

            tl.to("#loader", {
                y: "-110%",
                duration: 1.5,
                ease: "expo.inOut",
                delay: 0.3
            });

            tl.to("#hero", {
                opacity: 1,
                duration: 0.1,
                pointerEvents: 'auto'
            }, "-=0.8");

            tl.to(".hero-image", {
                scale: 1,
                duration: 5,
                ease: "power2.out"
            }, "-=0.8");

            tl.to("#label", { opacity: 1, y: 0, duration: 1.5 }, "-=3.5");
            tl.to("#title", { opacity: 1, scale: 1, duration: 2, ease: "expo.out" }, "-=3");
            tl.to("#desc", { opacity: 1, y: 0, duration: 1.5 }, "-=2.5");
            tl.to("#cta", { opacity: 1, y: 0, duration: 1.2, ease: "back.out(1.5)" }, "-=2");
            tl.to(".float-card", { opacity: 1, x: 0, duration: 1.5, stagger: 0.3 }, "-=1.5");
        });
    </script>
</body>
</html>
