<style>
    @keyframes shimmer {
        100% {
            transform: translateX(100%);
        }
    }

    /* Target the container scrollbar for an elegant look */
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background-color: rgba(156, 163, 175, 0.2); /* Low opacity gray */
        border-radius: 20px;
    }
    .dark .custom-scrollbar::-webkit-scrollbar-thumb {
        background-color: rgba(255, 255, 255, 0.1); /* Low opacity white for dark mode */
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background-color: rgba(156, 163, 175, 0.4);
    }
</style>

<button data-drawer-target="sidebar-multi-level-sidebar" data-drawer-toggle="sidebar-multi-level-sidebar"
    aria-controls="sidebar-multi-level-sidebar" type="button"
    class="inline-flex items-center p-2 mt-3 ms-3 text-sm text-neutral-500 rounded-lg sm:hidden hover:bg-neutral-100 dark:text-neutral-400 dark:hover:bg-neutral-800 transition-all">
    <span class="sr-only">Open sidebar</span>
    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
        <path clip-rule="evenodd" fill-rule="evenodd"
            d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z">
        </path>
    </svg>
</button>

<aside id="sidebar-multi-level-sidebar"
    class="fixed top-0 left-0 z-40 w-64 h-screen transition-transform -translate-x-full sm:translate-x-0"
    aria-label="Sidebar">
    <div
        class="h-full px-4 py-6 flex flex-col justify-between bg-white dark:bg-neutral-900 border-e border-neutral-200 dark:border-white/10 shadow-2xl transition-colors duration-300 overflow-hidden">

        <div class="flex flex-col flex-1 min-h-0">

            <div class="flex items-center mb-6 pb-2 px-2 group cursor-pointer border-b border-transparent dark:border-transparent">
                <div
                    class="relative w-11 h-11 bg-gradient-to-tr from-slate-900 to-indigo-950 dark:from-indigo-600 dark:to-violet-500 rounded-xl flex items-center justify-center shadow-xl shadow-indigo-500/10 dark:shadow-indigo-500/20 overflow-hidden border border-white/10 transition-all duration-500 ease-out group-hover:scale-105 group-hover:shadow-indigo-500/30">

                    <div
                        class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full animate-[shimmer_2.5s_infinite] ease-in-out">
                    </div>

                    <svg class="w-5 h-5 text-white transform transition-transform duration-500 ease-out group-hover:rotate-12"
                        fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>

                    <span
                        class="absolute bottom-1.5 right-1.5 w-1.5 h-1.5 bg-emerald-400 rounded-full animate-ping opacity-75"></span>
                    <span class="absolute bottom-1.5 right-1.5 w-1.5 h-1.5 bg-emerald-400 rounded-full"></span>
                </div>

                <div class="flex flex-col ms-3.5 tracking-tight">
                    <div class="flex items-center space-x-1">
                        <span
                            class="text-xl font-extrabold text-slate-900 dark:text-white bg-clip-text transition-colors duration-300">
                            Registrar
                        </span>
                        <span
                            class="w-1 h-1 rounded-full bg-indigo-600 dark:bg-indigo-400 transform scale-0 transition-transform duration-300 group-hover:scale-100"></span>
                    </div>
                    <span
                        class="text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500 -mt-0.5">
                        Official System
                    </span>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto custom-scrollbar pr-1">
                <ul class="space-y-2 font-medium">
                    <x-sidebar-link route="dashboard">
                        <x-slot name="icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                        </x-slot>
                        ទំព័រដើម
                    </x-sidebar-link>

                    <li class="pt-4 pb-1">
                        <span
                            class="px-3 text-xs font-semibold text-neutral-400 dark:text-neutral-500 uppercase tracking-wider">Statistic</span>
                    </li>
                    <x-sidebar-link route="student.index">
                        <x-slot name="icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </x-slot>
                        និស្សិត (Student)
                    </x-sidebar-link>

                    <li class="pt-4 pb-1">
                        <span
                            class="px-3 text-xs font-semibold text-neutral-400 dark:text-neutral-500 uppercase tracking-wider">Academics</span>
                    </li>

                    <x-sidebar-link route="faculty.index">
                        <x-slot name="icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </x-slot>
                        មហាវិទ្យាល័យ (Faculty)
                    </x-sidebar-link>

                    <x-sidebar-link route="major.index">
                        <x-slot name="icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </x-slot>
                        ជំនាញ (Major)
                    </x-sidebar-link>

                    <x-sidebar-link route="batch.index">
                        <x-slot name="icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                            </svg>
                        </x-slot>
                        ជំនាន់ (Batch)
                    </x-sidebar-link>

                    <x-sidebar-link route="shift.index">
                        <x-slot name="icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </x-slot>
                        វេន (Shift)
                    </x-sidebar-link>

                    <x-sidebar-link route="group.index">
                        <x-slot name="icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </x-slot>
                        ក្រុមសិក្សា (Group)
                    </x-sidebar-link>

                    <x-sidebar-link route="campus.index">
                        <x-slot name="icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </x-slot>
                        សាខា (Campus)
                    </x-sidebar-link>

                    <li class="pt-4 pb-1">
                        <span
                            class="px-3 text-xs font-semibold text-neutral-400 dark:text-neutral-500 uppercase tracking-wider">Staff</span>
                    </li>

                    <x-sidebar-link route="lecturer.index">
                        <x-slot name="icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </x-slot>
                        សាស្ដ្រាចារ្យ
                    </x-sidebar-link>
                </ul>
            </div>
        </div>

        <div class="pt-4 mt-2 border-t border-neutral-200 dark:border-white/10 space-y-4">
            <button onclick="toggleDarkMode()"
                class="flex items-center justify-between w-full px-4 py-2.5 bg-neutral-100 dark:bg-white/5 hover:bg-neutral-200 dark:hover:bg-white/10 rounded-xl transition-all duration-300 group">
                <div class="flex items-center">
                    <svg id="theme-icon-moon"
                        class="w-5 h-5 text-indigo-600 dark:hidden transition-transform group-hover:-rotate-12"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path
                            d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                    <svg id="theme-icon-sun" class="hidden w-5 h-5 text-yellow-400 dark:block animate-spin-slow"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path
                            d="M12 3v1m0 16v1m9-9h-1M4 9H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span class="ms-3 text-sm font-medium text-neutral-700 dark:text-neutral-300">Theme</span>
                </div>
                <div class="w-8 h-4 bg-neutral-300 dark:bg-indigo-600 rounded-full relative transition-colors">
                    <div
                        class="absolute top-0.5 left-0.5 dark:translate-x-4 w-3 h-3 bg-white rounded-full transition-transform duration-200">
                    </div>
                </div>
            </button>

            <form method="POST" action="{{ route('logout') }}" id="logout-form">
                @csrf
                <button type="submit"
                    class="flex items-center w-full px-4 py-2.5 text-sm font-medium text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-xl transition-all duration-200 group">
                    <svg class="w-5 h-5 transition-transform group-hover:translate-x-1" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span class="ms-3">ចាកចេញ (Logout)</span>
                </button>
            </form>

            <div
                class="flex items-center px-2 py-1 bg-neutral-50/50 dark:bg-white/5 rounded-2xl p-2 border border-neutral-100 dark:border-white/5">
                <img class="w-10 h-10 rounded-xl object-cover shadow-sm"
                    src="https://ui-avatars.com/api/?name=Alex+Rivera&background=6366f1&color=fff" alt="User">
                <div class="ms-3 flex-1 overflow-hidden">
                    <p class="text-sm font-bold text-neutral-900 dark:text-white truncate">Alex Rivera</p>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 truncate">Administrator</p>
                </div>
            </div>
        </div>
    </div>
</aside>

<script>
    function toggleDarkMode() {
        const html = document.documentElement;
        const isDark = html.classList.toggle('dark');
        localStorage.setItem('theme', isDark ? 'dark' : 'light');
    }

    if (localStorage.getItem('theme') === 'dark' ||
        (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
</script>
