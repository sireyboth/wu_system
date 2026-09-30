<?php
namespace App\Exports;

use App\Models\PaymentBatch;

/**
 * ACC's reconciliation export — one row per payment batch (= one invoice),
 * same list as the Payment Reconciliation page, with the subjects it
 * covers and ACC's own entry (payment number/note) alongside it.
 */
class PaymentBatchExport extends IExport
{
    protected string $model = PaymentBatch::class;

    protected array $relationships = ['student.person', 'registrations.subject', 'entries'];

    protected array $headings = [
        'No', 'Batch ID', 'Student Code', 'Full Name', 'Subjects', 'Paid At', 'Proof URL',
        'Payment Number', 'Payment Note', 'Entered At', 'Remark',
    ];

    public function query()
    {
        return PaymentBatch::query()->with($this->relationships)->latest();
    }

    public function map(mixed $data): array
    {
        $this->numRow++;

        $person = $data->student?->person;
        $entry  = $data->entries->first();

        return [
            $this->numRow,
            $data->id,
            $data->student?->code,
            trim(($person?->first_name ?? '') . ' ' . ($person?->last_name ?? '')),
            $data->registrations->map(fn($r) => $r->subject?->name_en)->filter()->implode(', '),
            $data->paid_at?->format('Y-m-d H:i:s'),
            // Same login-gated route the page uses, not /storage (see
            // PaymentBatchController::invoice).
            $data->invoice_path ? route('payment-batches.invoice', $data) : null,
            $entry?->payment_number,
            $entry?->payment_note,
            $entry?->entered_at?->format('Y-m-d H:i:s'),
            $data->remark,
        ];
    }
}
