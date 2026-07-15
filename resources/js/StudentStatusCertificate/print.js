import { CONFIG } from './config.js';
import { Toast } from './core.js';

/**
 * Replaces handlePreviewAction from the student CRUD module.
 * Fetches one student's record, builds the certificate HTML with their
 * data filled in, opens it in a dedicated window, and triggers print.
 */
export async function handlePrintAction(ApiService, id) {
    const { error, data } = await ApiService.request(`${CONFIG.API_BASE}/${id}`);
    if (error) {
        Toast.fire({ icon: 'error', title: 'មិនអាចទាញយកទិន្នន័យសម្រាប់វិញ្ញាបនបត្រ' });
        return;
    }

    const student = data.data || data;
    const printWindow = window.open('', '_blank', 'width=850,height=1200');
    if (!printWindow) {
        Toast.fire({ icon: 'warning', title: 'សូមអនុញ្ញាត popup ដើម្បីបោះពុម្ព (Please allow pop-ups to print)' });
        return;
    }

    printWindow.document.write(buildCertificateHtml(student));
    printWindow.document.close();

    printWindow.onload = () => {
        printWindow.focus();
        printWindow.print();
    };
}

/**
 * Maps a student API record to certificate display values with absolute text fallbacks.
 */
function extractCertificateData(student) {
    const person = student.person || {};
    const major = student.major || {};
    const address = Array.isArray(person.addresses) && person.addresses.length > 0 ? person.addresses[0] : null;

    const nameKh = person.first_name_kh || person.last_name_kh
        ? `${person.last_name_kh ?? ''} ${person.first_name_kh ?? ''}`.trim()
        : 'ចេង យូភីង';
    const nameEn = person.first_name || person.last_name
        ? `${person.last_name ?? ''} ${person.first_name ?? ''}`.trim().toUpperCase()
        : 'CHENG YOUPHING';

    const sexKh = person.sex === 'male' ? 'ប្រុស' : person.sex === 'female' ? 'ស្រី' : 'ស្រី';
    const sexEn = person.sex ? person.sex.charAt(0).toUpperCase() + person.sex.slice(1) : 'Female';

    return {
        regNumber: student.reg_number ?? '០៤៤៧/២៦ ស.វ/ក.ស',
        nameKh,
        nameEn,
        sexKh,
        sexEn,
        nationalityKh: person.nationality?.name_kh ?? 'ខ្មែរ',
        nationalityEn: person.nationality?.name.en ?? 'Khmer',
        dobKh: person.dob_kh ?? 'ថ្ងៃទី២៤ ខែសីហា ឆ្នាំ២០០៤',
        dobEn: person.dob_en ?? 'August 24, 2004',
        placeOfBirthKh: address?.province?.name_kh ?? 'រាជធានីភ្នំពេញ',
        placeOfBirthEn: address?.province?.name.en ?? 'Phnom Penh',
        majorKh: major.name_kh ?? 'គណនេយ្យ',
        majorEn: major.name.kh ?? 'Accounting',
        degreeKh: student.degree_kh ?? 'បរិញ្ញាបត្ររង',
        degreeEn: student.degree_en ?? 'Associate Degree',
        examDateKh: student.exit_exam_kh ?? 'ថ្ងៃទី២១ ខែកញ្ញា ឆ្នាំ២០២៤',
        examDateEn: student.exit_exam_en ?? 'September 21, 2024',
        lunarDateKh: student.lunar_date_kh ?? 'ថ្ងៃអង្គារ ៣កើត ខែជេស្ឋ ឆ្នាំមមី អដ្ឋស័ក ពុទ្ធសករាជ ២៥៧០',
        signatureDateKh: student.signature_date_kh ?? 'រាជធានីភ្នំពេញ ថ្ងៃទី១៩ ខែឧសភា ឆ្នាំ២០២៦',
        signatureDateEn: student.signature_date_en ?? 'May 19, 2026',
        presidentHonorificKh: student.president_honorific_kh ?? 'បណ្ឌិត',
        presidentNameKh: student.president_name_kh ?? 'កៀង រតនា'
    };
}

/**
 * Builds the full standalone certificate HTML string.
 */
