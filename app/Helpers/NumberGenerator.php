<?php
namespace App\Helpers;

use App\Models\NumberCounter;
use Carbon\Carbon;

class NumberGenerator
{
    public function generate(string $type, ?Carbon $issue_date = null): string
    {
        $issue_date ??= Carbon::now();
        $year = (int) $issue_date->format('Y');

        return execute(function () use ($year, $type, $issue_date) {
            $counter = NumberCounter::where('year', $year)
                ->where('type', $type)
                ->lockForUpdate()
                ->first();

            $sequence = $counter
                ? tap($counter)->increment('last_sequence')->last_sequence
                : NumberCounter::create([
                'year' => $year, 'type' => $type, 'last_sequence' => 1,
            ])->last_sequence;

            return $this->format($issue_date, $sequence);
        });
    }

    public function preview(string $type, Carbon $issue_date): string
    {
        $year = (int) $issue_date->format('Y');
        $last = NumberCounter::where('year', $year)
            ->where('type', $type)
            ->value('last_sequence') ?? 0;

        return $this->format($issue_date, $last + 1);
    }

    protected function format(Carbon $date, int $sequence): string
    {
        return sprintf('%s/%s',
            str_pad($sequence, 5, '0', STR_PAD_LEFT),
            $date->format('y')
        );
    }
}
