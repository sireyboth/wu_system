<style>
    @keyframes modalPop {
  0% {
    opacity: 0;
    transform: scale(0.85) translateY(20px);
  }
  70% {
    transform: scale(1.02) translateY(-2px);
  }
  100% {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
}

@keyframes hueRotate {
  0% { filter: blur(60px) hue-rotate(0deg); }
  50% { filter: blur(70px) hue-rotate(180deg); }
  100% { filter: blur(60px) hue-rotate(360deg); }
}

@keyframes pulseGlow {
  0%, 100% { opacity: 0.4; transform: scale(1); }
  50% { opacity: 0.8; transform: scale(1.08); }
}

.animate-modal-pop {
  animation: modalPop 0.45s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
}

.animate-hue {
  animation: hueRotate 12s linear infinite;
}

.animate-pulse-glow {
  animation: pulseGlow 3s ease-in-out infinite;
}

/* Dynamic conic ring transition */
#reportRing {
  transition: background 1.2s cubic-bezier(0.4, 0, 0.2, 1);
}
</style>

<div id="certificateReportModal"
    class="fixed inset-0 z-50 flex items-center justify-center px-4 py-6 bg-neutral-950/70 backdrop-blur-xl transition-all duration-300">

    <!-- Ambient Background Glow Effect -->
    <div class="absolute w-96 h-96 rounded-full bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 opacity-30 animate-hue pointer-events-none"></div>

    <div id="modalContainer"
        class="relative w-full max-w-xl overflow-hidden bg-white/90 dark:bg-neutral-900/90 backdrop-blur-2xl border border-white/20 dark:border-neutral-800 shadow-[0_25px_60px_-15px_rgba(0,0,0,0.5)] rounded-3xl animate-modal-pop">

        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-5 border-b border-neutral-200/50 dark:border-neutral-800/80 bg-neutral-50/40 dark:bg-neutral-900/40">
            <div class="flex items-center gap-3">
                <div class="relative flex items-center justify-center w-11 h-11 rounded-2xl bg-indigo-500/10 text-indigo-500 overflow-hidden group">
                    <div class="absolute inset-0 bg-indigo-500/20 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <svg class="w-6 h-6 transition-transform duration-300 group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-6h6v6m-9 4h12a2 2 0 002-2V5a2 2 0 00-2-2H6a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold leading-snug text-neutral-900 dark:text-white tracking-tight">របាយការណ៍សញ្ញាបត្រ</h2>
                    <p class="text-xs font-medium text-neutral-500 dark:text-neutral-400">Certificate Report Overview</p>
                </div>
            </div>

            <button type="button" id="closeCertificateReportBtn"
                class="group p-2 transition-all duration-200 text-neutral-400 hover:text-neutral-700 dark:hover:text-white rounded-xl hover:bg-neutral-200/50 dark:hover:bg-neutral-800 hover:rotate-90">
                <svg class="w-5 h-5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Main Content --}}
        <div class="p-6 space-y-6">

            {{-- Dynamic Progress Ring & Total Block --}}
            <div class="tilt-card relative flex items-center justify-between p-6 border rounded-2xl bg-gradient-to-br from-neutral-50 to-neutral-100/60 dark:from-neutral-900/60 dark:to-neutral-800/40 border-neutral-200/80 dark:border-neutral-800 shadow-inner overflow-hidden group">
                <div class="space-y-1 z-10">
                    <span class="text-xs font-bold tracking-wider text-neutral-400 uppercase">
                        សរុប (Total Certificates)
                    </span>
                    <div id="reportTotal" class="text-4xl font-black tracking-tight text-neutral-900 dark:text-white transition-all duration-300">
                        0
                    </div>
                </div>

                {{-- Conic Progress Donut Ring --}}
                <div id="reportRing"
                    class="relative flex items-center justify-center w-24 h-24 rounded-full shrink-0 shadow-lg transition-transform duration-500 group-hover:scale-105"
                    style="background: conic-gradient(#10b981 0deg, #374151 0deg);">
                    <div class="absolute flex flex-col items-center justify-center w-18 h-18 bg-white dark:bg-neutral-900 rounded-full shadow-inner">
                        <span id="reportPrintedPercent" class="text-sm font-extrabold text-neutral-900 dark:text-white">0%</span>
                        <span class="text-[9px] font-bold text-neutral-400 uppercase tracking-wider">Printed</span>
                    </div>
                </div>
            </div>

            {{-- Stat Tiles Grid --}}
            <div class="grid grid-cols-2 gap-4">

                {{-- Pending Tile --}}
                <div class="tilt-card group relative p-5 border rounded-2xl bg-amber-500/5 border-amber-500/20 hover:border-amber-500/40 transition-all duration-300 hover:shadow-lg hover:shadow-amber-500/10 overflow-hidden">
                    <div class="absolute -right-4 -top-4 w-16 h-16 bg-amber-500/10 rounded-full blur-xl group-hover:bg-amber-500/20 transition-all"></div>
                    <div class="flex items-center justify-between relative z-10">
                        <div>
                            <p class="text-xs font-bold text-amber-600 dark:text-amber-400">មិនទាន់ព្រីន</p>
                            <p class="text-[11px] text-amber-600/70 dark:text-amber-400/60 font-medium">Pending</p>
                        </div>
                        <div class="p-2.5 rounded-xl bg-amber-500/10 text-amber-500 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <p id="reportPending" class="mt-4 text-3xl font-black text-amber-700 dark:text-amber-300 relative z-10">0</p>
                </div>

                {{-- Printed Tile --}}
                <div class="tilt-card group relative p-5 border rounded-2xl bg-emerald-500/5 border-emerald-500/20 hover:border-emerald-500/40 transition-all duration-300 hover:shadow-lg hover:shadow-emerald-500/10 overflow-hidden">
                    <div class="absolute -right-4 -top-4 w-16 h-16 bg-emerald-500/10 rounded-full blur-xl group-hover:bg-emerald-500/20 transition-all"></div>
                    <div class="flex items-center justify-between relative z-10">
                        <div>
                            <p class="text-xs font-bold text-emerald-600 dark:text-emerald-400">បានព្រីនរួច</p>
                            <p class="text-[11px] text-emerald-600/70 dark:text-emerald-400/60 font-medium">Printed</p>
                        </div>
                        <div class="p-2.5 rounded-xl bg-emerald-500/10 text-emerald-500 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <p id="reportPrinted" class="mt-4 text-3xl font-black text-emerald-700 dark:text-emerald-300 relative z-10">0</p>
                </div>

                {{-- Other Status Tile --}}
                <div class="tilt-card col-span-2 group relative flex items-center justify-between p-5 border rounded-2xl bg-neutral-500/5 border-neutral-500/20 hover:border-neutral-500/40 transition-all duration-300">
                    <div class="flex items-center gap-3">
                        <div class="p-2.5 rounded-xl bg-neutral-500/10 text-neutral-400 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 12a0.75 0.75 0 11-1.5 0 011.5 0zm6 0a0.75 0.75 0 11-1.5 0 011.5 0zm6 0a0.75 0.75 0 11-1.5 0 011.5 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-neutral-700 dark:text-neutral-300">ផ្សេងៗ</p>
                            <p class="text-[11px] text-neutral-400 font-medium">Other statuses</p>
                        </div>
                    </div>
                    <p id="reportOther" class="text-2xl font-black text-neutral-900 dark:text-white">0</p>
                </div>

            </div>
        </div>

        {{-- Footer --}}
        <div class="flex items-center justify-end px-6 py-4 border-t border-neutral-200/50 dark:border-neutral-800/80 bg-neutral-50/40 dark:bg-neutral-900/40">
            <button type="button" id="closeCertificateReportBtn2"
                class="px-6 py-2.5 text-sm font-bold transition-all duration-200 rounded-xl text-neutral-600 dark:text-neutral-300 hover:bg-neutral-200/60 dark:hover:bg-neutral-800 active:scale-95">
                បិទ (Close)
            </button>
        </div>

    </div>
