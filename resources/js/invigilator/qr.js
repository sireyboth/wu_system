import QRCode from 'qrcode';
import { state, escapeHtml, Toast } from './core.js';
import { getRenderedRow } from './table-render.js';

/**
 * The QR encodes the row's public_url (a random token, never the id), so
 * a printed card keeps working after the invigilator is edited.
 */
export async function openQr(dom, id) {
    const row = getRenderedRow(id);
    if (!row?.public_url) return;

    state.qrRow = row;
    try {
        state.qrRow.qrDataUrl = await QRCode.toDataURL(row.public_url, { width: 480, margin: 1 });
    } catch (err) {
        console.error(err);
        Toast.fire({ icon: 'error', title: 'មិនអាចបង្កើត QR បានទេ (Could not create QR)' });
        return;
    }

    dom.qrImage.src = row.qrDataUrl;
    if (dom.qrPhoto) {
        if (row.photo_url) dom.qrPhoto.src = row.photo_url;
        dom.qrPhoto.classList.toggle('hidden', !row.photo_url);
    }
    dom.qrNameKh.textContent = row.name_kh ?? '';
    dom.qrNameEn.textContent = row.name_en ?? '';
    dom.qrCode.textContent = `ID: ${row.code ?? ''}${row.batch ? ` · ${row.batch}` : ''}`;
    dom.qrLink.href = row.public_url;
    dom.qrLink.textContent = row.public_url;

    window.InvigilatorQrModal.toggle(true);
}

export function downloadQr() {
    const row = state.qrRow;
    if (!row?.qrDataUrl) return;

    const a = document.createElement('a');
    a.href = row.qrDataUrl;
    a.download = `invigilator-${(row.code || row.id).toString().replace(/[^\w-]+/g, '_')}-qr.png`;
    a.click();
}

/** Opens a bare print window holding just the ID card (CR80 card size). */
export function printCard() {
    const row = state.qrRow;
    if (!row?.qrDataUrl) return;

    const win = window.open('', '_blank', 'width=480,height=640');
    if (!win) {
        Toast.fire({ icon: 'warning', title: 'Please allow pop-ups to print the card.' });
        return;
    }

    win.document.write(`<!doctype html><html><head><meta charset="utf-8">
        <title>${escapeHtml(row.name_en)} — Invigilator Card</title>
        <style>
            @page { size: 54mm 85.6mm; margin: 0; }
            * { box-sizing: border-box; }
            body { margin: 0; font-family: 'Khmer OS Battambang', 'Noto Sans Khmer', system-ui, sans-serif; }
            .card { width: 54mm; height: 85.6mm; padding: 5mm 4mm; display: flex; flex-direction: column;
                    align-items: center; justify-content: space-between; text-align: center; border: 0.3mm solid #e5e5e5; }
            .role { font-size: 7pt; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; color: #4f46e5; }
            .kh { font-size: 11pt; font-weight: 700; margin-top: 1mm; }
            .en { font-size: 8.5pt; color: #404040; }
            .qr { width: 30mm; height: 30mm; }
            .photo { width: 22mm; height: 26mm; object-fit: cover; border-radius: 2mm; margin: 1.5mm auto 0; display: block; }
            .meta { font-size: 7.5pt; color: #525252; font-family: ui-monospace, monospace; }
        </style></head><body>
        <div class="card">
            <div>
                <div class="role">អ្នកឃ្លាំមើល · Invigilator</div>
                ${row.photo_url ? `<img class="photo" src="${escapeHtml(row.photo_url)}" alt="">` : ''}
                <div class="kh">${escapeHtml(row.name_kh)}</div>
                <div class="en">${escapeHtml(row.name_en)}</div>
            </div>
            <img class="qr" src="${row.qrDataUrl}" alt="QR">
            <div class="meta">ID: ${escapeHtml(row.code)}${row.batch ? ` · ${escapeHtml(row.batch)}` : ''}</div>
        </div>
        <script>window.onload = () => { window.print(); };<\/script>
        </body></html>`);
    win.document.close();
}
