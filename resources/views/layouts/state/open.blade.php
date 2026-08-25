<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script>
        if (localStorage.theme === 'dark' ||
            (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:wght@300;400;500;600;700;800&family=Playfair+Display:wght@600;700&display=swap"
        rel="stylesheet">

    <title>@yield('title', 'Exam Portal')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @ddfsnStyles

    <style>
        :root {
            --gold: 38 92% 50%;
        }

        body {
            font-family: 'Kantumruy Pro', system-ui, sans-serif;
        }

        .font-display {
            font-family: 'Playfair Display', 'Kantumruy Pro', serif;
        }

        /* ---------- Formal background: infinite drifting curve pattern + soft glows ---------- */
        .state-bg {
            position: fixed;
            inset: 0;
            z-index: 0;
            overflow: hidden;
            background-color: #f8fafc;
        }

        html.dark .state-bg { background-color: #05070f; }

        .state-waves {
            position: absolute;
            inset: -20% -10%;
            background-repeat: repeat-x;
            background-size: 480px 100%;
            will-change: background-position;
        }

        .state-waves.layer-1 {
            top: 4%;
            height: 46%;
            opacity: .5;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 480 220'%3E%3Cpath d='M0 130 C 60 70, 120 190, 180 130 S 300 70, 360 130 S 480 190, 480 130' fill='none' stroke='%234f46e5' stroke-opacity='0.16' stroke-width='1.6'/%3E%3C/svg%3E");
            animation: waveDriftA 46s linear infinite;
        }

        .state-waves.layer-2 {
            top: 34%;
            height: 46%;
            opacity: .4;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 480 220'%3E%3Cpath d='M0 100 C 60 160, 120 40, 180 100 S 300 160, 360 100 S 480 40, 480 100' fill='none' stroke='%23f59e0b' stroke-opacity='0.14' stroke-width='1.4'/%3E%3C/svg%3E");
            animation: waveDriftB 60s linear infinite;
        }

        .state-waves.layer-3 {
            top: 62%;
            height: 46%;
            opacity: .3;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 480 220'%3E%3Cpath d='M0 130 C 60 70, 120 190, 180 130 S 300 70, 360 130 S 480 190, 480 130' fill='none' stroke='%236366f1' stroke-opacity='0.12' stroke-width='1.2'/%3E%3C/svg%3E");
            animation: waveDriftA 75s linear infinite reverse;
        }

        @keyframes waveDriftA {
            from { background-position-x: 0; }
            to   { background-position-x: -480px; }
        }

        @keyframes waveDriftB {
            from { background-position-x: 0; }
            to   { background-position-x: 480px; }
        }

        .state-glow {
            position: absolute;
            border-radius: 9999px;
            filter: blur(100px);
            opacity: .28;
            animation: driftGlow 24s ease-in-out infinite;
        }

        .state-glow.gold {
            width: 28rem; height: 28rem;
            top: -12%; right: -8%;
            background: radial-gradient(circle, hsla(var(--gold), .5), transparent 70%);
            animation-delay: -6s;
        }

        .state-glow.indigo {
            width: 32rem; height: 32rem;
            bottom: -16%; left: -10%;
            background: radial-gradient(circle, rgba(79,70,229,.45), transparent 70%);
        }

        @keyframes driftGlow {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(4%, 3%) scale(1.08); }
        }

        @media (prefers-reduced-motion: reduce) {
            .state-glow, .state-waves { animation: none; }
        }

        /* ---------- Entrance animations ---------- */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(14px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .fade-up {
            animation: fadeUp .6s cubic-bezier(.16,1,.3,1) both;
        }

        /* ---------- Print: card-only output ---------- */
        @media print {
            .no-print { display: none !important; }
            .state-bg { display: none !important; }
            body { background: #fff !important; }
        }

        /* ---------- Premium glass: pointer-tracked tilt + moving specular highlight ---------- */
        .glass-tilt {
            transform: perspective(1200px) rotateX(0deg) rotateY(0deg);
            transition: transform .5s cubic-bezier(.16,1,.3,1), box-shadow .5s cubic-bezier(.16,1,.3,1);
            transform-style: preserve-3d;
            will-change: transform;
        }

        /* JS sets --tiltX/--tiltY/--specX/--specY on mousemove; this is the resting state. */
        .glass-tilt {
            --specX: 30%;
            --specY: 20%;
        }

        .glass-specular {
            background: radial-gradient(circle at var(--specX, 30%) var(--specY, 20%), rgba(255,255,255,.55), transparent 42%);
            mix-blend-mode: overlay;
            opacity: .9;
        }

        html.dark .glass-specular {
            background: radial-gradient(circle at var(--specX, 30%) var(--specY, 20%), rgba(255,255,255,.22), transparent 42%);
        }

        /* Subtle material grain so the glass reads as a surface, not a flat gradient. */
        .glass-noise {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='120' height='120'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='2' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
            opacity: .05;
            mix-blend-mode: overlay;
        }

        html.dark .glass-noise { opacity: .08; }

        @media (prefers-reduced-motion: reduce) {
            .glass-tilt { transition: none; }
        }
    </style>
</head>

<body class="relative min-h-screen text-neutral-900 dark:text-white transition-colors">

    <div class="state-bg">
        <div class="state-waves layer-1"></div>
        <div class="state-waves layer-2"></div>
        <div class="state-waves layer-3"></div>
        <div class="state-glow gold"></div>
        <div class="state-glow indigo"></div>
    </div>

    <!-- Official header bar -->
    <header class="no-print relative z-10 border-b border-neutral-200/70 dark:border-white/10 bg-white/70 dark:bg-neutral-950/60 backdrop-blur-xl">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <!-- University logo — drop the real file at public/images/logo.png and this fills in automatically -->
                <div class="shrink-0 flex items-center justify-center w-10 h-10 rounded-xl bg-white/80 dark:bg-white/5 backdrop-blur-sm border border-neutral-200/80 dark:border-white/10 shadow-sm overflow-hidden p-1">
                    <img src="{{ asset('images/logo.png') }}" alt="Western University logo"
                         class="w-full h-full object-contain"
                         onerror="this.style.display='none'">
                </div>
                <div class="leading-tight">
                    <div class="text-sm font-bold tracking-wide">សាកលវិទ្យាល័យវេស្ទើន</div>
                    <div class="text-[11px] font-medium uppercase tracking-[0.14em] text-amber-600 dark:text-amber-400">Western University &middot;<br> Official Exam Portal</div>
                </div>
            </div>

            <button onclick="toggleDarkMode()" title="Toggle light / dark mode"
                class="flex items-center justify-center w-10 h-10 bg-white dark:bg-white/5 border border-neutral-200 dark:border-white/10 rounded-xl shadow-sm hover:shadow-md hover:border-indigo-300 dark:hover:border-indigo-500/40 transition-all">
                <svg class="hidden w-4.5 h-4.5 text-amber-500 dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-12.728l.707.707m12.728 12.728l.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
                </svg>
                <svg class="w-4.5 h-4.5 text-indigo-600 dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                </svg>
            </button>
        </div>
    </header>

    <main class="relative z-10 max-w-5xl mx-auto px-4 sm:px-6 py-10 sm:py-14">
        @yield('content')
    </main>

    <footer class="no-print relative z-10 max-w-5xl mx-auto px-4 sm:px-6 pb-10 text-center">
        <p class="text-[11px] tracking-wide text-neutral-400 dark:text-neutral-600">
            &copy; {{ date('Y') }} Western University &middot; Official Exam Portal
        </p>
    </footer>

    @stack('scripts')
</body>

</html>
