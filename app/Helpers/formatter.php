<?php

if (! function_exists('initials')) {
    function initials(string $name, int $limit = 2): string
    {
        $words    = explode(' ', trim($name));
        $initials = collect($words)
            ->filter() // remove empty strings from extra spaces
            ->map(fn($word) => strtoupper(mb_substr($word, 0, 1)))
            ->take($limit)
            ->implode('');

        return $initials;
    }
}

if (! function_exists('to_name')) {
    function to_name(object $object): string
    {
        return "{$object->name_kh} ({$object->name_en})";
    }
}

if (! function_exists('grouped')) {
    function grouped(mixed $data, array $keys = []): array
    {
        return collect($keys)->map(fn($key) => data_get($data, $key, ''))->all();
    }
}

if (! function_exists('imploded')) {
    function imploded(mixed $data, ?string $key = null, string $symbol = '|'): string
    {
        $items = collect($data)
            ->map(fn($item) => $key ? data_get($item, $key) : $item)
            ->filter(fn($item) => filled($item))
            ->all();

        return implode(" {$symbol} ", $items);
    }
}

if (! function_exists('split_full_name')) {
    /**
     * Cambodian convention: first word = family/last name,
     * everything after it = given/first name.
     */
    function split_full_name(?string $full_name): array
    {
        $name = trim((string) $full_name);
        if ($name === '') {
            return ['first_name' => null, 'last_name' => null];
        }

        // limit=2 so "last_name" swallows every remaining word,
        // not just the second one
        $parts = preg_split('/\s+/u', $name, 2);

        return [
            'last_name'  => $parts[0] ?? null,
            'first_name' => $parts[1] ?? null,
        ];
    }
}

if (! function_exists('join_full_name')) {
    function join_full_name(?string $first_name, ?string $last_name): ?string
    {
        $joined = collect([$last_name, $first_name])->filter()->implode(' ');

        return $joined === '' ? null : $joined;
    }
}

if (! function_exists('dated_format')) {
    function dated_format(mixed $value, string $pattern = 'D, d M Y', string $local = 'Asia/Phnom_Penh'): ?string
    {
        return $value ? date_format($value->timezone($local), $pattern) : null;
    }
}
