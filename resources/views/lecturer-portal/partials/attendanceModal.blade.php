<div id="attendanceModal"
     class="fixed inset-0 z-50 invisible opacity-0 bg-gradient-to-b from-neutral-50 to-white dark:from-neutral-950 dark:to-neutral-900 transition-opacity duration-300 items-center justify-center">

    <div id="attendanceModalCard" class="w-full h-full flex flex-col opacity-0 transition-opacity duration-300">

        <!-- Header: university identity + session context -->
        <div class="px-8 py-4 border-b border-neutral-200/70 dark:border-white/5 flex items-center justify-between shrink-0 bg-white/70 dark:bg-neutral-900/70 backdrop-blur-sm">
            <div class="flex items-center gap-4">
                <div class="w-11 h-11 rounded-xl bg-white dark:bg-white/5 border border-neutral-200/80 dark:border-white/10 shadow-sm p-1.5 shrink-0">
                    <img src="{{ asset('images/logo.png') }}" alt="Western University" class="w-full h-full object-contain">
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-indigo-600 dark:text-indigo-400">Western University &middot; Attendance Session</p>
                    <h3 class="text-xl font-bold text-neutral-900 dark:text-white leading-tight">Class <span id="attendanceClassCode" class="font-mono text-indigo-600 dark:text-indigo-400"></span></h3>
                </div>
            </div>
            <div class="flex items-center gap-5">
                <div id="attendanceClock" class="hidden sm:block text-right">
                    <p id="attendanceClockTime" class="text-lg font-mono font-bold text-neutral-700 dark:text-neutral-200 tabular-nums"></p>
                    <p id="attendanceClockDate" class="text-[11px] text-neutral-400 uppercase tracking-wide"></p>
                </div>
                <button type="button" onclick="AttendanceModal.toggle(false)" class="p-2 text-neutral-400 hover:text-neutral-600 dark:hover:text-white transition-colors rounded-lg hover:bg-neutral-100 dark:hover:bg-white/5">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        <div class="flex-1 min-h-0 overflow-y-auto px-8 py-8">
            <div id="attendanceNoSession" class="text-center py-20">
                <p class="text-lg text-neutral-500 dark:text-neutral-400 mb-6">No attendance session for today yet.</p>
                <button id="attendanceStartBtn" type="button"
                    class="inline-flex items-center px-8 py-4 text-lg font-bold text-white bg-indigo-600 rounded-2xl hover:bg-indigo-700 shadow-lg shadow-indigo-500/30 transition-all active:scale-95">
                    Start Attendance
                </button>
            </div>

            <div id="attendanceLive" class="hidden h-full max-w-7xl mx-auto">
                <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,440px)_minmax(0,1fr)] gap-10 h-full items-start">

                    <!-- Formal QR frame -->
                    <div class="lg:sticky lg:top-0">
                        <div class="relative rounded-[28px] bg-gradient-to-br from-indigo-600 to-indigo-800 p-[2px] shadow-2xl shadow-indigo-500/20">
                            <div class="rounded-[26px] bg-white dark:bg-neutral-900 px-8 py-8 text-center">
                                <p class="text-[11px] font-bold uppercase tracking-[0.25em] text-indigo-500 mb-5">Scan to Check In</p>

                                <div class="relative inline-block">
                                    <!-- viewfinder corner marks -->
                                    <span class="absolute -top-2 -left-2 w-6 h-6 border-t-[3px] border-l-[3px] border-indigo-500 rounded-tl-lg"></span>
                                    <span class="absolute -top-2 -right-2 w-6 h-6 border-t-[3px] border-r-[3px] border-indigo-500 rounded-tr-lg"></span>
                                    <span class="absolute -bottom-2 -left-2 w-6 h-6 border-b-[3px] border-l-[3px] border-indigo-500 rounded-bl-lg"></span>
                                    <span class="absolute -bottom-2 -right-2 w-6 h-6 border-b-[3px] border-r-[3px] border-indigo-500 rounded-br-lg"></span>

                                    <div id="attendanceQrWrap" class="p-3 bg-white rounded-2xl border border-neutral-100">
                                        <img id="attendanceQrImg" class="w-full max-w-[360px] aspect-square" alt="Attendance QR code">
                                    </div>
                                </div>

                                <p class="text-xs text-neutral-400 mt-5">Refreshes automatically every few seconds</p>
                                <div class="mt-3 inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200/70 dark:border-emerald-500/20">
                                    <span id="attendanceStatusDot" class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                    <p id="attendanceSessionStatus" class="text-xs font-bold uppercase tracking-wide text-emerald-700 dark:text-emerald-400"></p>
                                </div>

                                <div class="mt-5 pt-4 border-t border-neutral-100 dark:border-white/5">
                                    <a id="attendanceTestLink" href="#" target="_blank" rel="noopener"
                                        class="inline-flex items-center gap-1.5 text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                                        Open scan page directly
                                    </a>
                                    <p class="text-[11px] text-neutral-400 mt-1">For testing on this device, without a second phone</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Roster panel -->
                    <div class="min-w-0">
                        <div class="flex items-center justify-between mb-5 gap-4">
                            <div>
                                <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-neutral-400">Live Roster</p>
                                <span class="text-2xl font-extrabold text-neutral-900 dark:text-white" id="attendanceCount"></span>
                            </div>
                            <button id="attendanceSubmitBtn" type="button"
                                class="px-5 py-3 text-sm font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl shrink-0 shadow-sm shadow-rose-500/20 transition-all active:scale-95">
                                End &amp; Lock Session
                            </button>
                            <button id="attendanceStartNextBtn" type="button" class="hidden px-5 py-3 text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shrink-0 shadow-sm shadow-indigo-500/20 transition-all active:scale-95">
                                Start Session 2
                            </button>
                        </div>
                        <div class="bg-white dark:bg-neutral-900 divide-y divide-neutral-100 dark:divide-white/5 border border-neutral-200/80 dark:border-white/10 rounded-2xl shadow-sm overflow-hidden">
                            <div id="attendanceRosterList"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Compact Roumdoul attribution footer -->
        <div class="shrink-0 px-8 py-3 border-t border-neutral-200/70 dark:border-white/5 flex items-center justify-center gap-2.5 bg-white/60 dark:bg-neutral-900/60">
            <img src="{{ asset('images/Roumdoul_Logo.png') }}" alt="Roumdoul" class="w-4 h-4 object-contain opacity-80">
            <p class="text-[10px] text-neutral-400 dark:text-neutral-500 tracking-wide">
                &copy; {{ date('Y') }} Western University &middot; System by <span class="font-semibold text-pink-800/80 dark:text-pink-400/80">Roumdoul</span>
            </p>
        </div>
    </div>
</div>
