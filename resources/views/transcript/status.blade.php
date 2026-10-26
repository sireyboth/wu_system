@php
    use App\Helpers\Degree;
    use App\Helpers\Khmer;

    $t = $transcript;
    $degree = Degree::tryFrom((string) $t->degree);
    $degreeEn = $degree ? ucwords($degree->labelEn()) : ucfirst((string) $t->degree);
    $degreeKh = $degree?->labelKh() ?? '';
    $majorEn = $t->major?->name_en;
    $majorKh = $t->major?->name_kh ?: $majorEn;
    $fullName = mb_strtoupper(trim("{$t->surname} {$t->name}"));

    $graduated = $t->study_status === \App\Models\Transcript::GRADUATED;

    // "ឆ្នាំទី៤ ឆមាសទី២" / "year 4, semester 2" — each part only when it was filled in.
    $termKh = trim(
        ($t->current_year ? 'ឆ្នាំទី' . Khmer::digits($t->current_year) : '') . ' ' .
        ($t->current_semester ? 'ឆមាសទី' . Khmer::digits($t->current_semester) : '')
    );
    $termEn = collect([
        $t->current_year ? "year {$t->current_year}" : null,
        $t->current_semester ? "semester {$t->current_semester}" : null,
    ])->filter()->implode(', ');

    // Khmer address: typed one first, else the English address when it is a
    // province name ("Phnom Penh" → "រាជធានីភ្នំពេញ", "Kandal Province" → "ខេត្តកណ្ដាល").
    $addressKh = $t->address_kh;
    if (! $addressKh && $t->address) {
        $province = \App\Models\Province::query()
            ->where('name_en', trim(preg_replace('/\s+Province$/i', '', $t->address)))
            ->first();
        $addressKh = match (true) {
            ! $province                       => $t->address,
            $province->name_en === 'Phnom Penh' => 'រាជធានីភ្នំពេញ',
            default                           => "ខេត្ត{$province->name_kh}",
        };
    }

    // "បណ្ឌិត កៀង រតនា" → title "បណ្ឌិត", name "កៀង រតនា".
    $signer = trim((string) ($t->person_in_charge_kh ?: $t->person_in_charge));
    $signerTitle = collect(['បណ្ឌិត', 'សាស្ត្រាចារ្យ', 'លោកស្រី', 'លោក'])
        ->first(fn ($title) => str_starts_with($signer, $title . ' '));
    $signerName = $signerTitle ? trim(mb_substr($signer, mb_strlen($signerTitle))) : $signer;
