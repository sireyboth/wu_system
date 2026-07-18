{{-- Sidebar --}}
<style>
    #main-content {
        transition: margin-left 300ms ease-in-out, padding 300ms ease-in-out;
        margin-left: 0;
        padding: 5rem 1.5rem 1.5rem 1.5rem; /* Large top padding on mobile for the header bar */
        width: 100%;
    }

    @media (min-width: 640px) {
        #main-content {
            padding-top: 1.5rem; /* Return to uniform padding on desktop viewports */
        }
        body.sidebar-open #main-content {
            margin-left: 16rem;
            padding-left: 2rem;
        }
        body.sidebar-closed #main-content {
            margin-left: 0;
            padding-left: 2rem;
        }
    }
</style>

<header id="mobile-top-bar" class="fixed top-0 left-0 right-0 z-30 flex items-center justify-between px-4 h-16 bg-white dark:bg-neutral-900 border-b border-neutral-200 dark:border-white/10 shadow-sm sm:hidden transition-colors duration-300">
    <div class="flex items-center space-x-3">
        <div class="w-8 h-8 bg-gradient-to-tr from-slate-900 to-indigo-950 dark:from-indigo-600 dark:to-violet-500 rounded-lg flex items-center justify-center text-white text-xs font-bold">
            R
        </div>
        <span class="text-md font-bold text-slate-900 dark:text-white">Registrar</span>
    </div>
    
    <button onclick="toggleSidebar()" type="button" class="p-2 rounded-xl text-neutral-500 hover:bg-neutral-100 dark:hover:bg-white/5 transition-colors focus:outline-none">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
    </button>
</header>

