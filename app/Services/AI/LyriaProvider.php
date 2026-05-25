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
        $this->apiKey  = config("ai.gemini.api_key", "");
        $this->baseUrl = "https://generativelanguage.googleapis.com";
    }
    public function generateMusic(array $params): array
    {
        $prompt = $params["prompt"] ?? "";
        $payload = [
            "contents" => [["parts" => [["text" => $prompt]]]],
            "generationConfig" => ["responseModalities" => ["AUDIO"]],
        ];
        return $this->callApi("lyria-3-pro-preview", $payload);
    }
    public function generateBed(array $params): array
    {
        $prompt = ($params["prompt"] ?? "") . " Instrumental only, no vocals. Up to 3 minutes.";
        $payload = [
            "contents" => [["parts" => [["text" => $prompt]]]],
            "generationConfig" => ["responseModalities" => ["AUDIO"]],
        ];
        return $this->callApi("lyria-3-pro-preview", $payload);
    }
    public function processLyrics(array $params): array
    {
        return ["status" => "skipped", "processed_lyrics" => $params["lyrics"] ?? "", "provider" => $this->providerName()];
    }
    public function generateCover(array $params): array
    {
        return ["status" => "skipped", "provider" => $this->providerName()];
    }
    public function providerName(): string
    {
        return "lyria";
    }
    private function callApi(string $model, array $payload): array
    {
        try {
            $url = "{$this->baseUrl}/v1beta/models/{$model}:generateContent?key={$this->apiKey}";
            $response = Http::withHeaders(["Content-Type" => "application/json"])->timeout(120)->post($url, $payload);
            if ($response->successful()) {
                $data = $response->json();
                $audioData = $data["candidates"][0]["content"]["parts"][0]["inlineData"]["data"] ?? null;
                return ["status" => "done", "audio_data" => $audioData, "duration_seconds" => 60, "provider" => $this->providerName()];
            }
            Log::error("LyriaProvider error", ["status" => $response->status(), "body" => $response->body()]);
            return ["status" => "error", "error" => $response->body(), "provider" => $this->providerName()];
        } catch (\Exception $e) {
            Log::error("LyriaProvider exception", ["message" => $e->getMessage()]);
            return ["status" => "error", "error" => $e->getMessage(), "provider" => $this->providerName()];
        }
    }
}
