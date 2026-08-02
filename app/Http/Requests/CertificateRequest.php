<?php
namespace App\Http\Requests;

class CertificateRequest extends IRequest
{
    protected function formData(): array
    {
        return array_merge(
            check_exist('student_id', 'students'),
            [
                'issue_date'    => 'required|date',
                'full_date_kh'  => 'nullable|string|max:255',
                'short_date_kh' => 'nullable|string|max:255',
                'status'        => 'nullable|string|max:50',
                'type'          => 'nullable|string|max:50',
                'remark'        => 'nullable|string|max:500',
            ]
        );
    }
}