</div>


<script>
    // Animated Number Counter Function
function animateValue(elementId, start, end, duration) {
    const obj = document.getElementById(elementId);
    if (!obj) return;

    let startTimestamp = null;
    const step = (timestamp) => {
        if (!startTimestamp) startTimestamp = timestamp;
        const progress = Math.min((timestamp - startTimestamp) / duration, 1);
        // Ease Out Expo Easing Function
        const easedProgress = progress === 1 ? 1 : 1 - Math.pow(2, -10 * progress);
        const current = Math.floor(easedProgress * (end - start) + start);
        obj.innerHTML = current.toLocaleString();
        if (progress < 1) {
            window.requestAnimationFrame(step);
        }
    };
    window.requestAnimationFrame(step);
}

// Update Modal Data & Trigger Animations
function updateReportModalData(total, pending, printed, other) {
    // 1. Animate Numbers
    animateValue("reportTotal", 0, total, 1200);
    animateValue("reportPending", 0, pending, 1000);
    animateValue("reportPrinted", 0, printed, 1000);
    animateValue("reportOther", 0, other, 800);

    // 2. Animate Conic Progress Ring
    const percentage = total > 0 ? Math.round((printed / total) * 100) : 0;
    const ring = document.getElementById("reportRing");
    const percentLabel = document.getElementById("reportPrintedPercent");

    // Animate percentage label count
    animateValue("reportPrintedPercent", 0, percentage, 1200);
    setTimeout(() => {
        if (percentLabel) percentLabel.innerText = percentage + "%";
    }, 1200);

    // Dynamic gradient update
    if (ring) {
        const degrees = (percentage / 100) * 360;
        ring.style.background = `conic-gradient(#10b981 ${degrees}deg, #374151 0deg)`;
    }
}

// 3D Card Hover Motion Effect
document.querySelectorAll('.tilt-card').forEach(card => {
    card.addEventListener('mousemove', (e) => {
        const rect = card.getBoundingClientRect();
        const x = e.clientX - rect.left - rect.width / 2;
        const y = e.clientY - rect.top - rect.height / 2;
        card.style.transform = `perspective(1000px) rotateX(${-y / 20}deg) rotateY(${x / 20}deg) scale(1.01)`;
    });

    card.addEventListener('mouseleave', () => {
        card.style.transform = `perspective(1000px) rotateX(0deg) rotateY(0deg) scale(1)`;
    });
});
</script>
