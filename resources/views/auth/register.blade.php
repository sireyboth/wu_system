<x-guest-layout>
    <div class="fixed inset-0 min-h-screen w-full flex flex-col md:flex-row bg-white dark:bg-slate-950 overflow-y-auto transition-colors duration-500">

        <button onclick="toggleDarkMode()" class="absolute top-6 right-6 z-50 p-3 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-yellow-400 shadow-lg hover:scale-110 transition-all">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 hidden dark:block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 18v1m9-9h1M4 12H3m15.364-6.364l.707-.707M6.343 17.657l-.707.707m12.728 0l.707-.707M6.343 6.343l-.707-.707m12.728 12.728L5.99 12.318a7 7 0 1112.022-4.122z" />
            </svg>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 block dark:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
            </svg>
        </button>

        <div class="hidden md:flex md:w-1/2 bg-slate-900 relative overflow-hidden">
            <img src="https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?auto=format&fit=crop&q=80&w=2070"
                 class="absolute inset-0 object-cover w-full h-full opacity-60 dark:opacity-40" alt="Hotel Exterior">
            <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/20 to-transparent"></div>

            <div class="relative z-10 p-16 flex flex-col justify-between w-full">
                <div class="flex items-center gap-3">
                    <h2 class="text-white font-bold tracking-[0.2em] uppercase text-2xl border-l-4 border-indigo-500 pl-4">
                        Kep Sea View
                    </h2>
                </div>

                <div>
                    <h2 class="text-6xl font-black text-white leading-none tracking-tighter">
                        JOIN THE<br/><span class="text-indigo-400">NETWORK</span>
                    </h2>
                    <p class="text-slate-300 mt-6 max-w-sm text-lg font-light leading-relaxed">
                        Start managing world-class properties with our integrated staff portal.
                    </p>
                </div>

                <div class="text-slate-500 text-xs tracking-widest uppercase">
                    &copy; {{ date('Y') }} Internal System
                </div>
            </div>
        </div>

        <div class="flex-1 flex items-center justify-center p-8 sm:p-12 bg-white dark:bg-slate-900 transition-colors duration-500">
            <div class="w-full max-w-sm">
                <div class="mb-8">
                    <h1 class="text-4xl font-black text-slate-900 dark:text-white tracking-tight mb-2">Staff Registry</h1>
                    <div class="h-1 w-12 bg-indigo-600 mb-4"></div>
                    <p class="text-slate-500 dark:text-slate-400 text-sm">Create your professional account to begin.</p>
                </div>

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-[0.15em] text-slate-400 mb-1">Full Name</label>
                        <input id="name" type="text" name="name" :value="old('name')" required autofocus
                            class="block w-full px-0 py-2 bg-transparent border-0 border-b-2 border-slate-200 dark:border-slate-700 focus:ring-0 focus:border-indigo-600 transition-all text-slate-900 dark:text-white placeholder-slate-300 dark:placeholder-slate-600"
                            placeholder="John Doe" />
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-[0.15em] text-slate-400 mb-1">Work Email</label>
                        <input id="email" type="email" name="email" :value="old('email')" required
                            class="block w-full px-0 py-2 bg-transparent border-0 border-b-2 border-slate-200 dark:border-slate-700 focus:ring-0 focus:border-indigo-600 transition-all text-slate-900 dark:text-white placeholder-slate-300 dark:placeholder-slate-600"
                            placeholder="username@hotel.com" />
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-[0.15em] text-slate-400 mb-1">Create Password</label>
                        <input id="password" type="password" name="password" required
                            class="block w-full px-0 py-2 bg-transparent border-0 border-b-2 border-slate-200 dark:border-slate-700 focus:ring-0 focus:border-indigo-600 transition-all text-slate-900 dark:text-white placeholder-slate-300 dark:placeholder-slate-600"
                            placeholder="••••••••" />
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-[0.15em] text-slate-400 mb-1">Confirm Password</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required
                            class="block w-full px-0 py-2 bg-transparent border-0 border-b-2 border-slate-200 dark:border-slate-700 focus:ring-0 focus:border-indigo-600 transition-all text-slate-900 dark:text-white placeholder-slate-300 dark:placeholder-slate-600"
                            placeholder="••••••••" />
                    </div>

                    <div class="pt-4">
                        <button type="submit"
                            class="w-full bg-slate-900 dark:bg-indigo-600 hover:bg-indigo-700 dark:hover:bg-indigo-500 text-white font-bold py-4 rounded-lg shadow-xl shadow-slate-200 dark:shadow-none transition-all duration-300 active:scale-95 uppercase tracking-widest text-xs">
                            Create Account
                        </button>
                    </div>

                    <div class="mt-8 pt-6 border-t border-slate-100 dark:border-slate-800 text-center">
                        <p class="text-sm text-slate-500 dark:text-slate-400">
                            Already registered?
                            <a href="{{ route('login') }}" class="ml-1 font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                                Sign In
                            </a>
                        </p>
                    </div>
                </form>
            </div>

            <!-- NODE 2: STRATEGIC INSCRIPTION CARD -->
            <div class="bento-card bento-brand-panel">
                <div class="brand-meta">Security Protocol</div>
                <div class="brand-quote">
                    Every immutable academic ledger entry <em>originates from a verified identity</em>.
                </div>
                <div class="brand-meta" style="color: rgba(255,255,255,0.45);">Office of the Registrar</div>
            </div>

            <!-- NODE 3: GRAPHICAL PENDING SECURITY INSIGNIA -->
            <div class="bento-card bento-seal-panel">
                <div class="animated-seal-container">
                    <svg viewBox="0 0 110 110" fill="none">
                        <circle cx="55" cy="55" r="48" stroke="var(--rg-badge-text)" stroke-width="1.2" stroke-dasharray="4 6" class="dash-ring"/>
                        <circle cx="55" cy="55" r="36" fill="var(--rg-form-bg-dim)" stroke="var(--rg-form-border)" stroke-width="1"/>
                        <path d="M42 62 C48 46, 62 40, 74 36" stroke="var(--rg-badge-text)" stroke-width="1.8" stroke-linecap="round" fill="none"/>
                        <circle cx="74" cy="36" r="2" fill="var(--rg-badge-text)"/>
                        <text x="55" y="76" text-anchor="middle" fill="var(--rg-badge-text)" font-family="IBM Plex Mono, monospace" font-size="5" letter-spacing="1">VERIFYING</text>
                    </svg>
                </div>
                <div style="font-family: 'IBM Plex Mono', monospace; font-size: 0.6rem; letter-spacing: 0.12em; color: var(--rg-form-subtext); margin-top: 1.2rem; text-align: center; text-transform: uppercase;">
                    Awaiting Review
                </div>
            </div>

        </div>
    </div>

<script>
    // Configuration for a sleek "Toast" notification
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });

    // Check for Laravel Success Session
    @if(session('success') || session('status'))
        Toast.fire({
            icon: 'success',
            title: "{{ session('success') ?? session('status') }}",
            background: document.documentElement.classList.contains('dark') ? '#0f172a' : '#fff',
            color: document.documentElement.classList.contains('dark') ? '#fff' : '#000'
        });
    @endif

    // Check for Laravel Validation Errors
    @if($errors->any())
        Toast.fire({
            icon: 'error',
            title: 'Registration Failed',
            text: "{{ $errors->first() }}",
            background: document.documentElement.classList.contains('dark') ? '#0f172a' : '#fff',
            color: document.documentElement.classList.contains('dark') ? '#fff' : '#000'
        });
    @endif
</script>
