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

    <title>@yield('title', 'Exam Attendance')</title>
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
    <header class="relative z-10 border-b border-neutral-200/70 dark:border-white/10 bg-white/70 dark:bg-neutral-950/60 backdrop-blur-xl">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-600 to-indigo-800 text-white shadow-lg shadow-indigo-500/30 shrink-0">
                      <!-- University logo — drop the real file at public/images/logo.png and this fills in automatically -->
                <div class="shrink-0 flex items-center justify-center w-10 h-10 rounded-xl bg-white/80 dark:bg-white/5 backdrop-blur-sm border border-neutral-200/80 dark:border-white/10 shadow-sm overflow-hidden p-1">
                    <img src="{{ asset('images/logo.png') }}" alt="Western University logo"
                         class="w-full h-full object-contain"
                         onerror="this.style.display='none'">
                </div>
                </div>
                <div class="leading-tight">
                    <div class="text-sm font-bold tracking-wide">សាកលវិទ្យាល័យវេស្ទើន</div>
                    <div class="text-[11px] font-medium uppercase tracking-[0.14em] text-amber-600 dark:text-amber-400">Western University &middot; Official Exam Portal</div>
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

    <main id="geoContent" class="relative z-10 max-w-5xl mx-auto px-4 sm:px-6 py-10 sm:py-14 hidden">
        @yield('content')
    </main>

    <footer id="geoFooter" class="relative z-10 max-w-5xl mx-auto px-4 sm:px-6 pb-10 text-center hidden">
        <p class="text-[11px] tracking-wide text-neutral-400 dark:text-neutral-600">
            &copy; {{ date('Y') }} Western University &middot; Official Exam Attendance Portal &middot; Authorized on-site staff use only
        </p>
    </footer>

    <!-- Location gate — this page only unlocks on campus -->
    <div id="geoGate" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-neutral-50 dark:bg-neutral-950">
        <div class="w-full max-w-sm text-center">
            <div id="geoIconChecking" class="mx-auto mb-5 flex items-center justify-center w-16 h-16 rounded-2xl bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400">
                <svg class="w-7 h-7 animate-spin" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                </svg>
            </div>
            <div id="geoIconBlocked" class="hidden mx-auto mb-5 flex items-center justify-center w-16 h-16 rounded-2xl bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                </svg>
            </div>
            <h2 id="geoTitle" class="text-lg font-bold text-neutral-900 dark:text-white">កំពុងផ្ទៀងផ្ទាត់ទីតាំង</h2>
            <p id="geoMessage" class="mt-2 text-sm text-neutral-500 dark:text-neutral-400">
                កំពុងពិនិត្យមើលថាតើអ្នកកំពុងនៅលើបរិវេណសាកលវិទ្យាល័យ...<br>
                <span class="text-xs">Verifying that you're on campus...</span>
            </p>
            <button id="geoRetryBtn" type="button" onclick="window.__geoCheck()"
                class="hidden mt-5 inline-flex items-center gap-2 px-5 py-2.5 text-sm font-bold text-white bg-indigo-600 rounded-xl shadow-lg shadow-indigo-500/25 hover:bg-indigo-700 active:scale-95 transition-all">
                ព្យាយាមម្តងទៀត (Try Again)
            </button>
        </div>
    </div>

    <script>
        (function () {


            // // Western University Main Campus — https://maps.app.goo.gl/RmmQBV3c3v4PcBGZ8
            // var TARGET_LAT = 11.5715553;
            // var TARGET_LNG = 104.8892426;
            // var ALLOWED_RADIUS_METERS = 300;

            // Cambodia (approx. country center) — allow access from anywhere in the country
            var TARGET_LAT = 12.5657;
            var TARGET_LNG = 104.9910;
            var ALLOWED_RADIUS_METERS = 350000;

            var gate = document.getElementById('geoGate');
            var content = document.getElementById('geoContent');
            var footer = document.getElementById('geoFooter');
            var iconChecking = document.getElementById('geoIconChecking');
            var iconBlocked = document.getElementById('geoIconBlocked');
            var title = document.getElementById('geoTitle');
            var message = document.getElementById('geoMessage');
            var retryBtn = document.getElementById('geoRetryBtn');

            function showChecking() {
                iconChecking.classList.remove('hidden');
                iconBlocked.classList.add('hidden');
                retryBtn.classList.add('hidden');
                title.textContent = 'កំពុងផ្ទៀងផ្ទាត់ទីតាំង';
                message.innerHTML = 'កំពុងពិនិត្យមើលថាតើអ្នកកំពុងនៅលើបរិវេណសាកលវិទ្យាល័យ...<br><span class="text-xs">Verifying that you\'re on campus...</span>';
            }

            function showBlocked(titleText, messageHtml) {
                iconChecking.classList.add('hidden');
                iconBlocked.classList.remove('hidden');
                retryBtn.classList.remove('hidden');
                title.textContent = titleText;
                message.innerHTML = messageHtml;
            }

            function unlock() {
                gate.classList.add('hidden');
                content.classList.remove('hidden');
                footer.classList.remove('hidden');
            }

            // Haversine distance in meters
            function distanceMeters(lat1, lng1, lat2, lng2) {
                var R = 6371000;
                var dLat = (lat2 - lat1) * Math.PI / 180;
                var dLng = (lng2 - lng1) * Math.PI / 180;
                var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                    Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                    Math.sin(dLng / 2) * Math.sin(dLng / 2);
                return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            }

            window.__geoCheck = function () {
                showChecking();

                if (!('geolocation' in navigator)) {
                    showBlocked(
                        'មិនអាចផ្ទៀងផ្ទាត់ទីតាំងបានទេ',
                        'កម្មវិធីរុករករបស់អ្នកមិនគាំទ្រការកំណត់ទីតាំងទេ។<br><span class="text-xs">Your browser doesn\'t support location services.</span>'
                    );
                    return;
                }

                navigator.geolocation.getCurrentPosition(
                    function (position) {
                        var dist = distanceMeters(
                            position.coords.latitude,
                            position.coords.longitude,
                            TARGET_LAT,
                            TARGET_LNG
                        );

                        if (dist <= ALLOWED_RADIUS_METERS) {
                            unlock();
                        } else {
                            var km = (dist / 1000).toFixed(1);
                            showBlocked(
                                // 'ទំព័រនេះសម្រាប់តែបុគ្គលិកនៅបរិវេណប៉ុណ្ណោះ',
                                // 'អ្នកនៅចម្ងាយប្រហែល ' + km + ' គីឡូម៉ែត្រ ពីសាកលវិទ្យាល័យវេស្ទើន។<br>' +
                                // '<span class="text-xs">This page is only available on the Western University campus. You are about ' + km + ' km away.</span>'
                                'ទំព័រនេះសម្រាប់តែអ្នកប្រើប្រាស់នៅកម្ពុជាប៉ុណ្ណោះ',
                                'អ្នកនៅចម្ងាយប្រហែល ' + km + ' គីឡូម៉ែត្រ ពីកម្ពុជា។<br>' +
                                '<span class="text-xs">This page is only available within Cambodia. You are about ' + km + ' km away.</span>'
                            );
                        }
                    },
                    function (error) {
                        if (error.code === error.PERMISSION_DENIED) {
                            showBlocked(
                                'ត្រូវការការអនុញ្ញាតទីតាំង',
                                'សូមអនុញ្ញាតការចូលប្រើទីតាំង ដើម្បីបន្ត។<br><span class="text-xs">Please allow location access in your browser to continue.</span>'
                            );
                        } else {
                            showBlocked(
                                'មិនអាចទាញយកទីតាំងបានទេ',
                                'សូមព្យាយាមម្តងទៀត។<br><span class="text-xs">Could not determine your location. Please try again.</span>'
                            );
                        }
                    },
                    { enableHighAccuracy: true, timeout: 10000, maximumAge: 60000 }
                );
            };

            window.__geoCheck();
        })();
    </script>

    @stack('scripts')
</body>

</html>
