<?php

namespace App\Services\AI;

use App\Services\AdminSettingsService;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LyriaProvider implements AIProviderInterface
{
    private string $apiKey;

    public function __construct(private readonly AdminSettingsService $settings)
    {
        $this->apiKey = config("ai.gemini.api_key", "");
    }

    public function generateMusic(array $params): array
    {
        $mode   = $this->resolveMode("lyria_song_mode", "dev_pro_60");
        $prompt = ($params["prompt"] ?? "") . $mode["hint"];

        return $this->callApi($mode["provider"], $mode["model"], $prompt, $mode["duration_seconds"]);
    }

    public function generateBed(array $params): array
    {
        $mode   = $this->resolveMode("lyria_bed_mode", "dev_pro_180");
        $prompt = ($params["prompt"] ?? "") . " Instrumental only, no vocals." . $mode["hint"];

        return $this->callApi($mode["provider"], $mode["model"], $prompt, $mode["duration_seconds"]);
    }

    /**
     * Maps an admin mode string to provider + model + prompt hint + duration.
     * Providers:
     *   developer          -> Developer API (generativelanguage), ?key=, generateContent
     *   vertex             -> Vertex :predict (lyria-002, English/instrumental, 30s)
     *   vertex_interactions-> Vertex /interactions (lyria-3-pro, multilingual + vocals, up to ~184s)
     */
    private function resolveMode(string $settingKey, string $default): array
    {
        return match ($this->settings->get($settingKey, $default)) {
            "dev_clip_30"       => ["provider" => "developer",           "model" => "lyria-3-clip-preview", "hint" => "",                  "duration_seconds" => 30],
            "dev_pro_60"        => ["provider" => "developer",           "model" => "lyria-3-pro-preview",  "hint" => " Up to 1 minute.",  "duration_seconds" => 60],
            "dev_pro_180"       => ["provider" => "developer",           "model" => "lyria-3-pro-preview",  "hint" => " Up to 3 minutes.", "duration_seconds" => 180],
            "vertex_002_30"     => ["provider" => "vertex",              "model" => "lyria-002",            "hint" => "",                  "duration_seconds" => 30],
            "vertex_lyria3_pro" => ["provider" => "vertex_interactions", "model" => "lyria-3-pro-preview",  "hint" => "",                  "duration_seconds" => 180],
            default             => ["provider" => "developer",           "model" => "lyria-3-pro-preview",  "hint" => " Up to 1 minute.",  "duration_seconds" => 60],
        };
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

    /**
     * Routes to the correct provider implementation.
     */
    private function callApi(string $provider, string $model, string $prompt, int $durationSeconds): array
    {
        try {
            return match ($provider) {
                "vertex"              => $this->callVertex($model, $prompt, $durationSeconds),
                "vertex_interactions" => $this->callVertexInteractions($model, $prompt, $durationSeconds),
                default               => $this->callDeveloper($model, $prompt, $durationSeconds),
            };
        } catch (\Exception $e) {
            Log::error("LyriaProvider exception", ["message" => $e->getMessage()]);
            return ["status" => "error", "error" => $e->getMessage(), "provider" => $this->providerName()];
        }
    }

    /**
     * Developer API (generativelanguage.googleapis.com) — API key auth, generateContent.
     * Uses the Developer API path and its own quota limits.
     */
    private function callDeveloper(string $model, string $prompt, int $durationSeconds): array
    {
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$this->apiKey}";

        $payload = [
            "contents"         => [["parts" => [["text" => $prompt]]]],
            "generationConfig" => ["responseModalities" => ["AUDIO"]],
        ];

        $response = Http::withHeaders(["Content-Type" => "application/json"])
            ->timeout(120)
            ->post($url, $payload);

        Log::info("LyriaProvider developer response", ["body" => substr($response->body(), 0, 500)]);

        if ($response->successful()) {
            return $this->parseDeveloperResponse($response->json(), $durationSeconds);
        }

        Log::error("LyriaProvider developer error", ["status" => $response->status(), "body" => $response->body()]);
        return ["status" => "error", "error" => $response->body(), "provider" => $this->providerName()];
    }

    /**
     * Vertex AI :predict (lyria-002) — Service Account Bearer token.
     * English-only, instrumental, ~30s. Kept as a scalable test mode.
     */
    private function callVertex(string $model, string $prompt, int $durationSeconds): array
    {
        $project = config("ai.vertex.project");
        $region  = config("ai.vertex.region", "us-central1");
        $token   = $this->vertexToken();

        $url = "https://{$region}-aiplatform.googleapis.com/v1/projects/{$project}/locations/{$region}/publishers/google/models/{$model}:predict";

        $payload = [
            "instances"  => [["prompt" => $prompt]],
            "parameters" => ["sample_count" => 1],
        ];

        $response = Http::withHeaders([
            "Authorization" => "Bearer {$token}",
            "Content-Type"  => "application/json",
        ])->timeout(120)->post($url, $payload);

        Log::info("LyriaProvider vertex response", ["body" => substr($response->body(), 0, 500)]);

        if ($response->successful()) {
            return $this->parseVertexResponse($response->json(), $durationSeconds);
        }

        Log::error("LyriaProvider vertex error", ["status" => $response->status(), "body" => $response->body()]);
        return ["status" => "error", "error" => $response->body(), "provider" => $this->providerName()];
    }

    /**
     * Vertex AI /interactions (lyria-3-pro-preview) — Service Account Bearer token.
     * Multilingual (incl. Pashto/Dari) + vocals + structured songs up to ~184s.
     * Location is always "global" for this preview model. Generation takes 60-90s.
     * NOTE: model is in public preview ("not for production use" per Google's card).
     */
    private function callVertexInteractions(string $model, string $prompt, int $durationSeconds): array
    {
        $project = config("ai.vertex.project");
        $token   = $this->vertexToken();

        $url = "https://aiplatform.googleapis.com/v1beta1/projects/{$project}/locations/global/interactions";

        $payload = [
            "model" => $model,
            "input" => [["type" => "text", "text" => $prompt]],
        ];

        $response = Http::withHeaders([
            "Authorization" => "Bearer {$token}",
            "Content-Type"  => "application/json",
        ])->timeout(180)->post($url, $payload);

        // Do NOT log the full body — it contains multi-MB base64 audio.
        Log::info("LyriaProvider vertex interactions response", ["status" => $response->status()]);

        if ($response->successful()) {
            return $this->parseInteractionsResponse($response->json(), $durationSeconds);
        }

        Log::error("LyriaProvider vertex interactions error", ["status" => $response->status(), "body" => substr($response->body(), 0, 500)]);
        return ["status" => "error", "error" => substr($response->body(), 0, 500), "provider" => $this->providerName()];
    }

    /**
     * Fetches a Service Account OAuth Bearer token for Vertex.
     */
    private function vertexToken(): string
    {
        $keyPath     = config("ai.vertex.key_path");
        $jsonKey     = json_decode(file_get_contents($keyPath), true);
        $credentials = new ServiceAccountCredentials(["https://www.googleapis.com/auth/cloud-platform"], $jsonKey);
        return $credentials->fetchAuthToken()["access_token"];
    }

    /**
     * Parses the Developer API generateContent response.
     */
    private function parseDeveloperResponse(array $data, int $durationSeconds): array
    {
        $audioData = null;
        $lyrics    = null;

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
            $finishReason  = $data["candidates"][0]["finishReason"]  ?? "unknown";
            $finishMessage = $data["candidates"][0]["finishMessage"] ?? "No audio returned.";
            Log::warning("LyriaProvider developer: no audio in response", ["finishReason" => $finishReason, "finishMessage" => $finishMessage]);
            return ["status" => "error", "error" => "No audio returned. Reason: {$finishReason}. {$finishMessage}", "provider" => $this->providerName()];
        }

        return ["status" => "done", "audio_data" => $audioData, "lyrics" => $lyrics, "duration_seconds" => $durationSeconds, "provider" => $this->providerName()];
    }

    /**
     * Parses the Vertex :predict response (lyria-002).
     */
    private function parseVertexResponse(array $data, int $durationSeconds): array
    {
        $prediction = $data["predictions"][0] ?? [];
        $audioData  = $prediction["audioContent"] ?? $prediction["bytesBase64Encoded"] ?? null;

        if ($audioData === null) {
            Log::warning("LyriaProvider: no audio in vertex response", ["prediction_keys" => array_keys($prediction)]);
            return ["status" => "error", "error" => "No audio returned from Vertex Lyria.", "provider" => $this->providerName()];
        }

        return ["status" => "done", "audio_data" => $audioData, "lyrics" => null, "duration_seconds" => $durationSeconds, "provider" => $this->providerName()];
    }

    /**
     * Parses the Vertex /interactions response (lyria-3-pro-preview).
     * outputs[] contains text item(s) (lyrics + description) and one audio item
     * (type=audio, mime_type=audio/mpeg, data=base64 MP3).
     */
    private function parseInteractionsResponse(array $data, int $durationSeconds): array
    {
        $audioData = null;
        $lyrics    = null;

        foreach (($data["outputs"] ?? []) as $output) {
            $type = $output["type"] ?? "";
            if ($type === "audio" && isset($output["data"])) {
                $audioData = $output["data"];
            }
            if ($type === "text" && $lyrics === null && isset($output["text"])) {
                $lyrics = $output["text"]; // first text item = lyrics
            }
        }

        if ($audioData === null) {
            Log::warning("LyriaProvider: no audio in interactions response", ["status" => $data["status"] ?? "unknown"]);
            return ["status" => "error", "error" => "No audio returned from Vertex Lyria 3.", "provider" => $this->providerName()];
        }

        return ["status" => "done", "audio_data" => $audioData, "lyrics" => $lyrics, "duration_seconds" => $durationSeconds, "provider" => $this->providerName()];
    }
}
