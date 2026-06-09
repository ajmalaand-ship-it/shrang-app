<?php

namespace App\Services\AI;

use Google\Auth\Credentials\ServiceAccountCredentials;
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
        $prompt = $params["prompt"] ?? "A professional music album cover, artistic, square format.";
        $payload = [
            "instances"  => [["prompt" => $prompt]],
            "parameters" => ["sampleCount" => 1],
        ];
        try {
            $keyPath     = config("ai.vertex.key_path");
            $project     = config("ai.vertex.project");
            $region      = config("ai.vertex.region", "us-central1");
            $jsonKey     = json_decode(file_get_contents($keyPath), true);
            $credentials = new ServiceAccountCredentials(
                ["https://www.googleapis.com/auth/cloud-platform"],
                $jsonKey
            );
            $token = $credentials->fetchAuthToken()["access_token"];
            $url = "https://{$region}-aiplatform.googleapis.com/v1/projects/{$project}/locations/{$region}/publishers/google/models/imagen-4.0-generate-001:predict";
            $response = Http::withHeaders([
                "Authorization" => "Bearer {$token}",
                "Content-Type"  => "application/json",
            ])->timeout(60)->post($url, $payload);
            if ($response->successful()) {
                $data      = $response->json();
                $imageData = $data["predictions"][0]["bytesBase64Encoded"] ?? null;
                $mimeType  = $data["predictions"][0]["mimeType"] ?? "image/png";
                if ($imageData) {
                    return ["status" => "done", "image_data" => $imageData, "mime_type" => $mimeType, "provider" => "imagen4"];
                }
                Log::error("Imagen4: no image data in response", ["clip_id" => $params["clip_id"] ?? "unknown"]);
                return ["status" => "error", "error" => "No image data returned from Imagen 4", "provider" => "imagen4"];
            }
            Log::error("Imagen4 cover error", ["http_status" => $response->status(), "body" => substr($response->body(), 0, 300)]);
            if ($response->status() === 429) {
                return ["status" => "rate_limited", "error" => "Imagen 4 rate limit hit (429)", "provider" => "imagen4"];
            }
            return ["status" => "error", "error" => "Imagen 4 HTTP error: " . $response->status(), "provider" => "imagen4"];
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            Log::error("Imagen4 cover exception", ["message" => $msg]);
            // A cURL timeout (error 28 / "Operation timed out") on Imagen is, in practice, the same
            // capacity/throttling condition as a 429 (Vertex Imagen uses Dynamic Shared Quota and
            // sometimes stalls the connection instead of returning 429). Treat it as rate_limited so
            // the cover job's existing exponential backoff retries instead of failing immediately.
            if (
                stripos($msg, "cURL error 28") !== false
                || stripos($msg, "Operation timed out") !== false
                || stripos($msg, "timed out") !== false
            ) {
                return ["status" => "rate_limited", "error" => $msg, "provider" => "imagen4"];
            }
            return ["status" => "error", "error" => $msg, "provider" => "imagen4"];
        }
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
