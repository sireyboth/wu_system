<?php
namespace App\Exports;

use App\Exports\Concerns\StudentColumns;
use App\Models\PaymentBatch;

/**
 * ACC's reconciliation export — one row per payment batch (= one invoice),
 * same list as the Payment Reconciliation page, with every field: the
 * batch itself, the full student record, the registrations/subjects it
 * covers, and ACC's own entry (payment number/note) alongside it.
 */
class PaymentBatchExport extends IExport
{
    use StudentColumns;

    protected string $model = PaymentBatch::class;

    public function __construct()
    {
        $this->relationships = [
            ...$this->studentRelationships(),
            'uploader', 'registrations.subject', 'registrations.term', 'registrations.examType',
            'entries.enteredBy',
        ];

        $this->headings = [
            'No', 'Payment Batch ID',
            ...$this->studentHeadings(),
            'Term', 'Exam Type', 'Subject Count', 'Subject Codes', 'Subjects', 'Registration IDs',
            'Paid At', 'Uploaded By', 'Invoice Type', 'Invoice Path', 'Proof URL', 'Payment Batch Remark',
            'Payment Batch Created At', 'Payment Batch Updated At',
            'Entry ID', 'Payment Number', 'Payment Note', 'Entry Entered By', 'Entry Entered At', 'Entry Remark',
            'Entry Created At', 'Entry Updated At',
        ];
    }

    public function query()
    {
        return PaymentBatch::query()->with($this->relationships)->latest();
    }

    public function map(mixed $data): array
    {
        $this->numRow++;

        $regs  = $data->registrations;
        $entry = $data->entries->first();

        return [
            $this->numRow,
            $data->id,
            ...$this->studentColumns($data->student),
            $regs->map(fn($r) => $r->term?->title)->filter()->unique()->implode(', '),
            $regs->map(fn($r) => $r->examType?->name_en)->filter()->unique()->implode(', '),
            $regs->count(),
            $regs->map(fn($r) => $r->subject?->code)->filter()->implode(', '),
            $regs->map(fn($r) => $r->subject?->name_en)->filter()->implode(', '),
            $regs->pluck('id')->implode(', '),
            $this->dateTime($data->paid_at),
            $data->uploader?->name,
            $data->invoice_type,
            $data->invoice_path,
            // Same login-gated route the page uses, not /storage (see
            // PaymentBatchController::invoice).
            $data->invoice_path ? route('payment-batches.invoice', $data) : null,
            $data->remark,
            $this->dateTime($data->created_at),
            $this->dateTime($data->updated_at),
            $entry?->id,
            $entry?->payment_number,
            $entry?->payment_note,
            $entry?->enteredBy?->name,
            $this->dateTime($entry?->entered_at),
            $entry?->remark,
            $this->dateTime($entry?->created_at),
            $this->dateTime($entry?->updated_at),
        ];
    }
}
