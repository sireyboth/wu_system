<?php
namespace App\Http\Requests;

class InvigilatorRequest extends IRequest
{
    protected function formData(): array
    {
        return array_merge(
            DEFAULT_VALIDATE,
            check_unique('invigilators', 'code', true),
            [
                'batch'       => 'nullable|string|max:100',
                'department'  => 'nullable|string|max:100',
                'room'        => 'nullable|string|max:50',
                'valid_until' => 'nullable|date',

                // The repeatable history block. Rows with an id update that
                // row; rows without one are created; saved rows missing from
                // the list are removed (see InvigilatorController::syncHistories).
                'histories'               => 'sometimes|array',
                'histories.*.id'          => 'nullable|integer',
                'histories.*.description' => 'required|string|max:1000',
                'histories.*.date'        => 'nullable|date',
                'histories.*.rating'      => 'nullable|integer|between:1,5',
                'histories.*.remark'      => 'nullable|string|max:500',
            ]
        );
    }

    public function attributes(): array
    {
        return [
            'code'                    => 'ID',
            'histories.*.description' => 'history description',
            'histories.*.date'        => 'history date',
            'histories.*.rating'      => 'history rating',
        ];
    }
}
