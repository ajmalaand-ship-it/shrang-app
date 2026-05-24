<?php

namespace App\Services\AI;

interface AIProviderInterface
{
    /**
     * Generate a song from lyrics.
     * Duration target: 59-60 seconds.
     */
    public function generateMusic(array $params): array;

    /**
     * Generate bed/background music.
     * Duration target: up to 180 seconds (3 minutes).
     */
    public function generateBed(array $params): array;

    /**
     * Process lyrics — clean, translate, detect language.
     */
    public function processLyrics(array $params): array;

    /**
     * Generate a cover image from lyrics or description.
     */
    public function generateCover(array $params): array;

    /**
     * Return the provider name string.
     * Example: 'lyria', 'gemini', 'elevenlabs', 'fake'
     */
    public function providerName(): string;
}