<aside id="sidebar-multi-level-sidebar"
    class="fixed top-0 left-0 z-40 w-64 h-screen -translate-x-full transition-transform duration-300 ease-in-out"
    aria-label="Sidebar">
    
    <div class="h-full px-4 py-6 flex flex-col justify-between bg-white dark:bg-neutral-900 border-e border-neutral-200 dark:border-white/10 shadow-2xl transition-colors duration-300 overflow-hidden">
        <div class="flex flex-col flex-1 min-h-0">
            <div class="flex items-center justify-between mb-6 pb-2 px-2 border-b border-transparent">
                <div class="flex items-center group cursor-pointer">
                    <div class="relative w-11 h-11 bg-gradient-to-tr from-slate-900 to-indigo-950 dark:from-indigo-600 dark:to-violet-500 rounded-xl flex items-center justify-center shadow-xl shadow-indigo-500/10 dark:shadow-indigo-500/20 overflow-hidden border border-white/10 transition-all duration-500 ease-out group-hover:scale-105 group-hover:shadow-indigo-500/30">
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full animate-[shimmer_2.5s_infinite] ease-in-out"></div>
                        <svg class="w-5 h-5 text-white transform transition-transform duration-500 ease-out group-hover:rotate-12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span class="absolute bottom-1.5 right-1.5 w-1.5 h-1.5 bg-emerald-400 rounded-full animate-ping opacity-75"></span>
                        <span class="absolute bottom-1.5 right-1.5 w-1.5 h-1.5 bg-emerald-400 rounded-full"></span>
                    </div>

                    <div class="flex flex-col ms-3.5 tracking-tight">
                        <div class="flex items-center space-x-1">
                            <span class="text-xl font-extrabold text-slate-900 dark:text-white bg-clip-text transition-colors duration-300">
                                Registrar
                            </span>
                        </div>
                        <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500 -mt-0.5">
                            Official System
                        </span>
                    </div>
                </div>

                <button onclick="toggleSidebar()" type="button"
                    class="p-2 rounded-lg text-neutral-400 hover:text-neutral-700 hover:bg-neutral-100 dark:hover:text-white dark:hover:bg-white/10 transition-all">
                    <span class="sr-only">Close sidebar</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                    </svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto custom-scrollbar pr-1">
                <ul class="space-y-2 font-medium">
                    <x-sidebar-link route="dashboard">
                        <x-slot name="icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                        </x-slot>
                        ទំព័រដើម
                    </x-sidebar-link>

                    <li class="pt-4 pb-1">
                        <span class="px-3 text-xs font-semibold text-neutral-400 dark:text-neutral-500 uppercase tracking-wider">Certificate</span>
                    </li>
                    <x-sidebar-link route="StudentStatusCertificate.index">
                        <x-slot name="icon">
                            <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd" d="M7 2a2 2 0 0 0-2 2v1a1 1 0 0 0 0 2v1a1 1 0 0 0 0 2v1a1 1 0 1 0 0 2v1a1 1 0 1 0 0 2v1a1 1 0 1 0 0 2v1a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H7Zm3 8a3 3 0 1 1 6 0 3 3 0 0 1-6 0Zm-1 7a3 3 0 0 1 3-3h2a3 3 0 0 1 3 3 1 1 0 0 1-1 1h-6a1 1 0 0 1-1-1Z" clip-rule="evenodd"/>
                            </svg>
                        </x-slot>
                        បណ្ដោះអាសន្ន (Probisional)
                    </x-sidebar-link>

                    <li class="pt-4 pb-1">
                        <span class="px-3 text-xs font-semibold text-neutral-400 dark:text-neutral-500 uppercase tracking-wider">Statistic</span>
                    </li>
                    <x-sidebar-link route="student.index">
                        <x-slot name="icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </x-slot>
                        និស្សិត (Student)
                    </x-sidebar-link>

                    <li class="pt-4 pb-1">
                        <span class="px-3 text-xs font-semibold text-neutral-400 dark:text-neutral-500 uppercase tracking-wider">Academics</span>
                    </li>

                    <x-sidebar-link route="faculty.index">
                        <x-slot name="icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </x-slot>
                        មហាវិទ្យាល័យ (Faculty)
                    </x-sidebar-link>

                    <x-sidebar-link route="major.index">
                        <x-slot name="icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </x-slot>
                        ជំនាញ (Major)
                    </x-sidebar-link>

                    <x-sidebar-link route="batch.index">
                        <x-slot name="icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                            </svg>
                        </x-slot>
                        ជំនាន់ (Batch)
                    </x-sidebar-link>

                    <x-sidebar-link route="shift.index">
                        <x-slot name="icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </x-slot>
                        វេន (Shift)
                    </x-sidebar-link>

                    <x-sidebar-link route="group.index">
                        <x-slot name="icon">
                            <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M4.5 17H4a1 1 0 0 1-1-1 3 3 0 0 1 3-3h1m0-3.05A2.5 2.5 0 1 1 9 5.5M19.5 17h.5a1 1 0 0 0 1-1 3 3 0 0 0-3-3h-1m0-3.05a2.5 2.5 0 1 0-2-4.45m.5 13.5h-7a1 1 0 0 1-1-1 3 3 0 0 1 3-3h3a3 3 0 0 1 3 3 1 1 0 0 1-1 1Zm-1-9.5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0Z"/>
                            </svg>
                        </x-slot>
                        ក្រុមសិក្សា (Group)
                    </x-sidebar-link>

                    <x-sidebar-link route="status.index">
                        <x-slot name="icon">
                            <svg class="w-[19px] h-[19px] text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
  <path stroke="currentColor" stroke-width="2" d="M11.083 5.104c.35-.8 1.485-.8 1.834 0l1.752 4.022a1 1 0 0 0 .84.597l4.463.342c.9.069 1.255 1.2.556 1.771l-3.33 2.723a1 1 0 0 0-.337 1.016l1.03 4.119c.214.858-.71 1.552-1.474 1.106l-3.913-2.281a1 1 0 0 0-1.008 0L7.583 20.8c-.764.446-1.688-.248-1.474-1.106l1.03-4.119A1 1 0 0 0 6.8 14.56l-3.33-2.723c-.698-.571-.342-1.702.557-1.771l4.462-.342a1 1 0 0 0 .84-.597l1.753-4.022Z"/>
