<!DOCTYPE html>
<html lang="km">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Provisional Certificate</title>
  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Google Fonts for Gothic style and clean Khmer/Latin text -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:ital,wght@0,100..0,700;1,100..0,700&family=Khmer&family=UnifrakturMaguntia&display=swap" rel="stylesheet">

  <style>
    /* Absolute A4 dimensions and print setups */
    @page {
      size: A4;
      margin: 0;
    }
    body {
      background-color: #f3f4f6;
      margin: 0;
      padding: 0;
    }
    .a4-page {
      width: 210mm;
      height: 297mm;
      background-color: white;
      margin: 20px auto;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      box-sizing: border-box;
      position: relative;
    }
    @media print {
      body {
        background-color: white;
      }
      .a4-page {
        margin: 0;
        box-shadow: none;
      }
    }

    /* Custom font classes mapped to image styles */
    .font-gothic {
      font-family: 'UnifrakturMaguntia', serif;
    }
    .font-khmer-muol {
      font-family: 'Khmer', serif; /* Simulating formal bold Khmer header script */
      font-weight: 700;
    }
    .font-certificate-content {
      font-family: 'Times New Roman', 'Kantumruy Pro', serif;
      font-size: 10pt; /* Explicitly forced user requirement */
      line-height: 1.6;
    }
  </style>
