<?php

namespace App\Helpers;

use DateTimeInterface;

/** Khmer numerals and solar dates for the bilingual printouts. */
class Khmer
{
    public const MONTHS = [
        1 => 'មករា', 2 => 'កុម្ភៈ', 3 => 'មីនា', 4 => 'មេសា', 5 => 'ឧសភា', 6 => 'មិថុនា',
        7 => 'កក្កដា', 8 => 'សីហា', 9 => 'កញ្ញា', 10 => 'តុលា', 11 => 'វិច្ឆិកា', 12 => 'ធ្នូ',
    ];

    /** "1299/26" → "១២៩៩/២៦"; anything that isn't an ASCII digit is kept. */
    public static function digits(?string $value): string
    {
        return strtr((string) $value, ['០', '១', '២', '៣', '៤', '៥', '៦', '៧', '៨', '៩']);
    }

    /** "ថ្ងៃទី១៦ ខែវិច្ឆិកា ឆ្នាំ២០០២" */
    public static function date(?DateTimeInterface $date): string
    {
        if (! $date) {
            return '';
        }

        return 'ថ្ងៃទី' . self::digits($date->format('j'))
            . ' ខែ' . self::MONTHS[(int) $date->format('n')]
            . ' ឆ្នាំ' . self::digits($date->format('Y'));
    }

    public static function sex(?string $sex): string
    {
        return match ($sex) {
            'Male'   => 'ប្រុស',
            'Female' => 'ស្រី',
            default  => (string) $sex,
        };
    }

    /** Only the common case has a Khmer form; other nationalities print as typed. */
    public static function nationality(?string $nationality): string
    {
        return in_array(strtolower((string) $nationality), ['khmer', 'cambodian'], true) ? 'ខ្មែរ' : (string) $nationality;
    }
}
