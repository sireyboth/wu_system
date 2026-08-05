<?php
namespace App\Models;

use App\Helpers\IModel;
use Illuminate\Database\Eloquent\Builder;

class Term extends IModel
{
    protected $fillable = [
        'year',
        'semester',
        'code',
        'name',
        'start_date',
        'end_date',
        'remark',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'is_active'  => 'boolean',
    ];

    // Scope for current active term
    public function scopeActive(Builder $query)
    {
        return $query->where('is_active', true);
    }

    // Scope for current academic year
    public function scopeCurrentYear(Builder $query)
    {
        return $query->where('year', date('Y'));
    }

    // Helper methods
    public function isCurrent(): bool
    {
        return $this->is_active;
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->name} - Semester {$this->semester}";
    }
}