</head>
<body class="p-4">

  <!-- A4 Container Document wrapper -->
  <div class="a4-page px-[30mm] pt-[45mm] pb-[30mm] flex flex-col justify-between font-certificate-content text-gray-900 selection:bg-amber-100">

    <!-- Top Content Section -->
    <div>
      <!-- Reference Number Header Top-Left -->
      <div class="text-[9pt] font-sans mb-8">
        លេខ: ០៤៨៧/២៦ ស.វ.គ.ស
      </div>

      <!-- Main Headers -->
      <div class="text-center space-y-1 mb-10">
        <h1 class="font-khmer-muol text-lg tracking-wide">វិញ្ញាបនបត្របណ្តោះអាសន្ន</h1>
        <h2 class="font-gothic text-2xl font-normal leading-none">Provisional Certificate</h2>
        <p class="text-xs font-bold pt-1">វគ្គ</p>
      </div>

      <!-- Main Columns System: Perfect aligned vertical rows -->
      <div class="grid grid-cols-2 gap-x-12 items-start">

        <!-- Left Side Column: Khmer Version -->
        <div class="space-y-2 text-justify">
          <div class="font-khmer-muol text-[11pt] mb-3">សាកលវិទ្យាល័យបញ្ញាសាស្ត្រ បញ្ជាក់ថា៖</div>

          <div class="grid grid-cols-[100px_1fr] items-baseline">
            <span class="font-semibold">ឈ្មោះ</span>
            <span>: <strong class="font-khmer-muol text-[11pt] ml-1">ចេង យូភីង</strong></span>
          </div>

          <div class="grid grid-cols-[100px_1fr] items-baseline">
            <span>ភេទ</span>
            <span>: ស្រី</span>
          </div>

          <div class="grid grid-cols-[100px_1fr] items-baseline">
            <span>សញ្ជាតិ</span>
            <span>: ខ្មែរ</span>
          </div>

          <div class="grid grid-cols-[100px_1fr] items-baseline">
            <span>ថ្ងៃខែឆ្នាំកំណើត</span>
            <span>: ថ្ងៃទី២៤ ខែសីហា ឆ្នាំ២០០៤</span>
          </div>

          <div class="grid grid-cols-[100px_1fr] items-baseline">
            <span>ទីកន្លែងកំណើត</span>
            <span>: រាជធានីភ្នំពេញ</span>
          </div>

          <p class="pt-2 text-indent-sm">
            បានជោគជ័យគ្រប់លក្ខខណ្ឌសាកលវិទ្យាល័យ ដើម្បីទទួលបានសញ្ញាបត្រ <strong class="font-khmer-muol">បរិញ្ញាបត្ររង</strong> ជំនាញ <strong class="font-khmer-muol text-[10.5pt]">គណនេយ្យ</strong>។
          </p>

          <p >
            វិញ្ញាបនបត្រនេះចេញជូនសាមីខ្លួនយកទៅប្រើប្រាស់តាមការអាចចៀសបាន។
          </p>

          <p class="text-[9.5pt] pt-2 text-gray-700 italic">
            សម័យប្រឡង៖ ថ្ងៃទី២១ ខែកញ្ញា ឆ្នាំ២០២៤
          </p>
        </div>

        <!-- Right Side Column: English Version -->
        <div class="space-y-2 text-justify">
          <div class="font-bold text-[10.5pt] mb-3">This is to certify that:</div>

          <div class="grid grid-cols-[90px_1fr] items-baseline">
            <span>Name</span>
            <span>: <strong class="font-sans uppercase font-bold tracking-wide text-[9.5pt] ml-1">CHENG YOUPHING</strong></span>
          </div>

          <div class="grid grid-cols-[90px_1fr] items-baseline">
            <span>Sex</span>
            <span>: Female</span>
          </div>

          <div class="grid grid-cols-[90px_1fr] items-baseline">
            <span>Nationality</span>
            <span>: Khmer</span>
          </div>

          <div class="grid grid-cols-[90px_1fr] items-baseline">
            <span>Date of Birth</span>
            <span>: August 24, 2004</span>
          </div>

          <div class="grid grid-cols-[90px_1fr] items-baseline">
            <span>Place of Birth</span>
            <span>: Phnom Penh</span>
          </div>

          <p class="">
            has successfully fulfilled all the requirements of the university to be awarded the <strong class="font-serif font-bold text-[10.5pt]">Associate Degree</strong>, majoring in <strong class="font-serif font-bold text-[10.5pt]">Accounting</strong>.
          </p>

          <p class="pt-1">
            This certificate is presented to the bearer with all rights and privileges thereto pertaining.
          </p>

          <p class="text-[9.5pt] pt-2 text-gray-700">
            Date of comprehensive exam: September 21, 2024
          </p>
        </div>

      </div>

      <!-- Date and Signature block aligned exactly to center-right layout -->
      <div class="mt-8 flex flex-col items-center ml-auto w-1/2 text-center space-y-1">
        <p class="text-[9.5pt] font-sans">Phnom Penh, May 19, 2026</p>
        <p class="font-khmer-muol text-[10pt]">សាកលវិទ្យាធិការ</p>
        <p class="font-bold tracking-wider text-[9.5pt] uppercase font-serif">President</p>

        <!-- Name spacing placeholder simulating signature space -->
        <div class="h-16"></div>

        <p class="font-khmer-muol text-[11pt]">បណ្ឌិត គង់ នរា</p>
      </div>

    </div>

    <!-- Bottom Footer Section: Photo frame placeholder & Notes -->
    <div class="flex justify-between items-end border-t border-transparent pt-4">

      <!-- Photo Frame Placement Left -->
      <div class="text-center space-y-2">
        <div class="w-[30mm] h-[40mm] border-2 border-gray-400 border-dashed flex flex-col items-center justify-center bg-gray-50 text-gray-400">
          <span class="text-sm font-bold">4 x 6</span>
        </div>
      </div>

      <!-- Footnote warning notice Right-bottom aligned -->
      <div class="text-right text-[8.5pt] text-gray-500 max-w-[60%] space-y-0.5 leading-tight">
        <p><strong class="font-khmer-muol text-gray-700">កំណត់សំគាល់៖</strong> វិញ្ញាបនបត្រនេះចេញជូនតែម្តងគត់។</p>
        <p><strong class="font-bold font-serif text-gray-700">Note:</strong> This certificate is issued once only.</p>
      </div>

    </div>

  </div>

</body>
</html>
