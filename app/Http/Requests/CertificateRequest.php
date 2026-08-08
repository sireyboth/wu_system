<?php
namespace App\Http\Requests;

use App\Models\Student;

class CertificateRequest extends IRequest
{
    protected function formData(): array
    {
        return [
            'student_id'    => [
                'required', 'integer', 'exists:students,id',
                function ($attribute, $value, $fail) {
                    $student = Student::find($value);
                    if ($student && $student->entrance_exam === 'none') {
                        $fail('សិស្សនេះមិនទាន់ប្រឡងចូលទេ មិនអាចបង្កើតសញ្ញាបត្របានទេ។');
                    }
                },
            ],
            'issue_date'    => 'required|date',
            'full_date_kh'  => 'nullable|string|max:255',
            'short_date_kh' => 'nullable|string|max:255',
            'status'        => 'nullable|string|max:50',
            'type'          => 'nullable|string|max:50',
            'remark'        => 'nullable|string|max:500',
        ];
    }
}
