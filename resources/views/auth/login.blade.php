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
            <img src="https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&q=80&w=2070" 
                 class="absolute inset-0 object-cover w-full h-full opacity-60 dark:opacity-40" alt="Hotel Interior">
            <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/20 to-transparent"></div>
            
            <div class="relative z-10 p-16 flex flex-col justify-between w-full">
                <div class="flex items-center gap-3">
                    <h2 class="text-white font-bold tracking-[0.2em] uppercase text-2xl border-l-4 border-indigo-500 pl-4">
                        Kep Sea View
                    </h2>
                </div>
                
                <div>
                    <h2 class="text-6xl font-black text-white leading-none tracking-tighter">
                        PROPERTY<br/><span class="text-indigo-400">MANAGEMENT</span>
                    </h2>
                    <p class="text-slate-300 mt-6 max-w-sm text-lg font-light leading-relaxed">
                        Elevating the guest experience through seamless digital hospitality.
                    </p>
                </div>

                <div class="text-slate-500 text-xs tracking-widest uppercase">
                    &copy; {{ date('Y') }} Internal System
                </div>
            </div>
        </div>

        <div class="flex-1 flex items-center justify-center p-8 sm:p-12 bg-white dark:bg-slate-900 transition-colors duration-500">
            <div class="w-full max-w-sm">
                <div class="mb-10">
                    <h1 class="text-4xl font-black text-slate-900 dark:text-white tracking-tight mb-2">Staff Login</h1>
                    <div class="h-1 w-12 bg-indigo-600 mb-4"></div>
                    <p class="text-slate-500 dark:text-slate-400 text-sm">Welcome back. Enter your credentials to access the dashboard.</p>
                </div>

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-[0.15em] text-slate-400 mb-2">Work Email</label>
                        <input id="email" type="email" name="email" :value="old('email')" required autofocus 
                            class="block w-full px-0 py-3 bg-transparent border-0 border-b-2 border-slate-200 dark:border-slate-700 focus:ring-0 focus:border-indigo-600 transition-all text-slate-900 dark:text-white placeholder-slate-300 dark:placeholder-slate-600" 
                            placeholder="username@hotel.com" />
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <label class="block text-[10px] font-bold uppercase tracking-[0.15em] text-slate-400">Security Key</label>
                            <a class="text-[10px] font-bold uppercase tracking-tighter text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300" href="{{ route('password.request') }}">Recover</a>
                        </div>
                        <input id="password" type="password" name="password" required 
                            class="block w-full px-0 py-3 bg-transparent border-0 border-b-2 border-slate-200 dark:border-slate-700 focus:ring-0 focus:border-indigo-600 transition-all text-slate-900 dark:text-white placeholder-slate-300 dark:placeholder-slate-600" 
                            placeholder="••••••••" />
                    </div>

                    <div class="flex items-center justify-between pt-2">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="remember" class="rounded border-slate-300 dark:border-slate-700 bg-transparent text-indigo-600 focus:ring-indigo-500 w-4 h-4">
                            <span class="ml-2 text-xs font-semibold text-slate-500 dark:text-slate-400">Keep me active</span>
                        </label>
                    </div>

                    <button type="submit" 
                        class="w-full bg-slate-900 dark:bg-indigo-600 hover:bg-indigo-700 dark:hover:bg-indigo-500 text-white font-bold py-4 rounded-lg shadow-xl shadow-slate-200 dark:shadow-none transition-all duration-300 active:scale-95 uppercase tracking-widest text-xs">
                        Authorize Entry
                    </button>

                    <div class="mt-8 pt-6 border-t border-slate-100 dark:border-slate-800 text-center">
                        <p class="text-sm text-slate-500 dark:text-slate-400">
                            New to the team? 
                            <a href="{{ route('register') }}" class="ml-1 font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                                Request Access
                            </a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>