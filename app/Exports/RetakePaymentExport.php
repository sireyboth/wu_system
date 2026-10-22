<?php
namespace App\Exports;

use App\Exports\Concerns\StudentColumns;
use App\Models\RetakeRegistration;

/**
 * SA's payments export — same rows as the Payments page (confirmed
 * registrations only) with the same filters/search. One row per
 * registration with every field: the registration itself, the full
 * student record, its retake batch/term/exam type/subject/lecturer/score,
 * and the payment batch + ACC's reconciliation entry it was paid under.
 */
class RetakePaymentExport extends IExport
{
    use StudentColumns;

    protected string $model = RetakeRegistration::class;

    public function __construct(protected array $filters = [])
    {
        $this->relationships = [
            ...$this->studentRelationships(),
            'batch', 'term.campus', 'examType', 'subject', 'lecturer', 'score.enteredBy',
            'paymentBatch.uploader', 'paymentBatch.entries.enteredBy',
        ];

        $this->headings = [
            'No', 'Registration ID',
            ...$this->studentHeadings(),
            'Retake Batch ID', 'Retake Batch Status', 'Retake Batch Source', 'Retake Batch File',
            'Term', 'Term Campus', 'Term Start', 'Term End',
            'Exam Type', 'Exam Type Code',
            'Subject Code', 'Subject', 'Subject (KH)', 'Subject Credit',
            'Lecturer', 'Lecturer Code',
            'Is Selected', 'Selection Saved At', 'Registered At',
            'Payment Status', 'Outcome', 'Score', 'Score Entered By', 'Score Entered At',
            'Telegram Invited At', 'Exam Session ID', 'Previous Registration ID', 'Registration Remark',
            'Registration Created At', 'Registration Updated At',
            'Payment Batch ID', 'Paid At', 'Uploaded By', 'Invoice Type', 'Proof URL', 'Payment Batch Remark',
            'Payment Number', 'Payment Note', 'Entry Entered By', 'Entry Entered At', 'Entry Remark',
        ];
    }

    public function query()
    {
        // Same "confirmed" as the page and the report: registered_at alone
        // isn't enough — confirm() stamps it on unticked subjects too.
        $query = RetakeRegistration::query()
            ->with($this->relationships)
            ->whereNotNull('registered_at')
            ->where('is_selected', true);

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

        $batch   = $data->batch;
        $payment = $data->paymentBatch;
        $entry   = $payment?->entries->first();

        return [
            $this->numRow,
            $data->id,
            ...$this->studentColumns($data->student),
            $batch?->id,
            $batch?->status,
            $batch?->source_type,
            $batch?->file_name,
            $data->term?->title,
            $data->term?->campus?->name_en,
            $this->dateTime($data->term?->start_date, 'Y-m-d'),
            $this->dateTime($data->term?->end_date, 'Y-m-d'),
            $data->examType?->name_en,
            $data->examType?->code,
            $data->subject?->code,
            $data->subject?->name_en,
            $data->subject?->name_kh,
            $data->subject?->credit,
            $data->lecturer?->name_en,
            $data->lecturer?->code,
            $this->yesNo($data->is_selected),
            $this->dateTime($data->selection_saved_at),
            $this->dateTime($data->registered_at),
            $data->payment_status,
            $data->outcome,
            $data->score?->score,
            $data->score?->enteredBy?->name,
            $this->dateTime($data->score?->entered_at),
            $this->dateTime($data->telegram_invited_at),
            $data->exam_session_id,
            $data->previous_registration_id,
            $data->remark,
            $this->dateTime($data->created_at),
            $this->dateTime($data->updated_at),
            $payment?->id,
            $this->dateTime($payment?->paid_at),
            $payment?->uploader?->name,
            $payment?->invoice_type,
            // Same login-gated route the page uses, not /storage (see
            // PaymentBatchController::invoice).
            $payment?->invoice_path ? route('payment-batches.invoice', $payment) : null,
            $payment?->remark,
            $entry?->payment_number,
            $entry?->payment_note,
            $entry?->enteredBy?->name,
            $this->dateTime($entry?->entered_at),
            $entry?->remark,
        ];
    }
}
