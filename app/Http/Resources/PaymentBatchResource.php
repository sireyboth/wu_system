<?php
namespace App\Http\Resources;

class PaymentBatchResource extends IResource
{
    public function toList(): array
    {
        return to_list($this, [
            'student'      => new StudentResource($this->whenLoaded('student')),
            'invoice_path' => $this->invoice_path,
            'invoice_type' => $this->invoice_type,
            // Served by PaymentBatchController::invoice, not /storage (see
            // its route note). Root-relative like the front end's own
            // /api/v1 calls; ?v= changes when the image is replaced, so the
            // browser never shows a cached old one.
            'invoice_url'  => $this->invoice_path
                ? "/api/v1/payment-batches/{$this->id}/invoice?v=" . ($this->updated_at?->timestamp ?? 0)
                : null,
            'uploaded_by'  => $this->uploaded_by,
            'paid_at'      => $this->paid_at?->format('Y-m-d H:i:s'),
            'entries'      => PaymentEntryResource::collection($this->whenLoaded('entries')),
        ], false);
    }
}
