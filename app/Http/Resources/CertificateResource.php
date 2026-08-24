<?php
namespace App\Http\Resources;

class CertificateResource extends IResource
{
    public function toList(): array
    {
        return to_list($this, [
            'status'         => $this->status,
            'type'           => $this->type,
            'issue_date'     => dated_format($this->issue_date),
            'full_date_kh'   => $this->full_date_kh,
            'short_date_kh'  => $this->short_date_kh,
            'certificate_no' => $this->certificate_no,
            'student'        => new StudentResource($this->whenLoaded('student')),
        ], false);
    }
}
