<?php
namespace App\Jobs;
use App\Models\GenerationJob;
use App\Services\AI\AIService;
use App\Services\PromptService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
class GenerateSongJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public int $timeout = 600;
    public int $tries   = 3;
    public array $backoff = [60, 120, 300];
    public function __construct(
        private readonly string $generationJobId,
        private readonly array  $params
    ) {}
    public function handle(AIService $aiService, PromptService $promptService): void
    {
        $job = GenerationJob::findOrFail($this->generationJobId);
        try {
            $job->update(["status" => "running", "started_at" => now(), "attempts" => $job->attempts + 1]);
            $prompt = $promptService->buildSongPrompt($this->params);
            $result = $aiService->generateMusic(array_merge($this->params, ["prompt" => $prompt]));
            if ($result["status"] === "done") {
                $job->update([
                    "status"           => "done",
                    "progress_pct"     => 100,
                    "credits_charged"  => $job->credits_reserved,
                    "provider_response"=> $result,
                    "completed_at"     => now(),
                ]);
            } else {
                $this->handleFailure($job, $result["error"] ?? "Unknown error");
            }
        } catch (\Exception $e) {
            $this->handleFailure($job, $e->getMessage());
            throw $e;
        }
    }
    private function handleFailure(GenerationJob $job, string $error): void
    {
        $job->update([
            "status"        => "failed",
            "error_message" => $error,
            "completed_at"  => now(),
        ]);
        Log::error("GenerateSongJob failed", [
            "generation_job_id" => $this->generationJobId,
            "error"             => $error,
        ]);
    }
}
