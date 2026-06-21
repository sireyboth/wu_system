<style>
  @media print {
    /* 1. Page Setup */
    @page { 
        size: landscape; 
        margin: 0mm; 
    }

    /* 2. Hide everything by default using the 'Nuclear' approach */
    body * {
        visibility: hidden;
    }

    /* 3. Explicitly kill the sidebar and nav from the layout flow */
    .sidebar, .main-sidebar, .nav-container, .topbar, .no-print, 
    [class*="sidebar"], [class*="nav"], header, footer, aside {
        display: none !important;
    }

    /* 4. Ensure the parent containers of the print area don't have margins/padding */
    html, body, .content-wrapper, .main-content, .page-wrapper, main {
        visibility: visible !important;
        margin: 0 !important;
        padding: 0 !important;
        display: block !important;
        width: 100% !important;
        height: auto !important; /* Allow growing for multiple pages */
        overflow: visible !important;
    }

    /* 5. The Print Area: No absolute positioning (to allow multi-page flow) */
    #print-area {
        visibility: visible !important;
        display: block !important;
        width: 100% !important;
        margin: 0 !important;
        padding: 20px !important; /* Give it some breathing room */
        background: white !important;
    }

    #print-area * {
        visibility: visible !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    /* 6. Prevent the invoice card from being split awkwardly mid-way */
    .invoice-card {
        page-break-inside: avoid;
        break-inside: avoid;
    }
  }

  /* Keep your existing Stamp Styling below */
  .status-stamp {
    position: absolute;
    top: 45%;
    left: 15%;
    font-size: 5rem;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: 0.5rem;
    transform: rotate(-20deg);
    pointer-events: none;
    z-index: 0;
    padding: 1rem 2rem;
    border: 10px double currentColor;
    border-radius: 1rem;
    opacity: 0.15;
    display: none;
  }
  .stamp-paid { color: #059669; display: block; }
  .stamp-cancelled { color: #dc2626; display: block; }
  .stamp-pending { color: #d97706; display: block; }
</style>

<div id="print-area" class="hidden print:block p-6 font-inter bg-slate-50 min-h-screen">
    <div class="flex flex-row justify-between gap-6">
        @foreach([1, 2] as $copy)
        <div class="invoice-card relative w-1/2 p-8 bg-white border border-gray-200 shadow-sm overflow-hidden flex flex-col  min-h-[750px]">
            <div class="status-stamp" id="p-stamp-{{ $copy }}">PAID</div>
              <div class="relative z-10">
                <div class="flex justify-between items-center border-b-2 border-slate-900 pb-4">
                  <div class="flex items-center">
                      <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-16 w-auto">
                      <div >
                          <h2 class="text-xl font-bold text-[#b1a166] leading-tight">សណ្ឋាគារកែប ស៊ីវ្យូ</h2>
                          <h2 class="text-xs mt-2 text-slate-600 ">Kep Sea View Hotel & Skybar</h2>
                      </div>
                  </div>

                  <div class="text-right">
                      <h1 class="text-4xl font-black text-indigo-900 tracking-tighter uppercase ">Invoice</h1>
                      <div class="mt-1 text-[8px] uppercase tracking-widest text-slate-500 flex flex-col items-end">
                          <p>REF: <span id="p-invoice-no-{{ $copy }}" class="text-indigo-900">#0000</span></p>
                          <p>Date Issue: <span class="text-indigo-900">{{ date('d M Y') }}</span></p>
                      </div>
                  </div>
              </div>

              
                <div class="flex justify-between mt-2 mb-4 text-[11px] leading-relaxed">
                    <div class="w-1/2 text-left">
                        <span class="block text-[9px] font-black uppercase text-indigo-900 mb-1">Bill To:</span>
                        <p class="font-bold text-slate-900 text-sm" id="p-customer-name-{{ $copy }}"></p>
                        <p id="p-customer-contact-{{ $copy }}"></p>
                    </div>
                    <div class="w-1/2 text-right">
                        <span class="block text-[9px] font-black uppercase text-indigo-900 mb-1">From:</span>
                        <p class="font-bold text-slate-800">Kep Sea View Hotel</p>
                        <p>National Road #33A, Kaeb Town, Kep</p>
                        <p class="font-medium">Tel: 077 636 065 | Kepseaview@gmail.com</p>
                    </div>
                </div>

                <div class="grid grid-cols-3 border border-slate-200 rounded-md mb-4 bg-slate-50">
                    <div class="p-3 border-r border-slate-200">
                        <span class="block text-[8px] font-black uppercase text-slate-400">Check In</span>
                        <p class="font-bold text-slate-800 text-xs" id="p-check-in-{{ $copy }}"></p>
                    </div>
                    <div class="p-3 border-r border-slate-200 text-center">
                        <span class="block text-[8px] font-black uppercase text-slate-400">Check Out</span>
                        <p class="font-bold text-slate-800 text-xs" id="p-check-out-{{ $copy }}"></p>
                    </div>
                    <div class="p-3 text-right">
                        <span class="block text-[8px] font-black uppercase text-slate-400">Nights</span>
                        <p class="font-bold text-slate-800 text-xs" id="p-nights-{{ $copy }}"></p>
                    </div>
                </div>

                <table class="w-full text-[11px] mb-2">
                    <thead>
                        <tr class="bg-indigo-900 text-white uppercase tracking-tighter">
                            <th class="p-2 text-left">Description</th>
                            <th class="p-2 text-center">Food Price</th>
                            <!-- <th class="p-2 text-center">Qty</th> -->
                            <th class="p-2 text-right">Unit Price</th>
                            <th class="p-2 text-right">Discount</th>
                            <th class="p-2 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody id="p-items-list-{{ $copy }}" class="divide-y divide-slate-100"></tbody>
                </table>
            </div>

            <div class="relative z-10">
                <div class="flex justify-between items-end mb-6">
                    <div class="w-1/3">
                         <span class="block text-[8px] font-black uppercase text-slate-400 mb-1">Status</span>
                         <p class="text-[10px] font-bold text-slate-700 italic" id="p-status-text-{{ $copy }}"></p>
                    </div>
                    <div class="w-1/2 space-y-1">
                        <div class="font-bold flex justify-between text-xs px-2 text-indigo-900">
                            <span>Sub-Total</span>
                            <span id="p-subtotal-{{ $copy }}" class="font-bold"></span>
                        </div>
                        <div class="font-bold flex justify-between text-xs px-2 text-indigo-900">
                            <span>Booking-Price (-)</span>
                            <span id="p-booking-{{ $copy }}" class="font-bold"></span>
                        </div>
                        <div class="flex justify-between text-xs font-bold px-2 text-indigo-900 rounded mt-2">
                            <span>GRAND TOTAL</span>
                            <span id="p-grand-total-{{ $copy }}" class="font-black"></span>
                        </div>
                    </div>
                </div>

                <div class="flex justify-between items-center pt-10">
                    <div class="w-40 border-t border-slate-300 text-center pt-2 text-[9px] uppercase text-slate-400">
                        Customer Signature
                    </div>
                    <div class="text-[8px] text-slate-400 text-right">
                        <p>© 2026 KEP SEA VIEW HOTEL & SKYBAR</p>
                        <p>Printed: {{ date('H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>