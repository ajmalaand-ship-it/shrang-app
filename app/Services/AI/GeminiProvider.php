<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiProvider implements AIProviderInterface
{
    private string $apiKey;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey  = config("ai.gemini.api_key", "");
        $this->baseUrl = config("ai.gemini.base_url", "https://generativelanguage.googleapis.com");
    }

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
        $lyrics   = $params["lyrics"] ?? "";
        $language = $params["language"] ?? "en";
        $prompt   = "Clean and prepare these lyrics for AI music generation. Language: " . $language . ". Return only cleaned lyrics.\n\n" . $lyrics;
        return $this->callApi($prompt);
    }

    public function generateCover(array $params): array
    {
        return ["status" => "skipped", "provider" => $this->providerName()];
    }

    public function providerName(): string
    {
        return "gemini";
    }

    private function callApi(string $prompt): array
    {
        try {
            $response = Http::withHeaders([
                "Content-Type" => "application/json",
            ])->timeout(60)->post(
                "{$this->baseUrl}/v1beta/models/gemini-pro:generateContent?key={$this->apiKey}",
                ["contents" => [["parts" => [["text" => $prompt]]]]]
            );

            if ($response->successful()) {
                $text = $response->json("candidates.0.content.parts.0.text") ?? "";
                return ["status" => "done", "processed_lyrics" => $text, "provider" => $this->providerName()];
            }

            Log::error("GeminiProvider error", ["status" => $response->status()]);
            return ["status" => "error", "error" => $response->body(), "provider" => $this->providerName()];

        } catch (\Exception $e) {
            Log::error("GeminiProvider exception", ["message" => $e->getMessage()]);
            return ["status" => "error", "error" => $e->getMessage(), "provider" => $this->providerName()];
        }
    }
}
