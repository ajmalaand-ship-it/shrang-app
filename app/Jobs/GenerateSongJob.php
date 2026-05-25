<?php
namespace App\Jobs;
use App\Models\Clip;
use App\Models\GenerationJob;
use App\Models\MediaAsset;
use App\Services\AI\AIService;
use App\Services\CreditService;
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
    public function __construct(
        private readonly string $generationJobId,
        private readonly array  $params
    ) {}
    public function handle(AIService $aiService, PromptService $promptService, CreditService $creditService): void
    {
        $job = GenerationJob::findOrFail($this->generationJobId);
        try {
            $job->update(["status" => "running", "started_at" => now(), "attempts" => $job->attempts + 1]);
            $prompt = $promptService->buildSongPrompt($this->params);
            $result = $aiService->generateMusic(array_merge($this->params, ["prompt" => $prompt]));
            if ($result["status"] === "done") {
                $job->update(["status" => "done", "progress_pct" => 100, "credits_charged" => $job->credits_reserved, "completed_at" => now()]);
                Clip::where("id", $job->clip_id)->update(["status" => "ready"]);
                $creditService->commitReservation($this->generationJobId);
                if (!empty($result["audio_url"])) {
                    MediaAsset::create([
                        "clip_id"        => $job->clip_id,
                        "user_id"        => $this->params["user_id"],
                        "generation_job_id" => $this->generationJobId,
                        "type"           => "song_audio",
                        "storage_disk"   => "public",
                        "storage_key"    => $result["audio_url"],
                        "cdn_url"        => $result["audio_url"],
                        "mime_type"      => "audio/mpeg",
                        "file_size_bytes"=> 0,
                        "is_primary"     => true,
                        "is_temp"        => false,
                    ]);
                }
            } else {
                $this->handleFailure($job, $result["error"] ?? "Unknown error", $creditService);
            }
        } catch (\Exception $e) {
            $this->handleFailure($job, $e->getMessage(), $creditService);
            throw $e;
        }
    }
    private function handleFailure(GenerationJob $job, string $error, CreditService $creditService): void
    {
        $job->update(["status" => "failed", "error_message" => $error, "completed_at" => now()]);
        Clip::where("id", $job->clip_id)->update(["status" => "failed"]);
        $creditService->releaseReservation($this->generationJobId);
        Log::error("GenerateSongJob failed", ["generation_job_id" => $this->generationJobId, "error" => $error]);
    }
}