function buildCertificateHtml(student) {
    const d = extractCertificateData(student);

    return `<!DOCTYPE html>
<html lang="km">
<head>
<meta charset="UTF-8">
<title>Provisional Certificate — ${d.nameEn}</title>
<style>

/* ==========================================================================
   1. PAGE SETUP
   ========================================================================== */
:root {
    --page-width: 21cm;
    --page-height: 29.7cm;
    --content-margin-left: 3cm;
    --content-margin-right: 3cm;
    --footer-bottom-margin: 5cm;
}

/* ==========================================================================
   2. FONT FAMILIES
   ========================================================================== */
:root {
    --font-times:      'Times New Roman', Times, serif;
    --font-moul:       'Khmer OS Muol Light', 'Khmer OS Moul Light', sans-serif;
    --font-battambang: 'Khmer OS Battambang', sans-serif;
    --font-old-english: 'Old English Text MT', 'OldEnglishTextMT', serif;
}

/* ==========================================================================
   3. VERTICAL POSITIONS & ALIGNMENTS
   ========================================================================== */
:root {
    --reg-line-top:        8cm;
    --title-top:           9cm;

    /* PUSHED DOWN BY 1.5cm TO CREATE GAP BELOW TITLE */
    --certify-line-top:    12.6cm;    /* Was 11.1cm */
    --info-first-row-top:  13.5cm;    /* Was 12.0cm */
    --info-row-gap:        0.85cm;
    --paragraph-top:       17.5cm;    /* Was 16.0cm */
    --photo-box-top:       23.0cm;    /* Was 21.5cm */
    --signature-block-top: 21.5cm;    /* Was 20.0cm */

    --footer-top: calc(var(--page-height) - var(--footer-bottom-margin));
}

/* ==========================================================================
   4. HORIZONTAL LAYOUT COLUMN WIDTHS
   ========================================================================== */
:root {
    --kh-label-left: var(--content-margin-left);
    --kh-value-left: 5.4cm;
    --en-label-left: 10.6cm;
    --en-value-left: 12.9cm;

    --statement-kh-width: 7.2cm;
    --statement-en-width: 7.3cm;
    --signature-block-width: 7.2cm;

    /* Exact 4x6 inch specifications */
    --photo-box-width:  10.16cm;
    --photo-box-height: 15.24cm;

    --info-value-kh-width: 4.9cm;
    --info-value-en-width: 4.8cm;
}

/* ==========================================================================
   5. TYPOGRAPHY SPECIFICATIONS
   ========================================================================== */
:root {
    --fs-reg-line:          9pt;
    --fs-cert-title-kh:     15pt;
    --fs-cert-title-en:     22pt;
    --fs-certify-line:      11pt;
    --fs-certify-kh:        10pt;

    /* Label Data sizing */
    --fs-info-label-kh:     9pt;
    --fs-info-label-en:     10pt;

    /* Value Data configurations */
    --fs-info-value-kh-moul: 9pt;
    --fs-info-value-kh-bat:  9pt;
    --fs-info-value-en:      10pt;

    /* Standalone Special Overrides */
    --fs-degree-kh:         10pt;
    --fs-major-kh:          10pt;
    --fs-degree-en:         10pt;
    --fs-major-en:          10pt;

    /* Dates and Signatures */
    --fs-lunar-date:           8.5pt;
    --fs-signature-date-kh:    8.5pt;
    --fs-signature-date-en:    10pt;
    --fs-president-title-kh:   10pt;
    --fs-president-title-en:   11pt;
    --fs-president-name:       11pt;

    /* Footer */
    --fs-footer-kh: 9pt;
    --fs-footer-en: 10pt;
}

/* ==========================================================================
   6. LINE HEIGHTS (MS Word 1.0 Single Line Spacing)
   ========================================================================== */
:root {
    --lh-single: 1.0;
    --president-name-gap: 1.8cm;
}

@page { size: A4 portrait; margin: 0; }
* { box-sizing: border-box; margin: 0; padding: 0; }
body { background: #fff; margin: 0; padding: 0; }

.certificate-page {
    position: relative;
    width: var(--page-width);
    height: var(--page-height);
    background: #fff;
    overflow: hidden;
}

.reg-line {
    position: absolute; top: var(--reg-line-top); left: var(--kh-label-left);
    font-family: var(--font-battambang); font-size: var(--fs-reg-line);
    line-height: var(--lh-single);
}

.title-block {
    position: absolute;
    top: var(--title-top);
    left: 0;
    width: 100%;
    text-align: center;

    /* FIX: Reduced line-height from 2.0 to 1.1 to collapse the text row gaps */
    line-height: 1.5;
}

.khmer-ornament {
    /* Keep it in normal document flow so it never overlaps the text */
    font-family: 'Tacteing', sans-serif;
    font-size: 30px;       /* Slightly reduced font size to save vertical space */
    line-height: 0.5;
    letter-spacing: 0px;
    user-select: none;

}
.cert-title-kh { font-family: var(--font-moul); font-size: var(--fs-cert-title-kh); }
.cert-title-en { font-family: var(--font-old-english); font-size: var(--fs-cert-title-en); font-weight:bold; }

.certify-line {
    position: absolute; top: var(--certify-line-top); left: var(--kh-label-left);
    width: calc(100% - var(--kh-label-left) * 2);
    line-height: var(--lh-single);
}
.certify-kh-text { font-family: var(--font-moul); font-size: var(--fs-certify-kh); font-weight: normal; }
.certify-en-text {
    position: absolute; left: calc(var(--en-label-left) - var(--kh-label-left));
    font-family: var(--font-times); font-size: var(--fs-certify-line); font-weight: bold;
}

/* Row-by-Row Layout elements */
.info-row { position: absolute; left: 0; width: 100%; line-height: var(--lh-single); }
.info-row .kh-label { position: absolute; left: var(--kh-label-left); font-family: var(--font-battambang); font-size: var(--fs-info-label-kh); }
.info-row .kh-value { position: absolute; left: var(--kh-value-left); width: var(--info-value-kh-width); font-family: var(--font-battambang); font-size: var(--fs-info-value-kh-bat); }

/* Value Specific Typography Tweaks */
.info-row .kh-value.moul-style { font-family: var(--font-moul); font-size: var(--fs-info-value-kh-moul); }
.info-row .en-label { position: absolute; left: var(--en-label-left); font-family: var(--font-times); font-size: var(--fs-info-label-en); font-weight: bold; }
.info-row .en-value { position: absolute; left: var(--en-value-left); width: var(--info-value-en-width); font-family: var(--font-times); font-size: var(--fs-info-value-en); }
.info-row .en-value.times-style { font-family: var(--font-times); text-transform: uppercase; font-weight: bold; }

.statement-block { position: absolute; top: var(--paragraph-top); width: 100%;}
.statement-kh {
    line-height: 1.8;
    position: absolute; left: var(--kh-label-left); width: var(--statement-kh-width);
    font-family: var(--font-battambang); font-size: var(--fs-info-value-kh-bat); text-align: left;
}
.statement-kh-degree-major { font-family: var(--font-moul); font-size: var(--fs-degree-kh); }
.statement-en {
    line-height: 1.7;
    position: absolute; left: var(--en-label-left); width: var(--statement-en-width);
    font-family: var(--font-times); font-size: var(--fs-info-value-en); text-align: left;
}
.statement-en-degree-major { font-family: var(--font-times); font-size: var(--fs-degree-en); font-weight: bold; }

.photo-box {
    position: absolute; top: var(--photo-box-top); left: var(--kh-label-left);
    width: var(--photo-box-width); height: var(--photo-box-height);
    border: 1px solid #000; font-family: var(--font-battambang); font-size: 9pt;
    display: flex; align-items: center; justify-content: center; line-height: var(--lh-single);
}

.signature-block {
    position: absolute; top: var(--signature-block-top); left: var(--en-label-left);
    width: var(--signature-block-width); text-align: center; line-height: 1.5;
}
.lunar-date-kh        { font-family: var(--font-battambang); font-size: var(--fs-lunar-date); margin-bottom: 2px; }
.signature-date-kh    { font-family: var(--font-battambang); font-size: var(--fs-signature-date-kh); }
.signature-date-en    { font-family: var(--font-times); font-size: var(--fs-signature-date-en); margin-bottom: 0.4cm; }
.president-title-kh   { font-family: var(--font-moul); font-size: var(--fs-president-title-kh); margin-top: 0.5cm; }
.president-title-en   { font-family: var(--font-times); font-size: var(--fs-president-title-en); font-weight: bold; }
.president-name        { font-size: var(--fs-president-name); margin-top: var(--president-name-gap); }
.president-honorific   { font-family: var(--font-battambang); }
.president-name-value  { font-family: var(--font-moul); }

.footer-note {
    position: absolute; top: var(--footer-top); left: var(--kh-label-left);
    font-family: var(--font-battambang); font-size: var(--fs-footer-kh); line-height: 1.5;
}
.footer-note .footer-en {
    font-family: var(--font-times); font-style: normal; font-size: var(--fs-footer-en);
}
@font-face {
  font-family: 'Tacteing';
  src: url('/fonts/Tacteing.woff2') format('woff2');
  font-weight: 400;
  font-style: normal;
  font-display: swap;
}

</style>
</head>
<body>
<div class="certificate-page">
    <div class="reg-line">លេខៈ${d.regNumber}</div>

    <div class="title-block mb-5">
        <div class="cert-title-kh">វិញ្ញាបនបត្របណ្ដោះអាសន្ន</div>
        <div class="cert-title-en">Provisional Certificate</div>
        <div class="khmer-ornament">3</div>
    </div>
    <div class="content-dynamic">
        <div class="certify-line">
            <span class="certify-kh-text">សាកលវិទ្យាល័យវេស្ទើន បញ្ជាក់ថា</span>
            <span class="certify-en-text">This is to certify that:</span>
        </div>

        <div class="info-row" style="top: var(--info-first-row-top);">
            <span class="kh-label">ឈ្មោះ:</span>
            <span class="kh-value moul-style">${d.nameKh}</span>
            <span class="en-label">Name:</span>
            <span class="en-value times-style">${d.nameEn}</span>
        </div>

        <div class="info-row" style="top: calc(var(--info-first-row-top) + var(--info-row-gap) * 1);">
            <span class="kh-label">ភេទ:</span>
            <span class="kh-value">${d.sexKh}</span>
            <span class="en-label">Sex:</span>
            <span class="en-value">${d.sexEn}</span>
        </div>

        <div class="info-row" style="top: calc(var(--info-first-row-top) + var(--info-row-gap) * 2);">
            <span class="kh-label">សញ្ជាតិ:</span>
            <span class="kh-value">${d.nationalityKh}</span>
            <span class="en-label">Nationality:</span>
            <span class="en-value">${d.nationalityEn}</span>
        </div>

        <div class="info-row" style="top: calc(var(--info-first-row-top) + var(--info-row-gap) * 3);">
            <span class="kh-label">ថ្ងៃខែឆ្នាំកំណើត:</span>
            <span class="kh-value">${d.dobKh}</span>
            <span class="en-label">Date of Birth:</span>
            <span class="en-value">${d.dobEn}</span>
        </div>

        <div class="info-row" style="top: calc(var(--info-first-row-top) + var(--info-row-gap) * 4);">
            <span class="kh-label">ទីកន្លែងកំណើត:</span>
            <span class="kh-value">${d.placeOfBirthKh}</span>
            <span class="en-label">Place of Birth:</span>
            <span class="en-value">${d.placeOfBirthEn}</span>
        </div>

        <div class="statement-block">
            <div class="statement-kh">
                បានបំពេញគ្រប់លក្ខខណ្ឌជាស្ថាពរ របស់សាកលវិទ្យាល័យ ដើម្បីទទួលសញ្ញាបត្រ
                <span class="statement-kh-degree-major">${d.degreeKh}
                </br>
                </span> ឯកទេស
                <span class="statement-kh-degree-major">${d.majorKh}</span>
                <br>
                វិញ្ញាបនបត្រនេះចេញឲ្យសាមីខ្លួនប្រើប្រាស់តាមការដែលអាចប្រើបាន។
                <br>
                សម័យប្រឡង ${d.examDateKh}
            </div>
            <div class="statement-en">
                has successfully fulfilled all the requirements of the university to be awarded the
                <span class="statement-en-degree-major">${d.degreeEn},</span> majoring in <span class="statement-en-degree-major">${d.majorEn}.</span>
                <br>
                This certificate is presented to the bearer with all rights and privileges thereto pertaining.
                <br>
                Date of comprehensive exam: ${d.examDateEn}
            </div>
        </div>


        <div class="signature-block">
            <div class="lunar-date-kh">${d.lunarDateKh}</div>
            <div class="signature-date-kh">${d.signatureDateKh}</div>
            <div class="signature-date-en">Phnom Penh, ${d.signatureDateEn}</div>
            <div class="president-title-kh">សាកលវិទ្យាធិការ</div>
            <div class="president-title-en">PRESIDENT</div>
            <div class="president-name"><span class="president-honorific">${d.presidentHonorificKh}</span> <span class="president-name-value">${d.presidentNameKh}</span></div>
        </div>

        <div class="footer-note">
            <span style="font-family:var(--font-moul);">កំណត់សំគាល់៖</span> វិញ្ញាបនបត្រនេះចេញជូនបានតែម្តងគត់។
            <br>
            <span class="footer-en">Note: This certificate is issued once only.</span>
        </div>
        </div>
    </div>
</body>
</html>
`;
}
