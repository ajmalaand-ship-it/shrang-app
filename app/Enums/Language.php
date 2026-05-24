<?php

namespace App\Enums;

enum Language: string
{
    case Pashto  = 'ps';
    case Dari    = 'fa';
    case Urdu    = 'ur';
    case Arabic  = 'ar';
    case Hindi   = 'hi';
    case English = 'en';

    public function label(): string
    {
        return match ($this) {
            self::Pashto  => 'پښتو',
            self::Dari    => 'دری',
            self::Urdu    => 'اردو',
            self::Arabic  => 'العربية',
            self::Hindi   => 'हिन्दी',
            self::English => 'English',
        };
    }

    public function direction(): ScriptDirection
    {
        return match ($this) {
            self::Pashto,
            self::Dari,
            self::Urdu,
            self::Arabic  => ScriptDirection::Rtl,
            default       => ScriptDirection::Ltr,
        };
    }

    public static function fromCode(string $code): ?self
    {
        return self::tryFrom($code);
    }

    public static function labelMap(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $lang) => [$lang->value => $lang->label()])
            ->all();
    }
}
