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

    /**
     * IModel::getNameAttribute() assumes every model has name_kh/name_en
     * and builds "{name_kh} ({name_en})" from them — Term has its own
     * plain `name` column instead, so without this override, reading
     * $term->name silently returns " ()" (both pieces null) rather than
     * the actual column value.
     */
    public function getNameAttribute()
    {
        return $this->attributes['name'] ?? null;
    }

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
