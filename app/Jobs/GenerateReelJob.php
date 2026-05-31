<?php
namespace App\Jobs;
use App\Models\Clip;
use App\Models\GenerationJob;
use App\Models\MediaAsset;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
class GenerateReelJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public int $timeout = 300;
    public int $tries   = 2;
    public function __construct(
        private readonly string $clipId,
        private readonly string $generationJobId,
        private readonly array  $params
    ) {}
    public function handle(): void
    {
        $genJob = GenerationJob::find($this->generationJobId);
        if ($genJob) {
            $genJob->update(["status" => "running", "started_at" => now()]);
        }
        try {
            $clip      = Clip::findOrFail($this->clipId);
            $audioPath = $this->params["audio_path"] ?? null;
            $coverPath = $this->params["cover_path"] ?? null;
            $defaultBg = base_path("public/images/default-reel-bg.png");
            // Audio is required
            if (!$audioPath || !file_exists($audioPath)) {
                throw new \RuntimeException("Audio file not found: " . ($audioPath ?? "null"));
            }
            // Use cover if exists, otherwise use default background
            $imagePath = ($coverPath && file_exists($coverPath)) ? $coverPath : $defaultBg;
            if (!file_exists($imagePath)) {
                throw new \RuntimeException("Neither cover nor default background found at: {$imagePath}");
            }
            // Output path
            $outputFilename = "reels/" . $this->clipId . "-" . $this->generationJobId . ".mp4";
            $outputPath     = storage_path("app/public/" . $outputFilename);
            // Build FFmpeg command
            $ffmpeg = "/usr/bin/ffmpeg";
            $cmd = sprintf(
                "%s -y -loop 1 -i %s -i %s " .
                "-c:v libx264 -tune stillimage -preset fast -crf 23 -pix_fmt yuv420p " .
                "-c:a aac -b:a 192k " .
                "-vf \"scale=1080:1920:force_original_aspect_ratio=decrease,pad=1080:1920:(ow-iw)/2:(oh-ih)/2:black\" " .
                "-shortest -movflags +faststart %s 2>&1",
                $ffmpeg,
                escapeshellarg($imagePath),
                escapeshellarg($audioPath),
                escapeshellarg($outputPath)
            );
            Log::info("GenerateReelJob: running FFmpeg", [
                "clip_id"    => $this->clipId,
                "image_path" => $imagePath,
                "audio_path" => $audioPath,
                "output"     => $outputPath,
            ]);
            $ffmpegOutput = shell_exec($cmd);
            // Verify output file
            if (!file_exists($outputPath)) {
                Log::error("GenerateReelJob: FFmpeg did not create output file", [
                    "clip_id" => $this->clipId,
                    "output"  => $ffmpegOutput,
                ]);
                throw new \RuntimeException("FFmpeg did not create output file.");
            }
            $fileSize = filesize($outputPath);
            if ($fileSize < 10000) {
                unlink($outputPath);
                throw new \RuntimeException("Reel output file too small ({$fileSize} bytes). FFmpeg may have failed.");
            }
            $reelUrl = Storage::disk("public")->url($outputFilename);
            // Mark old reels not primary
            MediaAsset::where("clip_id", $clip->id)
                ->where("type", "reel_video")
                ->update(["is_primary" => false]);
            // Create new reel media asset
            MediaAsset::create([
                "clip_id"           => $clip->id,
                "user_id"           => $this->params["user_id"],
                "generation_job_id" => $this->generationJobId,
                "type"              => "reel_video",
                "storage_disk"      => "public",
                "storage_key"       => $outputFilename,
                "cdn_url"           => $reelUrl,
                "mime_type"         => "video/mp4",
                "file_size_bytes"   => $fileSize,
                "is_primary"        => true,
                "is_temp"           => false,
            ]);
            if ($genJob) {
                $genJob->update([
                    "status"       => "done",
                    "progress_pct" => 100,
                    "completed_at" => now(),
                ]);
            }
            Log::info("Reel generated", [
                "clip_id"         => $this->clipId,
                "storage_key"     => $outputFilename,
                "file_size_bytes" => $fileSize,
                "cdn_url"         => $reelUrl,
            ]);
        } catch (\Exception $e) {
            if ($genJob) {
                $genJob->update([
                    "status"        => "failed",
                    "completed_at"  => now(),
                    "error_message" => $e->getMessage(),
                ]);
            }
            Log::error("GenerateReelJob failed", [
                "clip_id" => $this->clipId,
                "error"   => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
