@php
    $romans = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV'];
    $fmt = fn ($n) => $n === null ? '' : number_format((float) $n, 2);
    $majorName = $transcript->major?->name_en;
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transcript {{ $transcript->issue_no }} — {{ $transcript->surname }} {{ $transcript->name }}</title>
    <style>
        /*
         * Content only — printed onto the university's pre-printed letterhead
         * (logo + kingdom header on top, address bar at the bottom), so no
         * frame/logo here. Positions follow the scanned official transcript.
         * --offset-x / --offset-y are the printer-alignment nudges from the
         * toolbar (saved per browser).
         */
        :root {
            --offset-x: 0mm;
            --offset-y: 0mm;
            --green: #a9d08e;
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
            font-family: 'Times New Roman', Times, serif;
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

        .content {
            position: absolute;
            left: calc(5mm + var(--offset-x));
            right: calc(5.4mm - var(--offset-x));
            top: calc(44mm + var(--offset-y));
        }

        /* Screen-only guides showing where the letterhead's own print sits. */
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
            height: 42mm;
            border-bottom: 1px dashed #d1d5db;
        }

        .guide.bottom {
            bottom: 0;
            height: 18mm;
            border-top: 1px dashed #d1d5db;
        }

        .issue-no {
            margin-top: 1.5mm;
            width: 62mm;
            text-align: center;
            font-size: 9.5pt;
            margin-left: -4mm;
        }

        h1 {
            margin: 1.5mm 0 2mm;
            text-align: center;
            font-size: 20pt;
            font-weight: bold;
            letter-spacing: .2px;
        }

        .info {
            display: grid;
            grid-template-columns: 79mm 44mm 1fr;
            row-gap: 1.2mm;
            font-size: 10.5pt;
            margin-bottom: 1.5mm;
        }

        .info b {
            font-weight: bold;
        }

        .info .full {
            grid-column: 1 / -1;
        }
