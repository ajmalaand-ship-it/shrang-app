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
     * Provider determines which endpoint/auth/payload callApi() uses.
     *
     * Vertex Lyria 3 modes are intentionally NOT offered — project `shrang`
     * is not yet allowlisted for lyria-3-* on Vertex. vertex_002_30 is the only
     * Vertex option and is a TEST mode until one successful generation is confirmed.
     */
    private function resolveMode(string $settingKey, string $default): array
    {
        return match ($this->settings->get($settingKey, $default)) {
            "dev_clip_30"   => ["provider" => "developer", "model" => "lyria-3-clip-preview", "hint" => "",                    "duration_seconds" => 30],
            "dev_pro_60"    => ["provider" => "developer", "model" => "lyria-3-pro-preview",  "hint" => " Up to 1 minute.",    "duration_seconds" => 60],
            "dev_pro_180"   => ["provider" => "developer", "model" => "lyria-3-pro-preview",  "hint" => " Up to 3 minutes.",   "duration_seconds" => 180],
            "vertex_002_30" => ["provider" => "vertex",    "model" => "lyria-002",            "hint" => "",                    "duration_seconds" => 30],
            default         => ["provider" => "developer", "model" => "lyria-3-pro-preview",  "hint" => " Up to 1 minute.",    "duration_seconds" => 60],
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
            return $provider === "vertex"
                ? $this->callVertex($model, $prompt, $durationSeconds)
                : $this->callDeveloper($model, $prompt, $durationSeconds);
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
     * Vertex AI (aiplatform.googleapis.com) — Service Account Bearer token, :predict.
     * Only lyria-002 is accessible to project `shrang` today (~30s). TEST MODE:
     * the response audio field is a best guess until one live success confirms it.
     */
    private function callVertex(string $model, string $prompt, int $durationSeconds): array
    {
        $keyPath = config("ai.vertex.key_path");
        $project = config("ai.vertex.project");
        $region  = config("ai.vertex.region", "us-central1");

        $jsonKey     = json_decode(file_get_contents($keyPath), true);
        $credentials = new ServiceAccountCredentials(["https://www.googleapis.com/auth/cloud-platform"], $jsonKey);
        $token       = $credentials->fetchAuthToken()["access_token"];

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
     * Parses the Developer API generateContent response.
     * Audio is base64 in candidates[0].content.parts[].inlineData/inline_data.data;
     * any text part is treated as lyrics.
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
     * NOTE: field name not yet confirmed by a live success — tries audioContent
     * then bytesBase64Encoded. Adjust after the first successful Vertex generation.
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
}
