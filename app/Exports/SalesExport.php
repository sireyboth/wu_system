<?php

namespace App\Exports;

use App\Models\SaleMGT;
use Maatwebsite\Excel\Concerns\{FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithCustomStartCell, WithEvents};
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\{Fill, Border, Alignment};

class SalesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithCustomStartCell, WithEvents
{
    public function collection()
    {
        // Load Sales with their Items (Eager Loading)
        return SaleMGT::with('items')->orderBy('created_at', 'desc')->get();
    }

    public function startCell(): string { return 'A5'; }

    public function headings(): array
    {
        return [
            'INV #', 'CUSTOMER', 'CONTACT', 'ROOM NO', 'ROOM TYPE', 
            'UNIT PRICE', 'FOOD PRICE', 'DISC %', 'ITEM TOTAL', 'STATUS', 'NOTE'
        ];
    }

    /**
     * The Magic: We return a nested array to create multiple rows per Sale
     */
    public function map($sale): array
    {
        $rows = [];
        foreach ($sale->items as $item) {
            $rows[] = [
                $sale->invoice_no,
                $sale->cus_first_name . ' ' . $sale->cus_last_name, // Your specific fields
                $sale->cus_contact,
                $item->room_number_snapshot,
                $item->room_type_snapshot,
                $item->room_unit_price_snapshot,
                $item->food_price,
                $item->discount_percent . '%',
                $item->total_price,
                strtoupper($sale->status),
                $item->note
            ];
        }
        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            5 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E1B4B']], // Deep Navy
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();

                // 1. Header Branding
                $sheet->mergeCells('A1:K1');
                $sheet->setCellValue('A1', 'KEP SEA VIEW HOTEL - FULL AUDIT REPORT');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(18);

                // 2. Formatting Money Columns (F, G, I)
                $sheet->getStyle("F6:I{$lastRow}")->getNumberFormat()->setFormatCode('#,##0.00 "$"');

                // 3. Freeze top rows and Customer Info
                $sheet->freezePane('D6');

                // 4. Zebra Striping for Readability
                for ($i = 6; $i <= $lastRow; $i++) {
                    if ($i % 2 == 0) {
                        $sheet->getStyle("A$i:K$i")->getFill()
                              ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F8FAFC');
                    }
                }
            },
        ];
    }
}