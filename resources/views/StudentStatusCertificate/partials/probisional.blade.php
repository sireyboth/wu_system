<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Status Certification</title>
    <link href="https://fonts.googleapis.com/css2?family=Khmer&family=Moul&family=Times+New+Roman&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">

</head>

      @font-face {
    font-family: 'Tacteing';
    src: local('Tacteing'), url('Tacteing.ttf') format('truetype');
}

@page {
    size: A4;
    margin: 0;
}

body {
    margin: 0;
    padding: 0;
    background-color: #f0f0f0;
    font-family: 'Times New Roman', serif;
    font-size: 14px;
    color: #000;
}

.a4-container {
    width: 210mm;
    height: 297mm;
    margin: 12mm auto;
    background: #fff;
    padding: 15mm 10mm;
    box-sizing: border-box;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
    position: relative;
}

.khmer-moul {
    font-family: 'Khmer OS Muol Light', 'Moul', cursive;
    font-size: 24px;
    font-weight: normal;
}

.khmer-regular {
    font-family: 'Khmer', sans-serif;
    font-size: 14px;
}

.eng-bold {
    font-weight: bold;
    font-size: 16px;
    font-family: 'Times New Roman';
}

.print-btn {
    position: fixed;
    top: 20px;
    left: 20px;
    padding: 10px 20px;
    background-color: #007acc;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-family: sans-serif;
    font-size: 14px;
    z-index: 999;
}