@endphp
<!DOCTYPE html>
<html lang="km">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Status Certification {{ $t->issue_no }} — {{ $fullName }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Battambang:wght@400;700&family=Moul&family=UnifrakturMaguntia&display=swap" rel="stylesheet">
    <style>
        /*
         * Content only — printed onto the university letterhead (logo,
         * kingdom header and address footer are already on the paper).
         * Positions follow the scanned Student Status Certification.
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
            left: 0;
            right: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font: 11px system-ui, sans-serif;
            color: #9ca3af;
            background: repeating-linear-gradient(45deg, #fafafa, #fafafa 6px, #f3f4f6 6px, #f3f4f6 12px);
        }

        .guide.top {
            top: 0;
            height: 45mm;
            border-bottom: 1px dashed #d1d5db;
        }

        .guide.bottom {
            bottom: 0;
            height: 20mm;
            border-top: 1px dashed #d1d5db;
        }

        .kh {
            font-family: var(--kh);
            font-size: 11pt;
        }

        .en {
            font-family: var(--en);
            font-size: 12pt;
        }

        .moul {
            font-family: var(--kh-title);
            font-weight: normal;
        }

        .issue-no {
            left: 13.5mm;
            top: 46mm;
            font-family: var(--kh);
            font-size: 10.5pt;
        }

        .titles {
            left: 0;
            right: 0;
            top: 65.5mm;
            text-align: center;
        }

        .titles .title-kh {
            font-family: var(--kh-title);
            font-size: 16pt;
            letter-spacing: .3em;
            line-height: 1.5;
        }

        .titles .title-en {
            font-family: 'Old English Text MT', 'UnifrakturMaguntia', serif;
            font-size: 21pt;
            line-height: 1.1;
        }

        .titles .rule {
            width: 20mm;
            margin: 6mm auto 0;
            border-top: 1px solid #000;
            position: relative;
        }

        .titles .rule::after {
            content: '❖';
            position: absolute;
            left: 50%;
            top: -2.3mm;
            transform: translateX(-50%);
            padding: 0 1.2mm;
            background: #fff;
            font-size: 8pt;
        }

        /* Khmer column 13.5–108mm, English column 113–205mm (as on the scan). */
        .col-kh {
            left: 13.5mm;
            width: 95mm;
            top: 93.5mm;
        }

        .col-en {
            left: 113.5mm;
            width: 91mm;
            top: 93.5mm;
        }

        .head {
            height: 9.5mm;
            white-space: nowrap;
        }

        .col-kh .head {
            padding-left: 10mm;
            font-family: var(--kh-title);
            font-size: 12pt;
        }

        .col-en .head {
            padding-left: 4mm;
            font-size: 17pt;
        }

        .row {
            display: flex;
            align-items: baseline;
            white-space: nowrap;
        }

        .row .label {
            flex-shrink: 0;
            padding-right: 2mm;
        }

        .col-kh .row {
            height: 7.45mm;
        }

        .col-kh .row .label {
            min-width: 33mm;
        }

        .col-en .row {
            height: 7.9mm;
        }

        .col-en .row .label {
            min-width: 32mm;
        }

        .col-kh .row .name {
            font-family: var(--kh-title);
            font-size: 11.5pt;
        }

        .col-en .row .name {
            font-weight: bold;
        }

        .col-kh .para {
            line-height: 7.6mm;
        }

        .col-en .para {
            line-height: 7.9mm;
        }

        .para p {
            margin: 0;
        }

        /* "ឆ្នាំទី៤ ឆមាសទី២" wraps as one piece under a long major name. */
        .para .nowrap {
            white-space: nowrap;
        }

        /* Lunar date sits right under the English paragraph, so a long major pushes it down. */
        .sign {
            width: 69mm;
            margin-top: 1.5mm;
            text-align: center;
            line-height: 6.2mm;
            white-space: nowrap;
        }

        .sign .kh {
            font-size: 10pt;
        }

        .sign .role-kh {
            font-family: var(--kh-title);
            font-size: 11pt;
        }

        .sign .role-en {
            font-weight: bold;
            font-size: 13pt;
        }

        .signatory {
            left: 142.5mm;
            top: 245.5mm;
            font-family: var(--kh);
            font-size: 11pt;
            white-space: nowrap;
        }

        .signatory .moul {
            font-size: 14pt;
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
        <span>Printer offset — X <input type="number" step="0.5" id="offsetX"> mm &nbsp; Y <input type="number" step="0.5" id="offsetY"> mm</span>
        <span style="opacity:.7">Load the letterhead paper · A4 · scale 100% · margins none</span>
    </div>

    <div class="sheet">
        <div class="guide top">Logo · Kingdom header (pre-printed)</div>
        <div class="guide bottom">Address footer (pre-printed)</div>

        <div class="layer">
            <div class="abs issue-no">លេខ: {{ Khmer::digits($t->issue_no) }}</div>

            <div class="abs titles">
                <div class="title-kh">លិខិតបញ្ជាក់ការសិក្សា</div>
                <div class="title-en">Student Status Certification</div>
                <div class="rule"></div>
            </div>

            <div class="abs col-kh kh">
                <div class="head">សាកលវិទ្យាល័យវេស្ទើន បញ្ជាក់ថា:</div>
                <div class="row"><span class="label">ឈ្មោះ:</span><span class="name">{{ $t->name_kh }}</span></div>
                <div class="row"><span class="label">ភេទ:</span><span>{{ Khmer::sex($t->sex) }}</span></div>
                <div class="row"><span class="label">អត្តលេខ:</span><span>{{ $t->student_code }}</span></div>
                <div class="row"><span class="label">សញ្ជាតិ:</span><span>{{ Khmer::nationality($t->nationality) }}</span></div>
                <div class="row"><span class="label">ថ្ងៃខែឆ្នាំកំណើត:</span><span>{{ Khmer::date($t->dob) }}</span></div>
                <div class="row"><span class="label">ទីកន្លែងកំណើត:</span><span>{{ $t->pob_kh ?: $t->pob }}</span></div>
                <div class="row"><span class="label">អាសយដ្ឋានបច្ចុប្បន្ន:</span><span>{{ $addressKh }}</span></div>
                <div class="para">
                    <p>{{ $graduated ? 'ជានិស្សិតបានបញ្ចប់ការសិក្សាថ្នាក់' : 'ជានិស្សិតកំពុងសិក្សាថ្នាក់' }} <span class="moul">{{ $degreeKh }}</span></p>
                    <p>ឯកទេស <span class="moul">{{ $majorKh }}</span>@if ($termKh) <span class="nowrap">{{ $termKh }}</span>@endif</p>
                    <p>ឆ្នាំសិក្សា {{ Khmer::digits($t->academic_year) }}</p>
                    <p>នៅ <span class="moul">សាកលវិទ្យាល័យវេស្ទើន</span> ពិតប្រាកដមែន។</p>
                    <p>លិខិតបញ្ជាក់នេះចេញឲ្យសាមីខ្លួនប្រើប្រាស់តាមការដែលអាចប្រើបាន។</p>
                </div>
            </div>

            <div class="abs col-en en">
                <div class="head">This is to certify that:</div>
                <div class="row"><span class="label">Name:</span><span class="name">{{ $fullName }}</span></div>
                <div class="row"><span class="label">Sex:</span><span>{{ $t->sex }}</span></div>
                <div class="row"><span class="label">ID:</span><span>{{ $t->student_code }}</span></div>
                <div class="row"><span class="label">Nationality:</span><span>{{ $t->nationality }}</span></div>
                <div class="row"><span class="label">Date of Birth:</span><span>{{ $t->dob?->format('F j, Y') }}</span></div>
                <div class="row"><span class="label">Place of Birth:</span><span>{{ $t->pob }}</span></div>
                <div class="row"><span class="label">Current Address:</span><span>{{ $t->address }}</span></div>
                <div class="para">
                    <p>{{ $graduated ? 'has completed' : 'is currently pursuing' }}{{ $termEn ? " {$termEn}" : '' }}
                        <b>{{ $degreeEn }}</b>, majoring in <b>{{ $majorEn }},</b> {{ $t->academic_year }}, at
                        <b>WESTERN UNIVERSITY</b>.</p>
                    <p>This certification is presented to the bearer with all rights and privileges thereto pertaining.</p>
                </div>

                <div class="sign">
                    <div class="kh">{{ $t->date_kh }}</div>
                    @if ($t->issue_date)
                        <div class="kh">រាជធានីភ្នំពេញ {{ Khmer::date($t->issue_date) }}</div>
                        <div class="en">Phnom Penh, {{ $t->issue_date->format('F j, Y') }}</div>
                    @endif
                    <div class="role-kh">{{ $t->person_role_kh }}</div>
                    <div class="role-en">{{ mb_strtoupper((string) $t->person_role) }}</div>
                </div>
            </div>

            {{-- As on the scan: the academic title in plain text, the name in Moul. --}}
            <div class="abs signatory">
                @if ($signerTitle)<span>{{ $signerTitle }}</span> @endif<span class="moul">{{ $signerName }}</span>
            </div>
        </div>
    </div>

    <script>
        // Printer-alignment nudge, remembered per browser. Purely cosmetic if storage is blocked.
        (function () {
            const read = (k, fallback) => { try { return localStorage.getItem('status-letter-' + k) ?? fallback; } catch (e) { return fallback; } };
            const save = (k, v) => { try { localStorage.setItem('status-letter-' + k, String(v)); } catch (e) {} };

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
        })();
    </script>
</body>

</html>
