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
            if ($result["status"] !== "done") {
                $this->handleFailure($job, $result["error"] ?? "AI provider returned non-done status", $creditService);
                return;
            }
            // Require audio_data — do not accept temporary external URLs
            $audioData = $result["audio_data"] ?? null;
            if (empty($audioData)) {
                $this->handleFailure($job, "Audio data missing from provider response. Cannot save clip.", $creditService);
                return;
            }
            // Decode audio bytes
            $audioBytes = base64_decode($audioData);
            if ($audioBytes === false || strlen($audioBytes) < 1000) {
                $this->handleFailure($job, "Audio data decode failed or file too small (" . strlen($audioBytes ?? "") . " bytes).", $creditService);
                return;
            }
            // Write to local public storage
            $filename = "audio/" . $this->generationJobId . ".mp3";
            $written  = Storage::disk("public")->put($filename, $audioBytes);
            if ($written === false) {
                $this->handleFailure($job, "Storage write returned false for: {$filename}", $creditService);
                return;
            }
            // Verify file exists on disk
            $fullPath = storage_path("app/public/" . $filename);
            if (!file_exists($fullPath)) {
                $this->handleFailure($job, "File not found on disk after write: {$fullPath}", $creditService);
                return;
            }
            // Verify file size
            $fileSize = filesize($fullPath);
            if ($fileSize < 1000) {
                Storage::disk("public")->delete($filename);
                $this->handleFailure($job, "Audio file too small after write ({$fileSize} bytes): {$filename}", $creditService);
                return;
            }
            // File is confirmed saved — now update DB
            $audioUrl = Storage::disk("public")->url($filename);
            // Create media asset
            MediaAsset::create([
                "clip_id"           => $job->clip_id,
                "user_id"           => $this->params["user_id"],
                "generation_job_id" => $this->generationJobId,
                "type"              => "song_audio",
                "storage_disk"      => "public",
                "storage_key"       => $filename,
                "cdn_url"           => $audioUrl,
                "mime_type"         => "audio/mpeg",
                "file_size_bytes"   => $fileSize,
                "is_primary"        => true,
                "is_temp"           => false,
            ]);
            // Mark job done, clip ready, commit credits
            $job->update([
                "status"           => "done",
                "progress_pct"     => 100,
                "credits_charged"  => $job->credits_reserved,
                "completed_at"     => now(),
                "provider_response"=> $result,
            ]);
            Clip::where("id", $job->clip_id)->update(["status" => "ready"]);
            $creditService->commitReservation($this->generationJobId);
            // Diagnostic success log
            Log::info("Audio file saved", [
                "generation_job_id" => $this->generationJobId,
                "clip_id"           => $job->clip_id,
                "storage_key"       => $filename,
                "file_exists"       => true,
                "file_size_bytes"   => $fileSize,
            ]);
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
        Log::error("GenerateSongJob failed", [
            "generation_job_id" => $this->generationJobId,
            "clip_id"           => $job->clip_id,
            "error"             => $error,
        ]);
    }
}
