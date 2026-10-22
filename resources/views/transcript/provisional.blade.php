@php
    use App\Helpers\Degree;
    use App\Helpers\Khmer;

    $t = $transcript;
    $degree = Degree::tryFrom((string) $t->degree);
    $degreeEn = $degree ? ucwords($degree->labelEn()) : ucfirst((string) $t->degree);
    $degreeKh = $degree?->labelKh() ?? '';
    $majorEn = $t->major?->name_en;
    $majorKh = $t->major?->name_kh ?: $majorEn;
    $fullName = trim("{$t->surname} {$t->name}");
@endphp
<!DOCTYPE html>
<html lang="km">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Provisional Certificate {{ $t->issue_no }} — {{ $fullName }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Battambang:wght@400;700&family=Moul&family=UnifrakturMaguntia&display=swap" rel="stylesheet">
    <style>
        /*
         * Content only — printed onto the pre-printed certificate paper
         * (decorative frame, logo, kingdom header and address footer are
         * already on it). Positions follow the scanned Provisional Certificate.
         * --offset-x / --offset-y are the printer-alignment nudges from the
         * toolbar (saved per browser).
         */
        :root {
            --offset-x: 0mm;
            --offset-y: 0mm;
            --kh: 'Khmer OS Battambang', 'Battambang', sans-serif;
            --kh-title: 'Khmer OS Muol Light', 'Kh Muol', 'Moul', serif;
            --en: 'Times New Roman', Times, serif;
        }

        @page {
            size: A4;
            margin: 0;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            background: #e5e5e5;
            color: #000;
            font-family: var(--en);
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .sheet {
            position: relative;
            width: 210mm;
            height: 297mm;
            margin: 12px auto;
            background: #fff;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, .15);
        }

        /* Everything printed sits in here, so one offset moves it all. */
        .layer {
            position: absolute;
            inset: 0;
            transform: translate(var(--offset-x), var(--offset-y));
        }

        .abs {
            position: absolute;
        }

        /* Screen-only guides for what the paper already carries. */
        .guide {
            position: absolute;
            display: flex;
            align-items: center;
            justify-content: center;
            font: 11px system-ui, sans-serif;
            color: #9ca3af;
            background: repeating-linear-gradient(45deg, #fafafa, #fafafa 6px, #f3f4f6 6px, #f3f4f6 12px);
        }

        .guide.frame {
            inset: 0;
            background: none;
            border: 14mm solid #f3f4f6;
            outline: 1px dashed #d1d5db;
            outline-offset: -14mm;
        }

        .guide.top {
            top: 14mm;
            left: 14mm;
            right: 14mm;
            height: 64mm;
            border-bottom: 1px dashed #d1d5db;
        }

        .guide.bottom {
            bottom: 14mm;
            left: 14mm;
            right: 14mm;
            height: 33mm;
            border-top: 1px dashed #d1d5db;
        }

        .issue-no {
            left: 30mm;
            top: 81.5mm;
            font-family: var(--kh);
            font-size: 10.5pt;
        }

        .titles {
            left: 0;
            right: 0;
            top: 87mm;
            text-align: center;
        }

        .titles .kh {
            font-family: var(--kh-title);
            font-size: 16pt;
            line-height: 1.4;
        }

        .titles .en {
            font-family: 'Old English Text MT', 'UnifrakturMaguntia', serif;
            font-size: 25pt;
            line-height: 1;
        }

        .titles .rule {
            width: 52mm;
            margin: 1.5mm auto 0;
            border-top: 1px solid #000;
            position: relative;
        }

        .titles .rule::after {
            content: '❖';
            position: absolute;
            left: 50%;
            top: -2.3mm;
            transform: translateX(-50%);
            padding: 0 1.5mm;
            background: #fff;
            font-size: 8pt;
        }

        /* Khmer (left) and English (right) side by side, row-aligned. */
        /* Khmer column 31–105mm, English column 107–190mm (as on the scan). */
        .body {
            left: 30mm;
            right: 20mm;
            top: 106.5mm;
            display: grid;
            grid-template-columns: 74mm 1fr;
            column-gap: 3mm;
            align-items: center;
        }

        .kh {
            font-family: var(--kh);
            font-size: 10.5pt;
        }

        .en {
            font-family: var(--en);
            font-size: 11pt;
        }

        .moul {
            font-family: var(--kh-title);
            font-weight: normal;
        }

        .body .head {
            height: 9mm;
            white-space: nowrap;
        }

        .body .head.kh {
            font-family: var(--kh-title);
            font-size: 11pt;
            padding-left: 3mm;
        }

        .body .head.en {
            font-size: 15.5pt;
            font-weight: bold;
            padding-left: 4mm;
        }

        /* Fixed height keeps each Khmer row level with its English twin. */
        .row {
            display: flex;
            align-items: baseline;
            height: 6.7mm;
            white-space: nowrap;
        }

        .row .label {
            flex-shrink: 0;
        }

        .kh.row .label {
            width: 28mm;
        }

        .en.row .label {
            width: 25mm;
        }

        .en.row .name {
            font-weight: bold;
            font-size: 12.5pt;
        }

        .kh.row .name {
            font-family: var(--kh-title);
            font-size: 11pt;
        }

        .para {
            align-self: start;
            margin-top: 1.5mm;
        }

        .kh.para {
            line-height: 1.75;
        }

        .en.para {
            line-height: 1.5;
        }

        .para p {
            margin: 0;
        }

        .en.para b.big {
            font-size: 13pt;
        }

        .photo {
            left: 34mm;
            top: 192mm;
            width: 23mm;
            height: 30mm;
            border: 1px solid #000;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: var(--kh);
            font-size: 11pt;
        }

        /* Under the English paragraph, in flow — a long major name pushes it down instead of overlapping. */
        .sign {
            grid-column: 2;
            margin-top: 0.5mm;
            padding-left: 4mm;
            text-align: center;
            line-height: 1.4;
            white-space: nowrap;
        }

        .sign .lunar {
            font-family: var(--kh);
            font-size: 10pt;
        }

        .sign .role-kh {
            font-family: var(--kh-title);
            font-size: 11pt;
        }

        .sign .role-en {
            font-weight: bold;
            font-size: 11.5pt;
        }

        .sign .signatory {
            margin-top: 11mm;
            font-family: var(--kh-title);
            font-size: 12pt;
        }

        .note {
            left: 29mm;
            top: 236mm;
            line-height: 1.5;
        }

        .note .kh {
            font-size: 10.5pt;
        }

        .note .en {
            font-size: 11.5pt;
        }

        body.hide-note .note,
        body.hide-photo .photo {
            display: none;
        }

        /* Toolbar */
        .toolbar {
            position: sticky;
            top: 0;
            z-index: 10;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
            justify-content: center;
            padding: 10px 16px;
            background: #111827;
            color: #e5e7eb;
            font: 13px system-ui, sans-serif;
        }

        .toolbar button {
            padding: 7px 14px;
            border: 0;
            border-radius: 8px;
            font: 600 13px system-ui, sans-serif;
            cursor: pointer;
            background: #4f46e5;
            color: #fff;
        }

        .toolbar input[type=number] {
            width: 56px;
            padding: 5px 6px;
            border-radius: 6px;
            border: 1px solid #4b5563;
            background: #1f2937;
            color: #fff;
        }

        .toolbar label {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            cursor: pointer;
        }

        @media print {

            html,
            body {
                background: #fff;
            }

            .toolbar,
            .guide {
                display: none !important;
            }

            .sheet {
                margin: 0;
                box-shadow: none;
            }
        }
    </style>
</head>

<body>
    <div class="toolbar">
        <button type="button" onclick="window.print()">🖨 Print</button>
        <label><input type="checkbox" id="showPhoto"> Photo box</label>
        <label><input type="checkbox" id="showNote"> Note line</label>
        <span>Printer offset — X <input type="number" step="0.5" id="offsetX"> mm &nbsp; Y <input type="number" step="0.5" id="offsetY"> mm</span>
        <span style="opacity:.7">Load the certificate paper · A4 · scale 100% · margins none</span>
    </div>

    <div class="sheet">
        <div class="guide frame"></div>
        <div class="guide top">Logo · Kingdom header (pre-printed)</div>
        <div class="guide bottom">Address footer (pre-printed)</div>

        <div class="layer">
            <div class="abs issue-no">លេខ: {{ Khmer::digits($t->issue_no) }}</div>

            <div class="abs titles">
                <div class="kh">វិញ្ញាបនបត្របណ្ដោះអាសន្ន</div>
                <div class="en">Provisional Certificate</div>
                <div class="rule"></div>
            </div>

            <div class="abs body">
                <div class="kh head">សាកលវិទ្យាល័យវេស្ទើន បញ្ជាក់ថា :</div>
                <div class="en head">This is to certify that:</div>

                <div class="kh row"><span class="label">ឈ្មោះ:</span><span class="name">{{ $t->name_kh }}</span></div>
                <div class="en row"><span class="label">Name:</span><span class="name">{{ $fullName }}</span></div>

                <div class="kh row"><span class="label">ភេទ:</span><span>{{ Khmer::sex($t->sex) }}</span></div>
                <div class="en row"><span class="label">Sex:</span><span>{{ $t->sex }}</span></div>

                <div class="kh row"><span class="label">សញ្ជាតិ:</span><span>{{ Khmer::nationality($t->nationality) }}</span></div>
                <div class="en row"><span class="label">Nationality:</span><span>{{ $t->nationality }}</span></div>

                <div class="kh row"><span class="label">ថ្ងៃខែឆ្នាំកំណើត:</span><span>{{ Khmer::date($t->dob) }}</span></div>
                <div class="en row"><span class="label">Date of Birth:</span><span>{{ $t->dob?->format('F j, Y') }}</span></div>

                <div class="kh row"><span class="label">ទីកន្លែងកំណើត:</span><span>{{ $t->pob_kh ?: $t->pob }}</span></div>
                <div class="en row"><span class="label">Place of Birth:</span><span>{{ $t->pob }}</span></div>

                <div class="kh para">
                    <p>បានបំពេញគ្រប់លក្ខខណ្ឌតម្រូវ របស់សាកលវិទ្យាល័យ ដើម្បីទទួលសញ្ញាបត្រ <span class="moul">{{ $degreeKh }}</span>
                        ឯកទេស <span class="moul">{{ $majorKh }}</span> ។</p>
                    <p>វិញ្ញាបនបត្រនេះចេញឲ្យសាមីខ្លួនប្រើប្រាស់តាមការដែលអាចប្រើបាន។</p>
                    @if ($t->exam_date)
                        <p>សម័យប្រឡង {{ Khmer::date($t->exam_date) }}</p>
                    @endif
                </div>
                <div class="en para">
                    <p>has successfully fulfilled all the requirements of the university to be awarded the
                        <b class="big">{{ $degreeEn }},</b> majoring in <b>{{ $majorEn }}.</b></p>
                    <p>This certificate is presented to the bearer with all rights and privileges thereto pertaining.</p>
                    @if ($t->exam_date)
                        <p>Date of comprehensive exam: {{ $t->exam_date->format('F j, Y') }}</p>
                    @endif
                </div>

                <div class="sign">
                    <div class="lunar">{{ $t->date_kh }}</div>
                    @if ($t->issue_date)
                        <div class="kh">រាជធានីភ្នំពេញ {{ Khmer::date($t->issue_date) }}</div>
                        <div class="en">Phnom Penh, {{ $t->issue_date->format('F j, Y') }}</div>
                    @endif
                    <div class="role-kh">{{ $t->person_role_kh }}</div>
                    <div class="role-en">{{ $t->person_role }}</div>
                    <div class="signatory">{{ $t->person_in_charge_kh ?: $t->person_in_charge }}</div>
                </div>
            </div>

            <div class="abs photo">៤ x ៦</div>

            <div class="abs note">
                <div class="kh"><b class="moul">កំណត់សំគាល់:</b> វិញ្ញាបនបត្រនេះចេញជូនបានតែម្ដងគត់។</div>
                <div class="en"><b>Note:</b> This certificate is issued once only.</div>
            </div>
        </div>
    </div>

    <script>
        // Printer-alignment nudge and the two optional blocks, remembered per
        // browser. Purely cosmetic if storage is blocked.
        (function () {
            const read = (k, fallback) => { try { return localStorage.getItem('provisional-' + k) ?? fallback; } catch (e) { return fallback; } };
            const save = (k, v) => { try { localStorage.setItem('provisional-' + k, String(v)); } catch (e) {} };

            const root = document.documentElement.style;
            ['x', 'y'].forEach((k) => {
                const input = document.getElementById('offset' + k.toUpperCase());
                const apply = (v) => {
                    root.setProperty('--offset-' + k, (Number(v) || 0) + 'mm');
                    save('offset-' + k, Number(v) || 0);
                };
                input.value = read('offset-' + k, '0');
                apply(input.value);
                input.addEventListener('input', (e) => apply(e.target.value));
            });

            [['showPhoto', 'photo'], ['showNote', 'note']].forEach(([id, key]) => {
                const box = document.getElementById(id);
                const apply = (on) => {
                    document.body.classList.toggle('hide-' + key, !on);
                    save(key, on ? 1 : 0);
                };
                box.checked = read(key, '1') === '1';
                apply(box.checked);
                box.addEventListener('change', (e) => apply(e.target.checked));
            });
        })();
    </script>
</body>

</html>
