<?php

namespace App\Services;

use App\Models\LanguageHint;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class PronunciationService
{
    private const CACHE_TTL = 300;

    public function injectHints(string $text, string $languageCode): string
    {
        $hints = $this->hintsForLanguage($languageCode);

        if ($hints->isEmpty()) {
            return '';
        }

        return $hints
            ->filter(fn (LanguageHint $hint) => mb_stripos($text, $hint->word) !== false)
            ->map(fn (LanguageHint $hint) => $hint->prompt_injection)
            ->unique()
            ->implode(' ');
    }

    public function flushCache(string $languageCode): void
    {
        Cache::forget("pronunciation_hints:{$languageCode}");
    }

    public function flushAllCaches(): void
    {
        foreach (['ps', 'fa', 'ur', 'ar', 'hi', 'en'] as $code) {
            $this->flushCache($code);
        }
    }

    private function hintsForLanguage(string $languageCode): Collection
    {
        return Cache::remember(
            "pronunciation_hints:{$languageCode}",
            self::CACHE_TTL,
            fn () => LanguageHint::query()
                ->where('language_code', $languageCode)
                ->where('is_active', true)
                ->get(['id', 'word', 'prompt_injection', 'phoneme_hint', 'provider'])
        );
    }
}