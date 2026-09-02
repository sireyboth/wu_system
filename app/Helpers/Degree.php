<?php

namespace App\Helpers;

enum Degree: string
{
    case Associate = 'associate';
    case Bachelor = 'bachelor';
    case Master = 'master';
    case PhD = 'phd';

    public function labelEn(): string
    {
        return match ($this) {
            self::Associate => "Associate's degree",
            self::Bachelor => "Bachelor's degree",
            self::Master => "Master's degree",
            self::PhD => 'PhD',
        };
    }

    public function labelKh(): string
    {
        return match ($this) {
            self::Associate => 'បរិញ្ញាបត្ររង',
            self::Bachelor => 'បរិញ្ញាបត្រ',
            self::Master => 'បរិញ្ញាបត្រជាន់ខ្ពស់',
            self::PhD => 'បណ្ឌិត',
        };
    }

    // generic accessor if you want to switch on locale dynamically
    public function label(string $locale = 'en'): string
    {
        return match ($locale) {
            'kh', 'km' => $this->labelKh(),
            default => $this->labelEn(),
        };
    }

    // handy for Filament Select::make('degree')->options(Degree::options())
    public static function options(string $locale = 'en'): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn(self $case) => [$case->value => $case->label($locale)])
            ->all();
    }
}
