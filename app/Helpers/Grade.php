<?php

namespace App\Helpers;

/**
 * The transcript grading scale — same thresholds as the registrar's Excel
 * sheet (Grade point / GRD formulas). Mirrored in resources/js/transcript/grade.js
 * for the live preview; this server copy is the one that gets stored.
 */
final class Grade
{
    /** [min score, grade point, letter], highest first. */
    public const SCALE = [
        [96, 4.00, 'A+'],
        [90, 3.75, 'A'],
        [85, 3.50, 'A-'],
        [80, 3.25, 'B+'],
        [75, 3.00, 'B'],
        [70, 2.75, 'B-'],
        [65, 2.50, 'C+'],
        [60, 2.25, 'C'],
        [55, 2.00, 'C-'],
        [50, 1.75, 'D'],
    ];

    /** @return array{0: float, 1: string} [grade point, letter] */
    public static function of(float $score): array
    {
        foreach (self::SCALE as [$min, $point, $letter]) {
            if ($score >= $min) {
                return [$point, $letter];
            }
        }

        return [0.00, 'F'];
    }

    /**
     * Credit-weighted GPA of the graded (non-IP) rows, as the registrar's
     * sheet does it: Σ(grade point × credit) ÷ total credits, shown to 2
     * decimals (Excel "##.##" rounds half up). Worked in integer hundredths
     * so float error can't flip the last digit.
     *
     * @param  array<int, array{0: float, 1: int}>  $rows  [grade point, credit]
     */
    public static function gpa(array $rows): float
    {
        $weighted = 0;
        $credits  = 0;
        foreach ($rows as [$point, $credit]) {
            $weighted += (int) round($point * 100) * (int) $credit;
            $credits  += (int) $credit;
        }

        if ($credits === 0) {
            return 0.0;
        }

        return intdiv(2 * $weighted + $credits, 2 * $credits) / 100;
    }
}