@media print {
    body { background-color: #fff; }
    .a4-container {
        margin: 0;
        box-shadow: none;
    }
    .print-btn { display: none; }
}
        .print-btn:hover { background-color: #005999; }

        /* Document Reference Section */
        .doc-number {
            font-size: 13px;
            margin-bottom: 30px;
        }

        /* Main Titles */
        .title-block {
            text-align: center;
            margin-bottom: 1px;
        }
        .title-block h1 {
            margin: 1px 1px 0px 0px;
        }


        /*.divider {
            width: 150px;
            height: 1px;
            background-color: #000;
            margin: 15px auto;
            position: relative;
        }
        /*.divider::after {
            content: "❖";
            position: absolute;
            top: -9px;
            left: 50%;
            transform: translateX(-50%);
            background: #fff;
            padding: 0 5px;
            font-size: 10px;
        }

        /* Split Columns Grid for Content */
        .content-grid {
            display: flex;
            justify-content: space-between;
            margin-top: 5px;
        }
        .column {
            width: 48%;
        }

        /* Row item adjustments */
        .info-row {
            display: flex;
            margin-bottom: 3px;
            align-items: baseline;
            font-family: 'KHMER OS BATTAMBANG';
            font-size: 15px;

        }

        .info-row-eng {
            display: flex;
            margin-bottom: 7px;
            align-items: baseline;
            font-family: 'Times New Roman';
            font-size: 16px;
        }
        .label {
            width: 130px;
            flex-shrink: 0;
        }
        .value_name {
            flex-grow: 1;
            font-family: 'Kh Muol';
            font-size: 15PX;
        }
        .value_name_ENG {
            flex-grow: 1;
            font-family: 'Times New Roman';
            font-size: 17px;

        }
        .value_major{
            font-family: 'khmer os muol light';
            font-style: if(.khmer-regular);
        }
        .value {

            font-family: 'KHMER OS BATTAMBANG';
            font-size: 15px;

        }
        //is currently pursuing yea..... 2025-2026, at WESTERN UNIVERSITY
        .valueeng {
            font-family: 'Times New Roman';
            font-size: 18px;
        }
        /* Bottom Footer / Signatures */
        .footer-section {
            margin-top: 5px;
            text-align: center;
            line-height: 1.6;
            padding-left: 320px;
            font-family: 'KHMER OS BATTAMBANG';

        }
        /*.signature-container {
            margin-top: 5px;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            text-align: center;
            padding-right:120px;
        }*/
        .signature-space {
            height: 50px; /* Space for physical stamp or signature */
        }
        .signee-name {

            padding-left: 80px;
            font-family: 'Kh Muol';

        }

        /* Hide interface elements when printing to PDF/Paper */
        @media print {
            body { background-color: #fff; }
            .a4-container {
                margin: 0;
                box-shadow: none;
            }
            .print-btn { display: none; }
        }
    </style>

<body>

    <button class="print-btn" onclick="triggerPrint()">Print Document / Save PDF</button>

    <div class="a4-container">

        <div class="doc-number khmer-regular" style="margin-top: 3.5cm;">
            លេខ: ០៥១៨/២៦ ស.វ/ក.ស
        </div>

        <div class="title-block">
            <h1 class="khmer-moul" style="font-family: 'Kh Muol'">លិខិតបញ្ជាក់ការសិក្សា</h1>
            <h1 class="eng-bold" style="font-size: 24px; font-family: 'Old English Text MT';">Student Status Certification</h1>
            <div><img src="E:/Coding/tacteing.png" width="150px" style="margin-top: 10PX;;"></div>
        </div>

        <div class="content-grid">

            <div class="column khmer-regular">
                <p class="khmer-moul" style="font-size: 16px; margin-bottom: 18px; padding-left: 10px;">សាកលវិទ្យាល័យវេស្ទើន បញ្ជាក់ថា៖</p>

                <div class="info-row"><div class="label">ឈ្មោះ:</div><div class="value_name">មិនមាន</div></div>
                <div class="info-row"><div class="label">ភេទ:</div><div class="value">ស្មិនមាន</div></div>
                <div class="info-row"><div class="label">អត្តលេខ:</div><div class="value">មិនមាន</div></div>
                <div class="info-row"><div class="label">សញ្ជាតិ:</div><div class="value">មិនមាន</div></div>
                <div class="info-row"><div class="label">ថ្ងៃខែឆ្នាំកំណើត:</div><div class="value">មិនមាន</div></div>
                <div class="info-row"><div class="label">ទីកន្លែងកំណើត:</div><div class="value">មិនមាន</div></div>
                <div class="info-row"><div class="label">អាសយដ្ឋានបច្ចុប្បន្ន:</div><div class="value">មិនមាន</div></div>

                <p class="value" style="text-align:justify;">ជានិស្សិតកំពុងសិក្សាឆ្នាំទី៣ ឆមាសទី២ ថ្នាក់ <span class="value_major">បរិញ្ញាបត្ររង</span> ឯកទេស <span class="value_major">គ្រប់គ្រង</span> ឆ្នាំសិក្សា ២០២៥ - ២០២៦ នៅ <span class="value_major">សាកលវិទ្យាល័យ វេស្ទើន</span> ពិតប្រាកដមែន។
                </p>
                <p style="margin-top: 20px; font-family: 'khmer os battambang'; font-size: 16px;">
                    លិខិតបញ្ជាក់នេះចេញឱ្យសាមីខ្លួនប្រើប្រាស់តាមការដែលអាចប្រើប្រាស់បាន។
                </p>
            </div>

            <div class="column">
                <p class="eng-bold" style="font-size: 20px; margin-bottom: 24px; padding-left: 40px;">This is to certify that:</p>

                <div class="info-row-eng"><div class="label">Name:</div><div class="value-name-eng""><strong> KHORN SIVEY</strong></div></div>
                <div class="info-row-eng"><div class="label">Sex:</div><div class="info-row-eng">Female</div></div>
                <div class="info-row-eng"><div class="label">ID:</div><div class="info-row-eng">0013919</div></div>
                <div class="info-row-eng"><div class="label">Nationality:</div><div class="info-row-eng">Khmer</div></div>
                <div class="info-row-eng"><div class="label">Date of Birth:</div><div class="info-row-eng">September 22, 2004</div></div>
                <div class="info-row-eng"><div class="label">Place of Birth:</div><div class="info-row-eng">Battambang Province</div></div>
                <div class="info-row-eng"><div class="label">Current Address:</div><div class="info-row-eng">Phnom Penh</div></div>

                <p class="value-name-eng" style="line-height: 1.75; Margin-top: 12px; text-align: justify; font-size: 17px;">
                    is currently pursuing year 4, semester 2, <strong>Associate Degree</strong>, majoring in <strong>Management </strong>, 2025-2026, at <strong>WESTERN UNIVERSITY</strong>.
                </p>
                <p style=" font-size: 17px; line-height: 1.75;">
                    This certification is presented to the bearer with all rights and privileges thereto pertaining.
                </p>
            </div>

        </div>

        <div class="footer-section">
            <div style="font-size: 13px;">ថ្ងៃសុក្រ ៦កើត ខែជេស្ឋ ឆ្នាំមមី អដ្ឋស័ក ព.ស.២៥៧០</div>
            <div style="font-family: 'Times New Roman'; font-size: 14px;">Phnom Penh, May 22, 2026</div>

            <div class="signature-container">
                <div class="khmer-moul" style="font-size: 14px; margin-top: 5px;">សាកលវិទ្យាធិការ</div>
                <div class="eng-bold" style="font-family: 'Times New Roman'; margin-bottom: 5px;">PRESIDENT</div>
                <div class="signature-space"></div>
                <div class="signee-name">បណ្ឌិត កៀង រតនា</div>
            </div>
        </div>

    </div>

    <script>
        function triggerPrint() {
            // Simply calls the window landscape/portrait printing workflow built-in to browsers
            window.print();
        }
    </script>
</body>
</html>
