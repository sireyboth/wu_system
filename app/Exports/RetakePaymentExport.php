<?php
namespace App\Exports;

use App\Models\RetakeRegistration;

/**
 * SA's payments export — same rows as the Payments page (confirmed
 * registrations only) with the same filters/search, plus the payment
 * batch's paid_at and Telegram invite time so SA can hand a paid/unpaid
 * list to whoever needs it.
 */
class RetakePaymentExport extends IExport
{
    protected string $model = RetakeRegistration::class;

    protected array $relationships = ['student.person', 'term', 'examType', 'subject', 'paymentBatch'];

    protected array $headings = [
        'No', 'Student Code', 'Full Name', 'Term', 'Exam Type', 'Subject', 'Registered At',
        'Payment Status', 'Payment Batch ID', 'Paid At', 'Telegram Invited At', 'Remark',
    ];

    public function __construct(protected array $filters = [])
    {
    }

    public function query()
    {
        $query = RetakeRegistration::query()
            ->with($this->relationships)
            ->whereNotNull('registered_at');

        if ($examTypeId = $this->filters['exam_type_id'] ?? null) {
            $query->where('exam_type_id', $examTypeId);
        }
        if ($termId = $this->filters['retake_term_id'] ?? null) {
            $query->where('retake_term_id', $termId);
        }
        if ($paymentStatus = $this->filters['payment_status'] ?? null) {
            $query->where('payment_status', $paymentStatus);
        }

        return $query->search($this->filters['search'] ?? null)->latest();
    }

    public function map(mixed $data): array
    {
        $this->numRow++;

        $person = $data->student?->person;

        return [
            $this->numRow,
            $data->student?->code,
            trim(($person?->first_name ?? '') . ' ' . ($person?->last_name ?? '')),
            $data->term?->title,
            $data->examType?->name_en,
            $data->subject?->name_en,
            $data->registered_at?->format('Y-m-d H:i:s'),
            $data->payment_status,
            $data->payment_batch_id,
            $data->paymentBatch?->paid_at?->format('Y-m-d H:i:s'),
            $data->telegram_invited_at?->format('Y-m-d H:i:s'),
            $data->remark,
        ];
    }
}
