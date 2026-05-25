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
use Illuminate\Support\Facades\Storage;
class GenerateBedMusicJob implements ShouldQueue
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
            $prompt = $promptService->buildBedPrompt($this->params);
            $result = $aiService->generateBed(array_merge($this->params, ["prompt" => $prompt]));
            if ($result["status"] === "done") {
                $job->update(["status" => "done", "progress_pct" => 100, "credits_charged" => $job->credits_reserved, "completed_at" => now(), "provider_response" => $result]);
                Clip::where("id", $job->clip_id)->update(["status" => "ready"]);
                $creditService->commitReservation($this->generationJobId);
                $audioData = $result["audio_data"] ?? null;
                $audioUrl  = $result["audio_url"] ?? null;
                if ($audioData || $audioUrl) {
                    $storageKey = $audioUrl ?? "audio/" . $this->generationJobId . ".mp3";
                    if ($audioData) {
                        $filename = "audio/" . $this->generationJobId . ".mp3";
                        Storage::disk("public")->put($filename, base64_decode($audioData));
                        $storageKey = $filename;
                        $audioUrl   = Storage::disk("public")->url($filename);
                    }
                    MediaAsset::create([
                        "clip_id"           => $job->clip_id,
                        "user_id"           => $this->params["user_id"],
                        "generation_job_id" => $this->generationJobId,
                        "type"              => "bed_audio",
                        "storage_disk"      => "public",
                        "storage_key"       => $storageKey,
                        "cdn_url"           => $audioUrl,
                        "mime_type"         => "audio/mpeg",
                        "file_size_bytes"   => $audioData ? strlen(base64_decode($audioData)) : 0,
                        "is_primary"        => true,
                        "is_temp"           => false,
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
        Log::error("GenerateBedMusicJob failed", ["generation_job_id" => $this->generationJobId, "error" => $error]);
    }
}
