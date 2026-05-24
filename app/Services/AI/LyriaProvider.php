<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LyriaProvider implements AIProviderInterface
{
    private string $apiKey;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey  = config('ai.lyria.api_key', '');
        $this->baseUrl = config('ai.lyria.base_url', 'https://aiplatform.googleapis.com');
    }

    public function generateMusic(array $params): array
    {
        $payload = [
            'prompt'           => $params['prompt'] ?? '',
            'duration_seconds' => 60,
            'language'         => $params['language'] ?? 'en',
            'output_format'    => 'mp3',
        ];
        return $this->callApi('generate_music', $payload);
    }

    public function generateBed(array $params): array
    {
        $payload = [
            'prompt'           => $params['prompt'] ?? '',
            'duration_seconds' => 180,
            'output_format'    => 'mp3',
            'mode'             => 'instrumental',
        ];
        return $this->callApi('generate_music', $payload);
    }

    public function processLyrics(array $params): array
    {
        return [
            'status'           => 'skipped',
            'processed_lyrics' => $params['lyrics'] ?? '',
            'provider'         => $this->providerName(),
        ];
    }

    public function generateCover(array $params): array
    {
        return [
            'status'   => 'skipped',
            'provider' => $this->providerName(),
        ];
    }

    public function providerName(): string
    {
        return 'lyria';
    }
    private function callApi(string $endpoint, array $payload): array
    {
        try {
            $response = Http::withHeaders([
                "Authorization" => "Bearer " . $this->apiKey,
                "Content-Type"  => "application/json",
            ])->timeout(120)->post("{$this->baseUrl}/v1/{$endpoint}", $payload);

            if ($response->successful()) {
                return array_merge(
                    $response->json(),
                    ["status" => "done", "provider" => $this->providerName()]
                );
            }

            Log::error("LyriaProvider error", [
                "status" => $response->status(),
                "body"   => $response->body(),
            ]);

            return [
                "status"   => "error",
                "error"    => $response->body(),
                "provider" => $this->providerName(),
            ];

        } catch (\Exception $e) {
            Log::error("LyriaProvider exception", ["message" => $e->getMessage()]);
            return [
                "status"   => "error",
                "error"    => $e->getMessage(),
                "provider" => $this->providerName(),
            ];
        }
    }
}
