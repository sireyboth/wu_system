import { CONFIG } from './config.js';
import { state } from './state.js';
import { escapeHtml, Toast } from '../invigilator/core.js';
import { getRenderedRow } from './table-render.js';

const DOC_ICONS = {
    transcript: 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
    provisional: 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z',
    status: 'M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2',
};
const DEFAULT_ICON = 'M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z';

// "ព្រឹត្តិប័ត្រពិន្ទុ (Transcript)" → ["ព្រឹត្តិប័ត្រពិន្ទុ", "Transcript"]
function splitLabel(label) {
    const match = /^(.*?)\s*\((.*)\)$/.exec(label);
    return match ? [match[1], match[2]] : [label, ''];
}

function docTypeCard([value, label], checked, counts, blockers) {
    const [kh, en] = splitLabel(label);
    const count = counts[value] ?? 0;
    const blocked = blockers[value] ?? [];
    // Still selectable — the server refuses it with the same reasons.
    const reasons = blocked.length
        ? `<span class="block mt-1 text-[11px] leading-snug text-rose-600 dark:text-rose-400">${blocked.map(escapeHtml).join(' · ')}</span>`
        : '';
    const badge = blocked.length
        ? `<span class="shrink-0 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200 dark:bg-rose-500/10 dark:text-rose-400 dark:border-rose-500/20">Cannot print</span>`
        : count
        ? `<span class="shrink-0 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20">បោះពុម្ព ${count}×</span>`
        : `<span class="shrink-0 px-2 py-0.5 rounded-full text-[11px] font-medium bg-neutral-100 text-neutral-500 dark:bg-white/5 dark:text-neutral-400">មិនទាន់បោះពុម្ព</span>`;

    return `
        <label class="block cursor-pointer" data-doc-card>
            <input type="radio" name="docType" value="${escapeHtml(value)}" class="sr-only peer" ${checked ? 'checked' : ''}>
            <div class="flex items-center gap-3 p-3.5 rounded-xl border-2 border-neutral-200 dark:border-white/10 transition-all
                        hover:border-indigo-300 dark:hover:border-indigo-500/40
                        peer-checked:border-indigo-500 peer-checked:bg-indigo-50/60 dark:peer-checked:bg-indigo-500/10">
                <span class="flex items-center justify-center w-10 h-10 shrink-0 rounded-lg bg-indigo-100 text-indigo-600 dark:bg-indigo-500/15 dark:text-indigo-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="${DOC_ICONS[value] ?? DEFAULT_ICON}" />
                    </svg>
                </span>
                <span class="flex-1 min-w-0 text-left">
                    <span class="block text-sm font-bold text-neutral-900 dark:text-white">${escapeHtml(kh)}</span>
                    ${en ? `<span class="block text-xs text-neutral-500 dark:text-neutral-400">${escapeHtml(en)}</span>` : ''}
                    ${reasons}
                </span>
                ${badge}
            </div>
        </label>`;
}

