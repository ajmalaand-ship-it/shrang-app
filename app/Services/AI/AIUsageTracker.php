<?php

namespace App\Services\AI;

use App\Models\AiUsageLog;
use Illuminate\Support\Facades\Log;

class AIUsageTracker
{
    public function track(array $data): void
    {
        try {
            AiUsageLog::create([
                "user_id"           => $data["user_id"] ?? null,
                "generation_job_id" => $data["generation_job_id"] ?? null,
                "provider"          => $data["provider"] ?? "unknown",
                "capability"        => $data["capability"] ?? "song",
                "prompt_tokens"     => $data["prompt_tokens"] ?? null,
                "output_tokens"     => $data["output_tokens"] ?? null,
                "duration_seconds"  => $data["duration_seconds"] ?? null,
                "provider_cost_usd" => $data["provider_cost_usd"] ?? null,
                "latency_ms"        => $data["latency_ms"] ?? null,
                "status"            => $data["status"] ?? "success",
                "error_code"        => $data["error_code"] ?? null,
            ]);
        } catch (\Exception $e) {
            Log::error("AIUsageTracker failed to write log", [
                "message" => $e->getMessage(),
                "data"    => $data,
            ]);
        }
    }
}
