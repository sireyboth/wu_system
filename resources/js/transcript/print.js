import { CONFIG } from './config.js';
import { state } from './state.js';
import { escapeHtml, Toast } from '../invigilator/core.js';
import { getRenderedRow } from './table-render.js';

/**
 * Print flow: pick the layout (Transcript / Provisional / …), confirm when
 * that layout was printed before, log the print, then open the page. The
 * server logs it (Api\TranscriptController::print) and re-checks the gates.
 */
export async function printDocument(ApiService, id, onDone) {
    const row = getRenderedRow(id);
    const counts = row?.print_counts ?? {};

    const { value: type } = await Swal.fire({
        title: 'ជ្រើសរើសឯកសារ (Choose document)',
        input: 'select',
        inputOptions: Object.fromEntries(
            Object.entries(state.docTypes).map(([value, label]) => [value, counts[value] ? `${label} — printed ${counts[value]}×` : label]),
        ),
        showCancelButton: true,
        confirmButtonText: 'បោះពុម្ព (Print)',
        cancelButtonText: 'បោះបង់',
    });
    if (!type) return;

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