/** Card picker for the document layout; resolves to the chosen doc_type, or undefined on cancel. */
async function chooseDocType(row, counts) {
    const student = [row?.name_kh, row?.student_code].filter(Boolean).map(escapeHtml).join(' · ');
    // Start on the document the student requested.
    const entries = Object.entries(state.docTypes);
    const selected = entries.some(([value]) => value === row?.request_type) ? row.request_type : entries[0]?.[0];

    const checkedType = () => Swal.getPopup().querySelector('input[name="docType"]:checked')?.value;

    const { value, isDenied } = await Swal.fire({
        title: 'ជ្រើសរើសឯកសារ',
        html: `
            <p class="mb-4 text-sm text-neutral-500 dark:text-neutral-400">
                Choose the document to preview or print${student ? `<br><span class="font-medium text-neutral-700 dark:text-neutral-200">${student}</span>` : ''}
            </p>
            <div class="space-y-2.5">
                ${entries.map((entry) => docTypeCard(entry, entry[0] === selected, counts, row?.print_blockers_by_type ?? {})).join('')}
            </div>`,
        width: 460,
        showCancelButton: true,
        showDenyButton: true,
        reverseButtons: true,
        buttonsStyling: false,
        confirmButtonText: 'បោះពុម្ព (Print)',
        denyButtonText: 'មើលជាមុន (Preview)',
        cancelButtonText: 'បោះបង់',
        customClass: {
            popup: '!rounded-2xl !p-0 overflow-hidden dark:!bg-neutral-900 border border-neutral-200 dark:border-white/10',
            title: '!pt-6 !px-6 !text-lg !font-bold text-neutral-900 dark:!text-white',
            htmlContainer: '!mx-6 !mt-1 !mb-0',
            actions: '!w-full !mt-6 !mb-0 !px-6 !py-4 !justify-end gap-2 border-t border-neutral-200 dark:border-white/10 bg-neutral-50 dark:bg-white/5',
            confirmButton: 'px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-all focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-neutral-900',
            cancelButton: 'px-4 py-2 text-sm font-medium rounded-lg text-neutral-600 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-white/10 transition-all focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500',
            denyButton: 'px-4 py-2 text-sm font-medium rounded-lg text-indigo-700 dark:text-indigo-300 bg-indigo-50 dark:bg-indigo-500/10 hover:bg-indigo-100 dark:hover:bg-indigo-500/20 transition-all focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500',
        },
        didOpen: (popup) => {
            // Double-click a card to preview — never prints (or counts) by accident.
            popup.querySelectorAll('[data-doc-card]').forEach((card) => card.addEventListener('dblclick', () => Swal.clickDeny()));
        },
        preConfirm: checkedType,
        preDeny: checkedType,
    });

    return value ? { type: value, preview: isDenied } : undefined;
}

/** Opens the layout in preview mode — not logged, and the page itself refuses to print. */
function previewDocument(row, type) {
    const url = new URL(row.preview_url, window.location.origin);
    url.searchParams.set('type', type);
    url.searchParams.set('preview', '1');
    window.open(url.toString(), '_blank');
}

/**
 * Print flow: pick the layout (Transcript / Provisional / …), then either
 * Preview it (nothing logged) or Print: confirm when that layout was printed
 * before, log the print, then open the page. The server logs it
 * (Api\TranscriptController::print) and re-checks the gates.
 */
export async function printDocument(ApiService, id, onDone) {
    const row = getRenderedRow(id);
    const counts = row?.print_counts ?? {};

    const choice = await chooseDocType(row, counts);
    if (!choice) return;
    if (choice.preview) return previewDocument(row, choice.type);
    const { type } = choice;

    const label = state.docTypes[type] ?? type;
    if (counts[type]) {
        const last = row.last_print;
        const confirmation = await Swal.fire({
            icon: 'warning',
            title: 'Already printed',
            html: `<b>${escapeHtml(label)}</b> has already been printed <b>${counts[type]}×</b>`
                + (last ? `<br><span class="text-sm">Last print: ${escapeHtml(state.docTypes[last.doc_type] ?? last.doc_type)} by ${escapeHtml(last.printed_by ?? '—')} · ${escapeHtml(last.printed_at ?? '')}</span>` : '')
                + '<br>Print it again?',
            showCancelButton: true,
            confirmButtonText: 'OK, print again',
            cancelButtonText: 'បោះបង់',
        });
        if (!confirmation.isConfirmed) return;
    }

    // Opened now, while the click still counts as a user gesture, so the popup isn't blocked.
    const tab = window.open('', '_blank');
    const { error, data } = await ApiService.request(`${CONFIG.TRANSCRIPTS_API}/${id}/prints`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ doc_type: type }),
    });
    if (error) {
        tab?.close();
        Toast.fire({ icon: 'error', title: data?.message || 'មិនអាចបោះពុម្ពបានទេ' });
        return;
    }

    if (tab) tab.location.href = data.print_url;
    else window.location.href = data.print_url;
    onDone();
}
