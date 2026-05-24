<?php

namespace App\Services\AI;

class ElevenLabsProvider implements AIProviderInterface
{
    public function generateMusic(array $params): array
    {
        return ["status" => "skipped", "provider" => $this->providerName()];
    }

    public function generateBed(array $params): array
    {
        return ["status" => "skipped", "provider" => $this->providerName()];
    }

    public function processLyrics(array $params): array
    {
        return ["status" => "skipped", "provider" => $this->providerName()];
    }

    public function generateCover(array $params): array
    {
        return ["status" => "skipped", "provider" => $this->providerName()];
    }

    public function providerName(): string
    {
        return "elevenlabs";
    }
}
