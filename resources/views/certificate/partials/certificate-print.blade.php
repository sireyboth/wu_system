<div id="certificatePrintArea" class="hidden">
    <link href="https://fonts.googleapis.com/css2?family=Moul&family=Battambang:wght@400;700&family=EB+Garamond:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        #certificatePrintArea {
            --ink: #1a1a1a;
            --muted: #5c5c5c;
            --rule: #9a9a9a;
            font-family: 'EB Garamond', 'Times New Roman', serif;
            font-size: 15px;
            color: var(--ink);
        }
        #certificatePrintArea .kh { font-family: 'Khmer OS Battambang', 'Battambang', sans-serif; }
        #certificatePrintArea .moul { font-family: 'Khmer OS Muol Light', 'Moul', cursive; font-weight: normal; }

        #certificatePrintArea .a4-container {
            width: 210mm;
            height: 297mm;
            margin: 0 auto;
            padding: 22mm 18mm 20mm;
            box-sizing: border-box;
            background: #fff;
            display: flex;
            flex-direction: column;
        }

        /* Reference number */
        #certificatePrintArea .doc-number { font-size: 13px; text-align: right; color: var(--muted); }
        #certificatePrintArea .doc-number span { color: var(--ink); font-weight: 600; }

        /* Title */
        #certificatePrintArea .title-block { text-align: center; margin: 10mm 0 8mm; }
        #certificatePrintArea .title-kh { font-size: 26px; margin: 0; line-height: 1.6; }
        #certificatePrintArea .title-en {
            font-size: 15px; font-weight: 600; letter-spacing: 0.32em;
            text-transform: uppercase; margin: 4px 0 0;
        }
        #certificatePrintArea .title-rule {
            display: flex; align-items: center; justify-content: center; gap: 10px;
            margin-top: 10px; color: var(--rule);
        }
        #certificatePrintArea .title-rule::before,
        #certificatePrintArea .title-rule::after { content: ''; width: 46mm; border-top: 1px solid var(--rule); }
        #certificatePrintArea .title-rule i { width: 6px; height: 6px; transform: rotate(45deg); background: var(--rule); }

        /* Two bilingual panels */
        #certificatePrintArea .content-grid {
            display: grid;
            grid-template-columns: 1fr 1px 1fr;
            column-gap: 8mm;
            margin-bottom: 12mm;
        }
        #certificatePrintArea .divider { background: #d6d6d6; }
        #certificatePrintArea .lead { margin: 0 0 3mm; height: 10mm; display: flex; align-items: flex-end; }
        #certificatePrintArea .lead.kh-lead { font-size: 15px; line-height: 1.8; }
        #certificatePrintArea .lead.en-lead { font-size: 18px; font-style: italic; }

        /* Fixed row height so the Khmer and English rows line up across the divider */
        #certificatePrintArea .fields {
            display: grid; grid-template-columns: auto 1fr; grid-auto-rows: 9.5mm;
            align-items: end; column-gap: 10px; margin: 0;
        }
        #certificatePrintArea .fields dt { color: var(--muted); white-space: nowrap; }
        #certificatePrintArea .fields dd { margin: 0; border-bottom: 1px dotted #c4c4c4; padding-bottom: 1px; }
        #certificatePrintArea .fields.kh { font-size: 14px; }
        #certificatePrintArea .name-kh { font-family: 'Khmer OS Muol Light', 'Moul', cursive; font-size: 15px; }
        #certificatePrintArea .name-en { font-weight: 700; letter-spacing: 0.04em; }

        #certificatePrintArea .statement { margin: 7mm 0 0; text-align: justify; line-height: 1.9; }
        /* Khmer has few spaces to stretch, so justify leaves gaps */
        #certificatePrintArea .statement.kh { font-size: 14px; line-height: 2; text-align: left; }

        /* Signature */
        #certificatePrintArea .signature {
            margin-top: auto;
            margin-left: auto;
            width: 82mm;
            text-align: center;
            line-height: 1.7;
        }
        #certificatePrintArea .signature .date-kh { font-size: 13px; }
        #certificatePrintArea .signature .date-en { font-size: 14px; font-style: italic; }
        #certificatePrintArea .signature .role-kh { font-size: 15px; margin-top: 6mm; }
        #certificatePrintArea .signature .role-en { font-weight: 700; letter-spacing: 0.2em; }
        #certificatePrintArea .signature .sign-space { height: 26mm; }
        #certificatePrintArea .signature .signer { font-size: 15px; border-top: 1px solid var(--rule); padding-top: 4px; margin: 0 8mm; }

        @page { size: A4; margin: 0; }
        @media print {
            body * { visibility: hidden; }
            #certificatePrintArea, #certificatePrintArea * { visibility: visible; }
            #certificatePrintArea.hidden { display: block !important; }
            #certificatePrintArea { position: absolute; top: 0; left: 0; }
            #certificatePrintArea .a4-container { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>

    <div class="a4-container">
        <div class="doc-number kh"><span id="printDocNumber">លេខ: —</span></div>

        <div class="title-block">
            <h1 class="moul title-kh">លិខិតបញ្ជាក់ការសិក្សា</h1>
            <p class="title-en">Student Status Certification</p>
            <div class="title-rule"><i></i></div>
        </div>

        <div class="content-grid">
            {{-- Khmer --}}
            <section>
                <p class="moul lead kh-lead">សាកលវិទ្យាល័យវេស្ទើន បញ្ជាក់ថា៖</p>
                <dl class="fields kh">
                    <dt>ឈ្មោះ</dt><dd><span id="printNameKh" class="name-kh">—</span></dd>
                    <dt>ភេទ</dt><dd id="printSexKh">—</dd>
                    <dt>អត្តលេខ</dt><dd id="printCodeKh">—</dd>
                    <dt>សញ្ជាតិ</dt><dd id="printNationalityKh">—</dd>
                    <dt>ថ្ងៃខែឆ្នាំកំណើត</dt><dd id="printDobKh">—</dd>
                    <dt>ទីកន្លែងកំណើត</dt><dd id="printBirthPlaceKh">—</dd>
                    <dt>អាសយដ្ឋានបច្ចុប្បន្ន</dt><dd id="printAddressKh">—</dd>
                </dl>
                <p class="statement kh">
                    ជានិស្សិតកំពុងសិក្សាថ្នាក់ <b id="printDegreeKh"></b>
                    ឯកទេស <b id="printMajorKh"></b>
                    ឆ្នាំទី<span id="printYearKh"></span> ឆមាសទី<span id="printSemesterKh"></span>
                    ឆ្នាំសិក្សា <span id="printAcademicYearKh"></span>
                    នៅ <b>សាកលវិទ្យាល័យវេស្ទើន</b> ពិតប្រាកដមែន។
                    លិខិតបញ្ជាក់នេះចេញឲ្យសាមីខ្លួនប្រើប្រាស់តាមការដែលអាចប្រើបាន។
                </p>
            </section>

            <div class="divider"></div>

            {{-- English --}}
            <section>
                <p class="lead en-lead">This is to certify that:</p>
                <dl class="fields">
                    <dt>Name</dt><dd><span id="printNameEn" class="name-en">—</span></dd>
                    <dt>Sex</dt><dd id="printSexEn">—</dd>
                    <dt>ID</dt><dd id="printCodeEn">—</dd>
                    <dt>Nationality</dt><dd id="printNationalityEn">—</dd>
                    <dt>Date of Birth</dt><dd id="printDobEn">—</dd>
                    <dt>Place of Birth</dt><dd id="printBirthPlaceEn">—</dd>
                    <dt>Current Address</dt><dd id="printAddressEn">—</dd>
                </dl>
                <p class="statement">
                    is currently pursuing year <span id="printYearEn"></span>,
                    semester <span id="printSemesterEn"></span>
                    <b id="printDegreeEn"></b>, majoring in <b id="printMajorEn"></b>,
                    <span id="printAcademicYearEn"></span>, at <b>WESTERN UNIVERSITY</b>.
                    This certification is presented to the bearer with all rights and
                    privileges thereto pertaining.
                </p>
            </section>
        </div>

        <div class="signature">
            <div id="printFullDateKh" class="kh date-kh"></div>
            <div id="printShortDateKh" class="kh date-kh"></div>
            <div id="printIssueDateEn" class="date-en"></div>
            <div class="moul role-kh">សាកលវិទ្យាធិការ</div>
            <div class="role-en">PRESIDENT</div>
            <div class="sign-space"></div>
            <div class="kh signer">បណ្ឌិត កៀង រតនា</div>
        </div>
    </div>
</div>
