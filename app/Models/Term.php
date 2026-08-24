<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

class Term extends IModel
{
    protected $fillable = [
        'year',
        'semester',
        'code',
        'name',
        'start',
        'end',
        'active',
        'remark',
    ];

    protected $casts = [
        'start'     => 'date',
        'end'       => 'date',
        'active' => 'boolean',
    ];

    // Scope for current active term
    public function scopeActive(Builder $query)
    {
        return $query->where('active', true);
    }

    // Scope for current academic year
    public function scopeCurrentYear(Builder $query)
    {
        return $query->where('year', date('Y'));
    }

    // Helper methods
    public function isCurrent(): bool
    {
        return $this->active;
    }

    public function getFormatedNameAttribute(): string
    {
        return "{$this->display_name} - Semester {$this->semester}";
    }
}
