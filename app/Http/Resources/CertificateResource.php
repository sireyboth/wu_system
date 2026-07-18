<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CertificateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return to_list($this, [
            'student'        => new StudentResource($this->whenLoaded('student')),
            'issue_date'     => $this->issue_date?->format('Y-m-d'),
            'full_date_kh'   => $this->full_date_kh,
            'short_date_kh'  => $this->short_date_kh,
            'certificate_no' => $this->certificate_no,
            'status'         => $this->status,
        ], false);
    }
}
