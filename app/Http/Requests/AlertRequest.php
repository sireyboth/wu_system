<?php
namespace App\Http\Requests;

use App\Models\Alert;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class AlertRequest extends IRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'repeat_type'          => $this->input('repeat_type') ?: 'none',
            'repeat_interval'      => $this->input('repeat_interval') ?: 1,
            'status'               => $this->input('status') ?: 'pending',
            'remind_enabled'       => $this->boolean('remind_enabled'),
        ]);
    }

    protected function formData(): array
    {
        return [
            'title'                    => 'required|string|max:150',
            'sub_title'                => 'nullable|string|max:150',
            'content'                  => 'required|string',
            'category'                 => 'nullable|string|max:50',
            'note'                     => 'nullable|string',

            'start_date'               => 'required|date',
            'end_date'                 => 'required|date|after_or_equal:start_date',

            'status'                   => ['nullable', Rule::in(Alert::STATUSES)],

            'repeat_type'              => ['nullable', Rule::in(Alert::REPEAT_TYPES)],
            'repeat_interval'          => 'nullable|integer|min:1|max:1000',
            'repeat_until'             => 'nullable|date|after_or_equal:start_date',

            'remind_enabled'           => 'nullable|boolean',
            'remind_interval_minutes'  => 'nullable|integer|min:1|max:525600', // 1 minute .. 1 year, unit picked in the UI
        ];
    }

    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            if ($this->boolean('remind_enabled') && blank($this->input('remind_interval_minutes'))) {
                $validator->errors()->add(
                    'remind_interval_minutes',
                    'សូមកំណត់ចន្លោះពេលរំលឹក ដើម្បីបើកការរំលឹកឡើងវិញ។ (Set a reminder interval to enable repeated nagging.)'
                );
            }
        });
    }
}
