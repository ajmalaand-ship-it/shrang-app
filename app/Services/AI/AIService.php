<?php
namespace App\Services\AI;
use Illuminate\Support\Facades\Log;
class AIService
{
    private AIProviderInterface $provider;
    private AIUsageTracker $tracker;
    public function __construct(AIProviderInterface $provider, AIUsageTracker $tracker)
    {
        $this->provider = $provider;
        $this->tracker  = $tracker;
    }
    public function generateMusic(array $params): array
    {
        $start  = microtime(true);
        $result = $this->provider->generateMusic($params);
        $this->logUsage("song", $params, $result, $start);
        return $result;
    }
    public function generateBed(array $params): array
    {
        $start  = microtime(true);
        $result = $this->provider->generateBed($params);
        $this->logUsage("bed", $params, $result, $start);
        return $result;
    }
    public function processLyrics(array $params): array
    {
        $start  = microtime(true);
        $result = $this->provider->processLyrics($params);
        $this->logUsage("transcribe", $params, $result, $start);
        return $result;
    }
    public function generateCover(array $params): array
    {
        $start  = microtime(true);
        $result = $this->provider->generateCover($params);
        $this->logUsage("cover", $params, $result, $start);
        return $result;
    }
    public function providerName(): string
    {
        return $this->provider->providerName();
    }
    private function logUsage(string $capability, array $params, array $result, float $start): void
    {
        $this->tracker->track([
            "user_id"           => $params["user_id"] ?? null,
            "generation_job_id" => $params["generation_job_id"] ?? null,
            "provider"          => $this->provider->providerName(),
            "capability"        => $capability,
            "duration_seconds"  => $result["duration_seconds"] ?? null,
            "latency_ms"        => (int) ((microtime(true) - $start) * 1000),
            "status"            => $result["status"] === "done" ? "success" : "error",
            "error_code"        => $result["error"] ?? null,
        ]);
    }
}
