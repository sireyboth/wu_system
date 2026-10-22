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

    // More than one term can be active at once now — different batches run
    // on genuinely different calendars at the same real-world time (e.g.
    // Batch 23's Year 2 Sem 1 starting September while Batch 24's Year 1
    // Sem 1 starts October). "Active" just marks a term as currently in
    // use; it's no longer an exclusive singleton.
    public function scopeActive(Builder $query)
    {
        return $query->where('is_active', true);
    }

    /**
     * The one term to silently assume when nothing else says which term is
     * meant — used only by the Excel student import and the two "advance
     * semester" actions when no term was explicitly chosen on the form.
     * With multiple terms active at once, "the" active term is ambiguous,
     * so this picks the active term whose date range actually contains
     * today; if none does (e.g. all active terms are in the future or the
     * dates are stale), it falls back to whichever active term started
     * most recently. Callers that know exactly which batch they're acting
     * on should pass an explicit term_id instead of relying on this guess.
     */
    public static function resolveDefault(): ?self
    {
        $today = now()->toDateString();

        return static::active()
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->first()
            ?? static::active()->where('start_date', '<=', $today)->orderByDesc('start_date')->first()
            ?? static::active()->orderByDesc('start_date')->first();
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
