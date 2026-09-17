<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

class ExamTerm extends IModel
{
    protected $fillable = [
        'exam_category_id', 'campus_id', 'title', 'exam_date', 'time_slots', 'is_active', 'remark',
    ];

    protected $casts = [
        'exam_date'  => 'date:Y-m-d',
        'time_slots' => 'array',
        'is_active'  => 'boolean',
    ];

    /**
     * IModel::getNameAttribute() assumes every model has name_kh/name_en —
     * ExamTerm has its own plain `title` column instead, same reasoning
     * as Term::getNameAttribute().
     */
    public function getNameAttribute()
    {
        return $this->attributes['title'] ?? null;
    }

    public function category()
    {
        return $this->belongsTo(ExamCategory::class, 'exam_category_id');
    }

    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    public function examStates()
    {
        return $this->hasMany(ExamState::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
