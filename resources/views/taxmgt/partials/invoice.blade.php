<style>
@media print {
    /* 1. Page Setup */
    @page { 
        size: landscape; 
        margin: 0mm; 
    }

    /* 2. Hide everything except print area */
    body * { visibility: hidden; }

    /* 3. Strip layout UI */
    .sidebar, .main-sidebar, .nav-container, .topbar, .no-print, 
    [class*="sidebar"], [class*="nav"], header, footer, aside {
        display: none !important;
    }

    /* 4. Reset Wrapper for Clean Print */
    html, body, .content-wrapper, .main-content, .page-wrapper, main {
        visibility: visible !important;
        margin: 0 !important;
        padding: 0 !important;
        display: block !important;
        width: 100% !important;
        height: auto !important;
        overflow: visible !important;
    }

    /* 5. The Print Area */
    #print-tax-area {
        visibility: visible !important;
        display: block !important;
        width: 100% !important;
        margin: 0 !important;
        padding: 10mm !important;
        background: white !important;
    }

    #print-tax-area * {
        visibility: visible !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    .invoice-card {
        page-break-inside: avoid;
        break-inside: avoid;
    }
  }

  /* Status Stamp */
  .status-stamp {
    position: absolute;
    top: 40%;
    left: 10%;
    font-size: 4rem;
    font-weight: 900;
    text-transform: uppercase;
    transform: rotate(-15deg);
    pointer-events: none;
    z-index: 0;
    padding: 1rem;
    border: 8px double currentColor;
    opacity: 0.1;
    display: none;
  }
  .stamp-paid { color: #059669; display: block; }
</style>

<div id="print-tax-area" class="hidden print:block font-inter bg-slate-50 min-h-screen">
    <div class="flex flex-row justify-between gap-4">
        @foreach([1, 2] as $copy)
        <div class="invoice-card relative w-1/2 p-6 bg-white border border-gray-300 shadow-sm flex flex-col min-h-[700px]">
            
            <div class="status-stamp" id="p-tax-stamp-{{ $copy }}">PAID</div>

            <div class="relative z-10 flex flex-col flex-grow">
                <div class="flex justify-between items-start border-b-2 border-slate-900 pb-3">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-14 w-auto">
                        <div>
                            <h2 class="text-lg font-bold text-[#b1a166]">សណ្ឋាគារកែប ស៊ីវ្យូ</h2>
                            <h2 class="text-[10px] text-slate-600 font-bold uppercase">Kep Sea View Hotel & Skybar</h2>
                            <p class="text-[9px] text-slate-500 font-medium">VAT TIN: K008-902203445</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <h1 class="text-2xl font-black text-indigo-900 uppercase">វិក្កយបត្រពន្ធ</h1>
                        <h2 class="text-sm font-bold text-indigo-800 -mt-1 uppercase">TAX INVOICE</h2>
                        <div class="mt-1 text-[9px] text-slate-500">
                            <p>លេខកូដ / No: <span id="p-tax-no-{{ $copy }}" class="font-bold text-red-600"></span></p>
                            <p>កាលបរិច្ឆេទ / Date: <span>{{ date('d-M-Y') }}</span></p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 mt-4 text-[10px] gap-4 mb-4">
                    <div class="border-l-2 border-indigo-500 pl-2">
                        <span class="block font-black text-indigo-900 uppercase text-[8px]">Bill To / អតិថិជន</span>
                        <p class="font-bold text-sm" id="p-tax-customer-{{ $copy }}"></p>
                        <p id="p-tax-address-{{ $copy }}" class="text-slate-600"></p>
                        <p class="font-bold">VAT-TIN: <span id="p-tax-vattin-{{ $copy }}"></span></p>
                    </div>
                    <div class="text-right text-slate-500 text-[9px]">
                        <p class="font-bold text-slate-800">Address:</p>
                        <p>National Road #33A, Kaeb Town, Kep</p>
                        <p>Tel: 077 636 065 | Kepseaview@gmail.com</p>
                    </div>
                </div>

                <table class="w-full text-[10px] border-collapse ">
                    <thead>
                        <tr class="bg-indigo-900 text-white uppercase font-bold">
                            <th class="p-2 text-left">Description / ការពិពណ៌នា</th>
                            <th class="p-2 text-center">Qty / ចំនួន</th>
                            <th class="p-2 text-right">Price / តម្លៃរាយ</th>
                            <th class="p-2 text-right">Total / សរុប</th>
                        </tr>
                    </thead>
                    <tbody id="p-tax-items-{{ $copy }}" class="divide-y divide-slate-100">
                        </tbody>
                </table>

                <div class="mt-4 pt-4 border-t-2 border-slate-100">
                    <div class="flex justify-end">
                        <div class="w-3/5 space-y-1">
                            <div class="flex justify-between text-[10px]">
                                <span class="text-slate-600">តម្លៃសរុប (Sub-Total)</span>
                                <span id="p-tax-base-{{ $copy }}" class="font-bold"></span>
                            </div>
                            <div class="flex justify-between text-[10px]">
                                <span class="text-slate-600">អាករស្នាក់នៅ (AT Tax)</span>
                                <span id="p-tax-extra-{{ $copy }}" class="font-bold"></span>
                            </div>
                            <div class="flex justify-between text-[10px]">
                                <span class="text-slate-600">អាករលើតម្លៃបន្ថែម (VAT 10%)</span>
                                <span id="p-tax-vat-{{ $copy }}" class="font-bold"></span>
                            </div>
                            <div class="border-t-2 border-indigo-900 flex justify-between bg-indigo-900 text-white font-bold px-2 py-1 rounded mt-1">
                                <span class="text-xs">សរុបរួម (GRAND TOTAL)</span>
                                <span id="p-tax-grand-total-{{ $copy }}" class="text-xs"></span>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between items-center mt-8">
                        <div class="text-center">
                            <div class="w-24 border-t border-slate-400 mx-auto"></div>
                            <p class="text-[8px] mt-1 uppercase font-bold text-slate-500">Customer Signature</p>
                        </div>
                        <div class="text-right">
                             <p class="text-[8px] text-slate-400 uppercase">Printed At: {{ date('H:i') }}</p>
                             <p class="text-[8px] text-slate-400">© 2026 KEP SEA VIEW HOTEL</p>
                        </div>
                        <div class="text-center">
                            <div class="w-24 border-t border-slate-400 mx-auto"></div>
                            <p class="text-[8px] mt-1 uppercase font-bold text-slate-500">Seller Signature</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>