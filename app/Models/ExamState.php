<?php
namespace App\Models;

class ExamState extends IModel
{
    protected $fillable = [
        'no', 'floor_order', 'floor', 'room', 'major', 'students', 'shift',
        'degree', 'majors', 'remark', 'sort_order', 'exam_date',
    ];

    protected $casts = [
        'majors'    => 'array',
        'exam_date' => 'date:Y-m-d',
    ];
}
