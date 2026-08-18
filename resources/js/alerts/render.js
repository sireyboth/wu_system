import { escapeHtml } from './core.js';

const TIER_STYLES = {
    high:   { border: 'border-t-rose-500', badge: 'bg-rose-50 text-rose-700 dark:bg-rose-500/10 dark:text-rose-400' },
    medium: { border: 'border-t-amber-500', badge: 'bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400' },
    later:  { border: 'border-t-indigo-400', badge: 'bg-indigo-50 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-400' },
    neutral:{ border: 'border-t-neutral-300 dark:border-t-white/10', badge: 'bg-neutral-100 text-neutral-600 dark:bg-white/5 dark:text-neutral-300' },
};

function formatDateTime(value) {
    if (!value) return '—';
    const d = new Date(value.replace(' ', 'T'));
    if (Number.isNaN(d.getTime())) return value;
    return d.toLocaleString('en-GB', { day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit' });
}

function dueLabel(alert) {
    if (alert.is_overdue) return { text: 'ហួសកំណត់ (Overdue)', cls: 'bg-rose-600 text-white' };
    const n = alert.days_until_start;
    if (n <= 0) return { text: 'ថ្ងៃនេះ (Today)', cls: 'bg-rose-500 text-white' };
    if (n === 1) return { text: 'ស្អែក (Tomorrow)', cls: 'bg-amber-500 text-white' };
    return { text: `ក្នុង ${n} ថ្ងៃ (in ${n} days)`, cls: 'bg-neutral-500 text-white' };
}

export function alertCard(alert, tier = 'neutral') {
    const style = TIER_STYLES[tier] ?? TIER_STYLES.neutral;
    const due = dueLabel(alert);
    const isCompleted = alert.status === 'completed';

    return `
        <div class="fade-up group relative flex flex-col bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-white/10 border-t-4 ${style.border} rounded-2xl shadow-sm hover:shadow-lg transition-all duration-300" data-alert-id="${alert.id}">
            <div class="p-4 flex-1 min-w-0">
                <div class="flex items-start justify-between gap-2 mb-2">
                    <div class="flex flex-wrap items-center gap-1.5">
                        ${alert.category ? `<span class="inline-flex items-center px-2 py-0.5 text-[10px] font-bold rounded-full ${style.badge}">${escapeHtml(alert.category)}</span>` : ''}
                        ${!isCompleted ? `<span class="inline-flex items-center px-2 py-0.5 text-[10px] font-bold rounded-full ${due.cls}">${due.text}</span>` : `<span class="inline-flex items-center px-2 py-0.5 text-[10px] font-bold rounded-full bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400">រួចរាល់ (Done)</span>`}
                        ${alert.is_snoozed ? `<span class="inline-flex items-center px-2 py-0.5 text-[10px] font-bold rounded-full bg-violet-50 text-violet-700 dark:bg-violet-500/10 dark:text-violet-400">Snoozed</span>` : ''}
                    </div>
                </div>

                <h3 class="font-bold text-sm text-neutral-900 dark:text-white leading-snug truncate">${escapeHtml(alert.title)}</h3>
                ${alert.sub_title ? `<p class="text-xs text-neutral-500 dark:text-neutral-400 truncate">${escapeHtml(alert.sub_title)}</p>` : ''}
                <p class="mt-2 text-xs text-neutral-500 dark:text-neutral-400 line-clamp-2">${escapeHtml(alert.content)}</p>

                <div class="mt-3 pt-3 border-t border-neutral-100 dark:border-white/5 grid grid-cols-2 gap-2 text-[11px]">
                    <div>
                        <div class="font-bold uppercase tracking-wide text-neutral-400 dark:text-neutral-500">Start</div>
                        <div class="text-neutral-700 dark:text-neutral-200 font-semibold">${formatDateTime(alert.start_date)}</div>
                    </div>
                    <div>
                        <div class="font-bold uppercase tracking-wide text-neutral-400 dark:text-neutral-500">End</div>
                        <div class="text-neutral-700 dark:text-neutral-200 font-semibold">${formatDateTime(alert.end_date)}</div>
                    </div>
                </div>

                <div class="mt-2 flex items-center gap-2 text-neutral-400 dark:text-neutral-500">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" title="Notifies via Telegram"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" /></svg>
                    ${alert.remind_enabled ? `<span class="text-[10px] font-semibold">↻ every ${alert.remind_interval_minutes}m</span>` : ''}
                    ${alert.repeat_type !== 'none' ? `<span class="text-[10px] font-semibold capitalize">⟳ ${alert.repeat_type}</span>` : ''}
                </div>
            </div>

            <div class="flex items-center gap-1 p-2 border-t border-neutral-100 dark:border-white/5 bg-neutral-50/50 dark:bg-white/[0.02] rounded-b-2xl">
                ${!isCompleted ? `
                <button data-action="complete" data-id="${alert.id}" title="Mark complete"
                    class="flex-1 inline-flex items-center justify-center gap-1 px-2 py-1.5 text-[11px] font-bold text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-500/10 rounded-lg hover:bg-emerald-100 dark:hover:bg-emerald-500/20 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                    Done
                </button>
                <div class="relative flex-1 snooze-wrap">
                    <button type="button" data-toggle-snooze data-id="${alert.id}" title="Snooze"
                        class="w-full inline-flex items-center justify-center gap-1 px-2 py-1.5 text-[11px] font-bold text-violet-700 dark:text-violet-400 bg-violet-50 dark:bg-violet-500/10 rounded-lg hover:bg-violet-100 dark:hover:bg-violet-500/20 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Snooze
                    </button>
                    <div data-snooze-menu class="hidden absolute z-20 bottom-full mb-1 left-0 w-32 bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-white/10 rounded-lg shadow-lg overflow-hidden max-h-64 overflow-y-auto">
                        <button type="button" data-snooze="5" data-id="${alert.id}" class="block w-full text-left px-3 py-2 text-xs hover:bg-neutral-100 dark:hover:bg-white/5">5 min</button>
                        <button type="button" data-snooze="10" data-id="${alert.id}" class="block w-full text-left px-3 py-2 text-xs hover:bg-neutral-100 dark:hover:bg-white/5">10 min</button>
                        <button type="button" data-snooze="15" data-id="${alert.id}" class="block w-full text-left px-3 py-2 text-xs hover:bg-neutral-100 dark:hover:bg-white/5">15 min</button>
                        <button type="button" data-snooze="30" data-id="${alert.id}" class="block w-full text-left px-3 py-2 text-xs hover:bg-neutral-100 dark:hover:bg-white/5">30 min</button>
                        <div class="border-t border-neutral-100 dark:border-white/5"></div>
                        <button type="button" data-snooze="60" data-id="${alert.id}" class="block w-full text-left px-3 py-2 text-xs hover:bg-neutral-100 dark:hover:bg-white/5">1 hour</button>
                        <button type="button" data-snooze="180" data-id="${alert.id}" class="block w-full text-left px-3 py-2 text-xs hover:bg-neutral-100 dark:hover:bg-white/5">3 hours</button>
                        <button type="button" data-snooze="360" data-id="${alert.id}" class="block w-full text-left px-3 py-2 text-xs hover:bg-neutral-100 dark:hover:bg-white/5">6 hours</button>
                        <button type="button" data-snooze="1440" data-id="${alert.id}" class="block w-full text-left px-3 py-2 text-xs hover:bg-neutral-100 dark:hover:bg-white/5">Tomorrow</button>
                    </div>
                </div>` : ''}
                <button data-action="edit" data-id="${alert.id}" title="Edit"
                    class="p-1.5 text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-500/10 rounded-lg transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </button>
                <button data-action="delete" data-id="${alert.id}" title="Delete"
                    class="p-1.5 text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-500/10 rounded-lg transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </div>
        </div>`;
}

export function emptySection(text) {
    return `<div class="col-span-full py-8 text-center text-xs text-neutral-400 dark:text-neutral-500 border border-dashed border-neutral-200 dark:border-white/10 rounded-2xl">${text}</div>`;
}
