<?php
namespace App\Models;

class InvigilatorHistory extends IModel
{
    protected $fillable = ['invigilator_id', 'description', 'date', 'rating', 'remark'];

    protected $casts = [
        'date'   => 'date',
        'rating' => 'integer',
    ];

    public function invigilator()
    {
        return $this->belongsTo(Invigilator::class);
    }
}