/*
        .info .address b {
            margin-left: 5mm;
        } */

        table.courses {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            font-size: 10.5pt;
        }

        table.courses td {
            padding: 0 1mm 0 0;
            height: 5.5mm; /* subject row spacing — smaller = tighter rows */
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            vertical-align: middle;
        }

        table.courses tr.head td {
            font-weight: bold;
            text-align: center;
            height: 9mm;
            line-height: 1.1;
            border-top: 2.5px solid var(--green);
            border-bottom: 2.5px solid var(--green);
        }

        /* Closing green line under the last year's courses. */
        table.courses tr:last-child td {
            border-bottom: 2.5px solid var(--green);
        }

        table.courses tr.head.single td {
            height: 6mm;
            border-bottom: 0;
        }

        table.courses tr.head td.left {
            text-align: left;
        }

        /* Course codes are never cut ("COMM-401"); the title column gives way instead. */
        table.courses td.code {
            text-overflow: clip;
            overflow: visible;
        }

        .c {
            text-align: center;
        }

        .ip {
            text-align: center;
        }

        .totals {
            margin-left: 50%;
            border-top: 1.5px dashed #000;
            margin-top: 1mm;
            padding-top: 1mm;
            font-size: 10.5pt;
            font-weight: bold;
        }

        .totals .row {
            display: flex;
        }

        .totals .label {
            flex: 1;
            padding-left: 9mm;
        }

        .totals .value {
            width: 22mm;
            text-align: center;
        }

        .sign {
            margin-left: 50%;
            text-align: center;
            font-size: 10.5pt;
            font-weight: bold;
            margin-top: 2mm;
        }

        .signatory {
            margin-left: 50%;
            margin-top: 15mm;
            padding-left: 44mm;
            font-size: 10.5pt;
            font-weight: bold;
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

        .toolbar button,
        .toolbar a {
            padding: 7px 14px;
            border: 0;
            border-radius: 8px;
            font: 600 13px system-ui, sans-serif;
            cursor: pointer;
            text-decoration: none;
        }

        .toolbar .primary {
            background: #4f46e5;
            color: #fff;
        }

        .toolbar .ghost {
            background: #374151;
            color: #e5e7eb;
        }

        .toolbar input {
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
    @include('transcript.partials.preview')
    <div class="toolbar">
        @unless ($preview)
            <button type="button" class="primary" onclick="window.print()">🖨 Print</button>
        @endunless
        <a class="ghost" href="{{ request()->fullUrlWithQuery(['detail' => $detail ? 0 : 1]) }}">
            {{ $detail ? 'Hide' : 'Show' }} score &amp; grade point columns
        </a>
        <span>Printer offset — X <input type="number" step="0.5" id="offsetX"> mm &nbsp; Y <input type="number" step="0.5" id="offsetY"> mm</span>
        <span style="opacity:.7">Load the letterhead paper · A4 · scale 100% · margins none</span>
    </div>

    <div class="sheet">
        <div class="guide top">Letterhead area (not printed)</div>
        <div class="guide bottom">Letterhead footer (not printed)</div>

        <div class="content">
            <div class="issue-no">N<sup>o</sup>: {{ $transcript->issue_no }}</div>
            <h1>Official Transcript</h1>

            <div class="info">
                <div>Name: <b>{{ $transcript->name }}</b></div>
                <div>ID: <b>{{ $transcript->student_code }}</b></div>
                <div>Degree: <b>{{ $transcript->degree_label }}</b></div>

                <div>Surname: <b>{{ $transcript->surname }}</b></div>
                <div>Sex: <b>{{ $transcript->sex }}</b></div>
                <div>Major: <b>{{ $majorName }}</b></div>

                <div>Date of Birth: <b>{{ $transcript->dob?->format('F j, Y') }}</b></div>
                <div>Nationality: <b>{{ $transcript->nationality }}</b></div>
                <div>Academic Year: <b>{{ $transcript->academic_year }}</b></div>

                <div class="full">Place of Birth: <b>{{ $transcript->pob }}</b></div>
                <div class="full address">Address: <b>{{ $transcript->address }}</b></div>
            </div>

            <table class="courses">
                <colgroup>
                    @foreach ([1, 2] as $side)
                        <col style="width: 21mm">
                        <col>
                        <col style="width: {{ $side === 1 ? 7 : 9 }}mm">
                        @if ($detail)
                            <col style="width: 11mm">
                            <col style="width: 9mm">
                        @endif
                        <col style="width: 10mm">
                    @endforeach
                </colgroup>

                @foreach ($years as $year => $semesters)
                    @php
                        $roman = $romans[$year] ?? $year;
                        $rowCount = max($semesters[1]->count(), $semesters[2]->count());
                    @endphp
                    <tr class="head {{ $loop->first ? '' : 'single' }}">
                        <td class="left">YEAR {{ $roman }}@if ($loop->first)<br>CODE N<sup>o</sup>@endif</td>
                        <td>SEMESTER I @if ($loop->first)<br>COURSE TITLES @endif</td>
                        <td>CR</td>
                        @if ($detail)
                            <td>Score</td>
                            <td>GP</td>
                        @endif
                        <td>GRD</td>
                        <td class="left">CODE N<sup>o</sup></td>
                        <td>SEMESTER II @if ($loop->first)<br>COURSE TITLES @endif</td>
                        <td>CR</td>
                        @if ($detail)
                            <td>Score</td>
                            <td>GP</td>
                        @endif
                        <td>GRD</td>
                    </tr>

                    @for ($i = 0; $i < $rowCount; $i++)
                        <tr>
                            @foreach ([1, 2] as $semester)
                                @php $course = $semesters[$semester][$i] ?? null; @endphp
                                @if (! $course)
                                    <td></td><td></td><td></td>
                                    @if ($detail)<td></td><td></td>@endif
                                @else
                                    <td class="code">{{ $course->code }}</td>
                                    <td class="title">{{ $course->title }}</td>
                                    @if ($course->isInProgress())
                                        <td class="ip" colspan="{{ $detail ? 4 : 2 }}">IP</td>
                                        @continue
                                    @endif
                                    <td class="c">{{ $course->credit }}</td>
                                    @if ($detail)
                                        <td class="c">{{ $fmt($course->score) }}</td>
                                        <td class="c">{{ $fmt($course->grade_point) }}</td>
                                    @endif
                                @endif
                                @if ($course)
                                    <td>{{ $course->grade }}</td>
                                @else
                                    <td></td>
                                @endif
                            @endforeach
                        </tr>
                    @endfor
                @endforeach
            </table>

            <div class="totals">
                <div class="row">
                    <span class="label">TOTAL CREDITS COMPLETED:</span>
                    <span class="value">{{ number_format($transcript->total_credits, 2) }}</span>
                </div>
                <div class="row">
                    <span class="label" style="padding-left: 14mm">TOTAL GPA:</span>
                    <span class="value">{{ number_format((float) $transcript->total_gpa, 2) }}</span>
                </div>
            </div>

            <div class="sign">
                <div>Phnom Penh, {{ $transcript->issue_date?->format('F j, Y') }}</div>
                <div><span id="signRole">{{ $transcript->person_role }}</span></div>
            </div>

            <div class="signatory" id="signatory">{{ $transcript->person_in_charge }}</div>
        </div>
    </div>

    <script>
        // Printer-alignment nudge, remembered per browser (every printer feeds
        // letterhead slightly differently). Purely cosmetic if storage is blocked.
        // A course title too long for its column shrinks (down to 8pt) instead
        // of being cut off with "…". Runs before the signature measurement.
        document.querySelectorAll('table.courses td.title').forEach((cell) => {
            let size = 10.5;
            while (cell.scrollWidth > cell.clientWidth && size > 8) {
                size -= 0.25;
                cell.style.fontSize = size + 'pt';
            }
        });

        // The signatory's name starts directly under the "I" of the role
        // (PRES·I·DENT) — measured from the rendered text, so it holds for any
        // role wording; with no "I" it lines up with the role's first letter.
        (function () {
            const role = document.getElementById('signRole');
            const name = document.getElementById('signatory');
            const text = role?.firstChild;
            if (!role || !name || !text) return;

            const index = Math.max(0, (text.textContent || '').toUpperCase().indexOf('I'));
            const range = document.createRange();
            range.setStart(text, Math.min(index, text.length));
            range.setEnd(text, Math.min(index + 1, text.length));

            const anchor = range.getBoundingClientRect().left;
            const base = name.parentElement.getBoundingClientRect().left;
            name.style.marginLeft = '0';
            name.style.paddingLeft = (anchor - base) + 'px';
        })();

        (function () {
            const root = document.documentElement.style;
            const inputs = { x: document.getElementById('offsetX'), y: document.getElementById('offsetY') };
            const read = (k) => { try { return localStorage.getItem('transcript-offset-' + k) || '0'; } catch (e) { return '0'; } };
            const apply = (k, v) => {
                root.setProperty('--offset-' + k, (Number(v) || 0) + 'mm');
                try { localStorage.setItem('transcript-offset-' + k, String(Number(v) || 0)); } catch (e) {}
            };
            ['x', 'y'].forEach((k) => {
                inputs[k].value = read(k);
                apply(k, inputs[k].value);
                inputs[k].addEventListener('input', (e) => apply(k, e.target.value));
            });
        })();
    </script>
</body>

</html>
