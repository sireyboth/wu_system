<?php
namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Alert extends IModel
{
    public const REPEAT_TYPES = ['none', 'daily', 'weekly', 'monthly', 'yearly'];
    public const STATUSES = ['pending', 'completed'];

    protected $fillable = [
        'title', 'sub_title', 'content', 'category',
        'start_date', 'end_date', 'note',
        'status', 'completed_at',
        'repeat_type', 'repeat_interval', 'repeat_until',
        'remind_enabled', 'remind_interval_minutes',
        'snoozed_until',
    ];

    protected $casts = [
        'start_date'              => 'datetime',
        'end_date'                => 'datetime',
        'completed_at'            => 'datetime',
        'snoozed_until'           => 'datetime',
        'repeat_until'            => 'date',
        'repeat_interval'         => 'integer',
        'remind_interval_minutes' => 'integer',
        'remind_enabled'          => 'boolean',
    ];

    public function logs(): HasMany
    {
        return $this->hasMany(AlertLog::class)->latest('sent_at');
    }

    public function isSnoozed(): bool
    {
        return (bool) $this->snoozed_until?->isFuture();
    }

    public function isOverdue(): bool
    {
        return $this->status === 'pending' && $this->end_date->isPast();
    }

    /**
     * Whole-day distance from now to start_date — negative once it's started
     * (or gone overdue). Used to bucket the dashboard cards (High: <= 1,
     * Medium: 2-3) and is intentionally day-granular, not hour-precise.
     */
    public function daysUntilStart(): int
    {
        return (int) now()->startOfDay()->diffInDays($this->start_date->copy()->startOfDay(), false);
    }

    protected function repeatUnit(): ?string
    {
        return match ($this->repeat_type) {
            'daily'   => 'days',
            'weekly'  => 'weeks',
            'monthly' => 'months',
            'yearly'  => 'years',
            default   => null,
        };
    }

    /**
     * Called when an alert is marked complete. If it repeats, re-arms this
     * same row to its next occurrence (shifts start/end forward, resets
     * status) instead of leaving it completed or spawning a new row — an
     * alert behaves like a recurring task, not a calendar of occurrences.
     */
    public function completeOrAdvance(): void
    {
        $unit = $this->repeatUnit();

        if (! $unit) {
            $this->update(['status' => 'completed', 'completed_at' => now()]);
            return;
        }

        $duration = $this->start_date->diffInSeconds($this->end_date);
        $nextStart = $this->start_date->copy()->add($this->repeat_interval, $unit);

        if ($this->repeat_until && $nextStart->gt($this->repeat_until->copy()->endOfDay())) {
            $this->update(['status' => 'completed', 'completed_at' => now()]);
            return;
        }

        $this->update([
            'start_date'    => $nextStart,
            'end_date'      => $nextStart->copy()->addSeconds($duration),
            'status'        => 'pending',
            'completed_at'  => null,
            'snoozed_until' => null,
        ]);
    }

    public function log(string $type, ?string $message = null, bool $success = true): AlertLog
    {
        return $this->logs()->create([
            'type'    => $type,
            'message' => $message,
            'sent_at' => now(),
            'success' => $success,
        ]);
    }

    public function hasLoggedToday(string $type): bool
    {
        return $this->logs()
            ->where('type', $type)
            ->whereDate('sent_at', now()->toDateString())
            ->exists();
    }

    public function lastLogAt(string $type): ?Carbon
    {
        return $this->logs()->where('type', $type)->first()?->sent_at;
    }
}
