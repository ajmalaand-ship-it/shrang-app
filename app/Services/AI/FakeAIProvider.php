<?php

namespace App\Services\AI;

class FakeAIProvider implements AIProviderInterface
{
    public function generateMusic(array $params): array
    {
        return [
            'status'          => 'done',
            'provider_job_id' => 'fake-song-' . uniqid(),
            'audio_url'       => 'https://fake-cdn.shrang.test/song.mp3',
            'duration_seconds'=> 60,
            'provider'        => $this->providerName(),
        ];
    }

    public function generateBed(array $params): array
    {
        return [
            'status'          => 'done',
            'provider_job_id' => 'fake-bed-' . uniqid(),
            'audio_url'       => 'https://fake-cdn.shrang.test/bed.mp3',
            'duration_seconds'=> 180,
            'provider'        => $this->providerName(),
        ];
    }

    public function processLyrics(array $params): array
    {
        return [
            'status'           => 'done',
            'processed_lyrics' => $params['lyrics'] ?? 'Fake processed lyrics',
            'detected_language'=> $params['language'] ?? 'en',
            'provider'         => $this->providerName(),
        ];
    }

    public function generateCover(array $params): array
    {
        return [
            'status'   => 'done',
            'image_url'=> 'https://fake-cdn.shrang.test/cover.jpg',
            'provider' => $this->providerName(),
        ];
    }

    public function providerName(): string
    {
        return 'fake';
    }
}