</svg>


                        </x-slot>
                        ស្ថានភាព (Status)
                    </x-sidebar-link>
                </ul>
            </div>
        </div>

        <div class="pt-4 mt-2 border-t border-neutral-200 dark:border-white/10 space-y-4">
            <!-- Theme Toggle Button -->
            <button onclick="toggleDarkMode()"
                class="flex items-center justify-between w-full px-4 py-2.5 bg-neutral-100 dark:bg-white/5 hover:bg-neutral-200 dark:hover:bg-white/10 rounded-xl transition-all duration-300 group">
                <div class="flex items-center">
                    <!-- Moon Icon (Visible in Light Mode, hidden in Dark Mode) -->
                    <svg id="theme-icon-moon" class="w-5 h-5 text-indigo-600 dark:hidden transition-transform group-hover:-rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                    
                    <!-- Sun Icon (Hidden in Light Mode, visible in Dark Mode) -->
                    <svg id="theme-icon-sun" class="w-5 h-5 text-amber-500 hidden dark:block transition-transform group-hover:rotate-45" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-12.728l.707.707m12.728 12.728l.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
                    </svg>
                    
                    <span class="ms-3 text-sm font-medium text-neutral-700 dark:text-neutral-300">
                        <span class="dark:hidden">រចនាប័ទ្ម (Light)</span>
                        <span class="hidden dark:inline">រចនាប័ទ្ម (Dark)</span>
                    </span>
                </div>
                
                <!-- Status Dot indicator -->
                <span class="w-2.5 h-2.5 rounded-full bg-indigo-600 dark:bg-amber-500 shadow-sm"></span>
            </button>

            <form method="POST" action="{{ route('logout') }}" id="logout-form">
                @csrf
                <button type="submit"
                    class="flex items-center w-full px-4 py-2.5 text-sm font-medium text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-xl transition-all duration-200 group">
                    <svg class="w-5 h-5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span class="ms-3">ចាកចេញ (Logout)</span>
                </button>
            </form>
        </div>
    </div>
</aside>

<button id="sidebar-open-tab" onclick="toggleSidebar()" type="button"
    style="display: none;"
    class="hidden sm:flex fixed top-1/2 left-0 -translate-y-1/2 z-30 items-center justify-center w-6 h-14 bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-white/10 rounded-r-xl shadow-md text-neutral-500 hover:text-indigo-600 hover:w-7 transition-all">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
    </svg>
</button>

<script>
    const sidebarEl = document.getElementById('sidebar-multi-level-sidebar');
    const openTabEl = document.getElementById('sidebar-open-tab');
    const mobileTopBarEl = document.getElementById('mobile-top-bar');

    function applySidebarState(isOpen) {
        const isDesktop = window.matchMedia('(min-width: 640px)').matches;

        // Sync visibility helper modifiers
        sidebarEl.classList.toggle('-translate-x-full', !isOpen);
        document.body.classList.toggle('sidebar-open', isOpen);
        document.body.classList.toggle('sidebar-closed', !isOpen);
        
        if (isDesktop) {
            // Desktop Mode Styles Coordination
            openTabEl.style.display = isOpen ? 'none' : 'flex';
            if (mobileTopBarEl) mobileTopBarEl.style.display = 'none';
        } else {
            // Mobile Mode Styles Coordination
            openTabEl.style.display = 'none';
            if (mobileTopBarEl) mobileTopBarEl.style.display = isOpen ? 'none' : 'flex';
        }
    }

    function toggleSidebar() {
        const isCurrentlyOpen = !sidebarEl.classList.contains('-translate-x-full');
        const willOpen = !isCurrentlyOpen;
        applySidebarState(willOpen);
        localStorage.setItem('sidebarOpen', willOpen ? 'true' : 'false');
    }

    // Dynamic browser resize listener
    window.matchMedia('(min-width: 640px)').addEventListener('change', () => {
        const saved = localStorage.getItem('sidebarOpen');
        const isDesktop = window.matchMedia('(min-width: 640px)').matches;
        const shouldOpen = saved !== null ? saved === 'true' : isDesktop;
        applySidebarState(shouldOpen);
    });

    (function initSidebar() {
        const saved = localStorage.getItem('sidebarOpen');
        const isDesktop = window.matchMedia('(min-width: 640px)').matches;
        const shouldOpen = saved !== null ? saved === 'true' : isDesktop;
        applySidebarState(shouldOpen);
    })();

    // Close mobile overlay drawer when clicking outside the menu area
document.addEventListener('click', function (e) {
    // 1. STRICT CHECK: If the screen is desktop (>= 640px), immediately stop execution.
    // This ensures clicking outside NEVER hides or alters the sidebar layout on desktop views.
    const isDesktop = window.matchMedia('(min-width: 640px)').matches;
    if (isDesktop) return; 
    
    // 2. Mobile-Only execution path
    const isOpen = !sidebarEl.classList.contains('-translate-x-full');
    
    // If the sidebar drawer is active on mobile, and the user clicks outside both 
    // the sidebar container and the top header navbar, safely toggle it closed.
    if (isOpen && !sidebarEl.contains(e.target) && !mobileTopBarEl.contains(e.target)) {
        applySidebarState(false);
        localStorage.setItem('sidebarOpen', 'false');
    }
});

</script>