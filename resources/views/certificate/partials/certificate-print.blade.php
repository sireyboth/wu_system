<div id="certificatePrintArea" class="hidden">
    <link href="https://fonts.googleapis.com/css2?family=Moul&family=Battambang:wght@400;700&display=swap" rel="stylesheet">
    <style>
        #certificatePrintArea {
            font-family: 'Times New Roman', serif;
            font-size: 14px;
            color: #000;
        }
        #certificatePrintArea .khmer-text {
            font-family: 'Khmer OS Battambang', 'Battambang', sans-serif;
        }
        #certificatePrintArea .a4-container {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            background: #fff;
            padding: 15mm 10mm;
            box-sizing: border-box;
            position: relative;
        }
        #certificatePrintArea .khmer-moul { font-family: 'Khmer OS Muol Light', 'Moul', cursive; font-size: 24px; }
        #certificatePrintArea .info-row .label,
        #certificatePrintArea .info-row > div { font-family: 'Khmer OS Battambang', 'Battambang', sans-serif; }
        #certificatePrintArea .eng-bold { font-weight: bold; font-size: 16px; }
        #certificatePrintArea .doc-number { font-size: 13px; margin-bottom: 30px; margin-top: 3.5cm; }
        #certificatePrintArea .title-block { text-align: center; margin-bottom: 1px; }
        #certificatePrintArea .content-grid { display: flex; justify-content: space-between; margin-top: 5px; }
        #certificatePrintArea .column { width: 48%; }
        #certificatePrintArea .info-row,
        #certificatePrintArea .info-row-eng { display: flex; margin-bottom: 5px; align-items: baseline; font-size: 15px; }
        #certificatePrintArea .label { width: 130px; flex-shrink: 0; }
        #certificatePrintArea .footer-section { margin-top: 40px; text-align: center; line-height: 1.6; padding-left: 320px; }
        #certificatePrintArea .signature-space { height: 50px; }

        @media print {
            body * { visibility: hidden; }
            #certificatePrintArea, #certificatePrintArea * { visibility: visible; }
            #certificatePrintArea.hidden { display: block !important; }
            #certificatePrintArea { position: absolute; top: 0; left: 0; }
        }
    </style>

    <div class="a4-container">
        <div class="doc-number" id="printDocNumber">លេខ: —</div>

        <div class="title-block">
            <h1 class="khmer-moul">លិខិតបញ្ជាក់ការសិក្សា</h1>
            <h1 class="eng-bold" style="font-size:24px;">Student Status Certification</h1>
        </div>

        <div class="content-grid">
            <div class="column">
                <p class="khmer-moul" style="font-size:16px;margin-bottom:18px;">សាកលវិទ្យាល័យវេស្ទើន បញ្ជាក់ថា៖</p>
                <div class="info-row"><div class="label">ឈ្មោះ:</div><div id="printNameKh">—</div></div>
                <div class="info-row"><div class="label">ភេទ:</div><div id="printSexKh">—</div></div>
                <div class="info-row"><div class="label">អត្តលេខ:</div><div id="printCodeKh">—</div></div>
                <div class="info-row"><div class="label">សញ្ជាតិ:</div><div id="printNationalityKh">—</div></div>
                <div class="info-row"><div class="label">ថ្ងៃខែឆ្នាំកំណើត:</div><div id="printDobKh">—</div></div>
                <div class="info-row"><div class="label">អាសយដ្ឋានបច្ចុប្បន្ន:</div><div id="printAddressKh">—</div></div>
                <p class="khmer-text" style="text-align:justify;margin-top:15px;">
                    ជានិស្សិតកំពុងសិក្សាថ្នាក់ <b id="printDegreeKh"></b> ឯកទេស <b id="printMajorKh"></b>
                    នៅ <b>សាកលវិទ្យាល័យ វេស្ទើន</b> ពិតប្រាកដមែន។
                </p>
            </div>

            <div class="column">
                <p class="eng-bold" style="font-size:20px;margin-bottom:24px;">This is to certify that:</p>
                <div class="info-row-eng"><div class="label">Name:</div><div><strong id="printNameEn">—</strong></div></div>
                <div class="info-row-eng"><div class="label">Sex:</div><div id="printSexEn">—</div></div>
                <div class="info-row-eng"><div class="label">ID:</div><div id="printCodeEn">—</div></div>
                <div class="info-row-eng"><div class="label">Nationality:</div><div id="printNationalityEn">—</div></div>
                <div class="info-row-eng"><div class="label">Date of Birth:</div><div id="printDobEn">—</div></div>
                <div class="info-row-eng"><div class="label">Current Address:</div><div id="printAddressEn">—</div></div>
                <p style="text-align:justify;margin-top:15px;">
                    is currently pursuing <strong id="printDegreeEn"></strong>, majoring in <strong id="printMajorEn"></strong>,
                    at <strong>WESTERN UNIVERSITY</strong>.
                </p>
            </div>
        </div>

        <div class="footer-section">
            <div id="printFullDateKh" class="khmer-text"></div>
            <div id="printIssueDateEn"></div>
            <div class="khmer-moul" style="font-size:14px;margin-top:20px;">សាកលវិទ្យាធិការ</div>
            <div class="eng-bold">PRESIDENT</div>
            <div class="signature-space"></div>
            <div class="khmer-text">បណ្ឌិត កៀង រតនា</div>
        </div>
    </div>
</div>
