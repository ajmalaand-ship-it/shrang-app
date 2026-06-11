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

            $titleOverlayPath = null;
            $titleTempPaths = [];
            $titleText = trim((string) ($clip->display_title ?? ""));

            if ($titleText !== "" && $titleText !== "Untitled Clip") {
                $titleText = mb_substr($titleText, 0, 80);

                $overlayDir = storage_path("app/public/reel-overlays");
                if (!is_dir($overlayDir)) {
                    mkdir($overlayDir, 0755, true);
                }

                $titleShadowPath = $overlayDir . "/{$clip->id}-{$this->generationJobId}-title-shadow.png";
                $titleMainPath = $overlayDir . "/{$clip->id}-{$this->generationJobId}-title-main.png";
                $titleOverlayPath = $overlayDir . "/{$clip->id}-{$this->generationJobId}-title.png";
                $titleTempPaths = [$titleShadowPath, $titleMainPath, $titleOverlayPath];

                $titleMarkup = 'pango:<span font="Vazirmatn Bold 56">' .
                    htmlspecialchars($titleText, ENT_QUOTES | ENT_XML1, 'UTF-8') .
                    '</span>';

                $shadowCmd = sprintf(
                    "/usr/bin/convert -background none -fill 'rgba(0,0,0,0.88)' -size 980x260 %s -trim +repage -gravity center -extent 980x260 -blur 0x2 PNG32:%s 2>&1",
                    escapeshellarg($titleMarkup),
                    escapeshellarg($titleShadowPath)
                );

                $mainCmd = sprintf(
                    "/usr/bin/convert -background none -fill white -size 980x260 %s -trim +repage -gravity center -extent 980x260 PNG32:%s 2>&1",
                    escapeshellarg($titleMarkup),
                    escapeshellarg($titleMainPath)
                );

                $combineCmd = sprintf(
                    "/usr/bin/convert -size 980x260 xc:none %s -gravity center -geometry +0+5 -composite %s -gravity center -composite PNG32:%s 2>&1",
                    escapeshellarg($titleShadowPath),
                    escapeshellarg($titleMainPath),
                    escapeshellarg($titleOverlayPath)
                );

                exec($shadowCmd, $shadowOut, $shadowCode);
                exec($mainCmd, $mainOut, $mainCode);
                exec($combineCmd, $combineOut, $combineCode);

                if ($shadowCode !== 0 || $mainCode !== 0 || $combineCode !== 0 || !file_exists($titleOverlayPath)) {
                    Log::warning("GenerateReelJob: title overlay creation failed", [
                        "clip_id" => $this->clipId,
                        "shadow_output" => implode("\n", $shadowOut ?? []),
                        "main_output" => implode("\n", $mainOut ?? []),
                        "combine_output" => implode("\n", $combineOut ?? []),
                    ]);

                    $titleOverlayPath = null;
                }
            }

            $filter = "[0:v]split=2[bgsrc][fgsrc];" .
                "[bgsrc]scale=1180:2098:force_original_aspect_ratio=increase,crop=1080:1920," .
                "zoompan=z='1.04+0.025*sin(on/50)':x='iw/2-(iw/zoom/2)+30*sin(on/66)':y='ih/2-(ih/zoom/2)+26*cos(on/78)':d=1:s=1080x1920:fps=30," .
                "gblur=sigma=20,eq=brightness=-0.03:saturation=1.08,vignette=angle=PI/9," .
                "drawbox=x=210:y=505:w=660:h=2:color=0xFF9A4A@0.44:t=fill," .
                "drawbox=x=210:y=1400:w=660:h=2:color=0xFF9A4A@0.30:t=fill[bg];" .
                "[fgsrc]scale=820:820:force_original_aspect_ratio=decrease,pad=820:820:(ow-iw)/2:(oh-ih)/2:color=0x101014," .
                "zoompan=z='1.008+0.012*sin(on/52)':x='iw/2-(iw/zoom/2)':y='ih/2-(ih/zoom/2)':d=1:s=820x820:fps=30,format=rgba,split=2[shadowraw][fg];" .
                "[shadowraw]boxblur=24:2,colorchannelmixer=rr=0:gg=0:bb=0:aa=0.30[shadow];" .
                "[bg][shadow]overlay=(W-w)/2+4*sin(t*0.65):552+7*sin(t*0.8)[tmp];" .
                "[tmp][fg]overlay=(W-w)/2+4*sin(t*0.65):540+7*sin(t*0.8)[base]";

            if ($titleOverlayPath && file_exists($titleOverlayPath)) {
                $filter .= ";[base][2:v]overlay=(W-w)/2:1375,format=yuv420p[v]";
            } else {
                $filter .= ";[base]format=yuv420p[v]";
            }

            if ($titleOverlayPath && file_exists($titleOverlayPath)) {
                $cmd = sprintf(
                    "%s -y -loop 1 -framerate 30 -i %s -i %s -loop 1 -i %s " .
                    "-filter_complex %s -map %s -map 1:a:0 " .
                    "-c:v libx264 -preset fast -crf 24 -r 30 -pix_fmt yuv420p " .
                    "-c:a aac -b:a 192k " .
                    "-shortest -movflags +faststart %s 2>&1",
                    $ffmpeg,
                    escapeshellarg($imagePath),
                    escapeshellarg($audioPath),
                    escapeshellarg($titleOverlayPath),
                    escapeshellarg($filter),
                    escapeshellarg("[v]"),
                    escapeshellarg($outputPath)
                );
            } else {
                $cmd = sprintf(
                    "%s -y -loop 1 -framerate 30 -i %s -i %s " .
                    "-filter_complex %s -map %s -map 1:a:0 " .
                    "-c:v libx264 -preset fast -crf 24 -r 30 -pix_fmt yuv420p " .
                    "-c:a aac -b:a 192k " .
                    "-shortest -movflags +faststart %s 2>&1",
                    $ffmpeg,
                    escapeshellarg($imagePath),
                    escapeshellarg($audioPath),
                    escapeshellarg($filter),
                    escapeshellarg("[v]"),
                    escapeshellarg($outputPath)
                );
            }
            Log::info("GenerateReelJob: running FFmpeg", [
                "clip_id"    => $this->clipId,
                "image_path" => $imagePath,
                "audio_path" => $audioPath,
                "output"     => $outputPath,
            ]);
            $ffmpegOutput = shell_exec($cmd);

            foreach ($titleTempPaths as $tempPath) {
                if ($tempPath && file_exists($tempPath)) {
                    @unlink($tempPath);
                }
            }

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
