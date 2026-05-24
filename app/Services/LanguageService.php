<?php

namespace App\Services;

use App\Enums\Language;
use App\Enums\ScriptDirection;

class LanguageService
{
    public const SUPPORTED = ['ps', 'fa', 'ur', 'ar', 'hi', 'en'];

    public const RTL_LANGUAGES = ['ps', 'fa', 'ur', 'ar'];

    public function resolve(string $code): string
    {
        $code = strtolower(trim($code));

        return in_array($code, self::SUPPORTED, true)
            ? $code
            : config('app.locale', 'en');
    }

    public function getDirection(string $code): ScriptDirection
    {
        return in_array($code, self::RTL_LANGUAGES, true)
            ? ScriptDirection::Rtl
            : ScriptDirection::Ltr;
    }

    public function isRtl(string $code): bool
    {
        return in_array($code, self::RTL_LANGUAGES, true);
    }

    public function supportedLanguages(): array
    {
        return Language::labelMap();
    }
}
