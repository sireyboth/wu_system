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
            // Root-relative, like the front end's own /api/v1 calls — an
            // absolute asset() URL follows the request's host/scheme and
            // comes out http:// behind an HTTPS proxy, which browsers block.
            'invoice_url'  => $this->invoice_path ? '/storage/' . ltrim($this->invoice_path, '/') : null,
            'uploaded_by'  => $this->uploaded_by,
            'paid_at'      => $this->paid_at?->format('Y-m-d H:i:s'),
            'entries'      => PaymentEntryResource::collection($this->whenLoaded('entries')),
        ], false);
    }
}
