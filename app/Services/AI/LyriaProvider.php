<?php
namespace App\Services\AI;
use Google\Auth\Credentials\ServiceAccountCredentials;
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
        return $this->callApi(config("ai.lyria.model", "lyria-3-pro-preview"), $payload);
    }
    public function generateBed(array $params): array
    {
        $prompt = ($params["prompt"] ?? "") . " Instrumental only, no vocals. Up to 3 minutes.";
        $payload = [
            "contents" => [["parts" => [["text" => $prompt]]]],
            "generationConfig" => ["responseModalities" => ["AUDIO"]],
        ];
        return $this->callApi(config("ai.lyria.model", "lyria-3-pro-preview"), $payload);
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
            $keyPath     = config("ai.vertex.key_path");
            $project     = config("ai.vertex.project");
            $region      = config("ai.vertex.region", "us-central1");
            $jsonKey     = json_decode(file_get_contents($keyPath), true);
            $credentials = new ServiceAccountCredentials(["https://www.googleapis.com/auth/cloud-platform"], $jsonKey);
            $token       = $credentials->fetchAuthToken()["access_token"];
            $url = "https://{$region}-aiplatform.googleapis.com/v1/projects/{$project}/locations/{$region}/publishers/google/models/{$model}:generateContent";
            $response = Http::withHeaders(["Authorization" => "Bearer {$token}", "Content-Type" => "application/json"])->timeout(120)->post($url, $payload);
            Log::info("LyriaProvider raw response", ["body" => substr($response->body(), 0, 500)]);
            if ($response->successful()) {
                $data = $response->json();
                $audioData = null;
                $lyrics = null;
                $parts = $data["candidates"][0]["content"]["parts"] ?? [];
                foreach ($parts as $part) {
                    if (isset($part["inlineData"]["data"])) {
                        $audioData = $part["inlineData"]["data"];
                    }
                    if (isset($part["inline_data"]["data"])) {
                        $audioData = $part["inline_data"]["data"];
                    }
                    if (isset($part["text"])) {
                        $lyrics = $part["text"];
                    }
                }
                if ($audioData === null) {
                    $finishReason = $data["candidates"][0]["finishReason"] ?? "unknown";
                    $finishMessage = $data["candidates"][0]["finishMessage"] ?? "No audio returned.";
                    Log::warning("LyriaProvider: no audio in response", ["finishReason" => $finishReason, "finishMessage" => $finishMessage]);
                    return ["status" => "error", "error" => "No audio returned. Reason: " . $finishReason . ". " . $finishMessage, "provider" => $this->providerName()];
                }
                return ["status" => "done", "audio_data" => $audioData, "lyrics" => $lyrics, "duration_seconds" => 60, "provider" => $this->providerName()];
            }
            Log::error("LyriaProvider error", ["status" => $response->status(), "body" => $response->body()]);
            return ["status" => "error", "error" => $response->body(), "provider" => $this->providerName()];
        } catch (\Exception $e) {
            Log::error("LyriaProvider exception", ["message" => $e->getMessage()]);
            return ["status" => "error", "error" => $e->getMessage(), "provider" => $this->providerName()];
        }
    }
}
