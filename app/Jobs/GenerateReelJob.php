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

    /** Allowed reel templates (Phase 6a plumbing; all render cover_glow for now). */
    private const TEMPLATES = ["cover_glow", "minimal_dark", "poetry_poster"];
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

        $template = $this->params["template"] ?? "cover_glow";
        if (!in_array($template, self::TEMPLATES, true)) {
            $template = "cover_glow";
        }
        Log::info("GenerateReelJob: template", ["clip_id" => $this->clipId, "template" => $template]);

        $titleTempPaths = [];

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

            $overlayDir = storage_path("app/private/reel-overlays");
            if (!is_dir($overlayDir)) {
                mkdir($overlayDir, 0755, true);
            }

            // ---- Title overlay (clean white Pango text, RTL-safe) ----
            $titleOverlayPath = null;
            $titleText = trim((string) ($clip->display_title ?? ""));
            $titleText = preg_replace('/[\x00-\x1F\x7F\x{061C}\x{200B}\x{200E}\x{200F}\x{202A}-\x{202E}\x{2066}-\x{2069}]/u', '', $titleText) ?? "";
            $titleText = trim($titleText);

            if ($titleText !== "" && $titleText !== "Untitled Clip") {
                $titleText = mb_substr($titleText, 0, 80);

                $titleOverlayPath = $overlayDir . "/{$clip->id}-{$this->generationJobId}-title.png";
                $titleTempPaths[] = $titleOverlayPath;

                $titleMarkup = 'pango:<span font="Vazirmatn Bold 52" foreground="white">' .
                    htmlspecialchars($titleText, ENT_QUOTES | ENT_XML1, 'UTF-8') .
                    '</span>';

                $titleCmd = sprintf(
                    "/usr/bin/convert -background none -size 920x300 -gravity center %s -trim +repage -bordercolor none -border 20 PNG32:%s 2>&1",
                    escapeshellarg($titleMarkup),
                    escapeshellarg($titleOverlayPath)
                );
                exec($titleCmd, $titleOut, $titleCode);

                if ($titleCode !== 0 || !file_exists($titleOverlayPath)) {
                    Log::warning("GenerateReelJob: title overlay creation failed", [
                        "clip_id" => $this->clipId,
                        "title_output" => implode("\n", $titleOut ?? []),
                    ]);
                    $titleOverlayPath = null;
                }
            }

            // ---- Rounded cover with soft orange glow (cover_glow template only) ----
            $coverArtPath = null;
            $hasCover = ($coverPath && file_exists($coverPath));
            if ($hasCover && $template === "cover_glow") {
                $coverRoundPath = $overlayDir . "/{$clip->id}-{$this->generationJobId}-cover-round.png";
                $coverArtPath   = $overlayDir . "/{$clip->id}-{$this->generationJobId}-cover-glow.png";
                $titleTempPaths[] = $coverRoundPath;
                $titleTempPaths[] = $coverArtPath;

                $roundCmd = sprintf(
                    "/usr/bin/convert %s -resize 820x820^ -gravity center -extent 820x820 ".
                    "\\( +clone -alpha extract -draw 'fill black polygon 0,0 0,40 40,0 fill white circle 40,40 40,0' ".
                    "\\( +clone -flip \\) -compose Multiply -composite ".
                    "\\( +clone -flop \\) -compose Multiply -composite \\) ".
                    "-alpha off -compose CopyOpacity -composite PNG32:%s 2>&1",
                    escapeshellarg($coverPath),
                    escapeshellarg($coverRoundPath)
                );
                exec($roundCmd, $roundOut, $roundCode);

                $glowCmd = sprintf(
                    "/usr/bin/convert -size 920x920 xc:none ".
                    "\\( -size 820x820 xc:'#FF9A4A' -gravity center -background none -extent 920x920 -blur 0x22 \\) -composite ".
                    "%s -gravity center -composite PNG32:%s 2>&1",
                    escapeshellarg($coverRoundPath),
                    escapeshellarg($coverArtPath)
                );
                exec($glowCmd, $glowOut, $glowCode);

                if (($roundCode ?? 1) !== 0 || ($glowCode ?? 1) !== 0 || !file_exists($coverArtPath)) {
                    Log::warning("GenerateReelJob: cover art creation failed", [
                        "clip_id" => $this->clipId,
                        "round_output" => implode("\n", $roundOut ?? []),
                        "glow_output"  => implode("\n", $glowOut ?? []),
                    ]);
                    $coverArtPath = null;
                }
            }

            // ---- Probe audio duration for duration-aware motion ----
            $durCmd = sprintf(
                "/usr/bin/ffprobe -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 %s 2>&1",
                escapeshellarg($audioPath)
            );
            $durRaw = trim((string) shell_exec($durCmd));
            $dur = is_numeric($durRaw) ? (float) $durRaw : 60.0;
            if ($dur < 1) { $dur = 60.0; }

            // ---- Smooth duration-aware Ken Burns zoom (no zoompan, no wobble) ----
            $bgZoom = "scale=w='2160*(1+min(0.0015*t,0.20))':h=-1:eval=frame,crop=1080:1920";
            $fgZoom = "scale=w='920*(1+min(0.0012*t,0.09))':h=-1:eval=frame";

            if ($hasCover && $template === "minimal_dark") {
                $cmd = $this->buildMinimalDark($overlayDir, $clip, $coverPath, $audioPath, $outputPath, $titleOverlayPath, $titleTempPaths);
            } elseif ($hasCover && $template === "poetry_poster") {
                $cmd = $this->buildPoetryPoster($overlayDir, $clip, $coverPath, $audioPath, $outputPath, $titleOverlayPath, $titleTempPaths);
            } elseif ($coverArtPath && file_exists($coverArtPath)) {
                // cover_glow (unchanged). Inputs: 0 = cover (blurred bg), 1 = audio, 2 = glow cover, 3 = title (optional)
                $filter = "[0:v]scale=2160:3840:force_original_aspect_ratio=increase,crop=2160:3840," .
                    "{$bgZoom},gblur=sigma=24,eq=brightness=-0.05:saturation=1.06,vignette=angle=PI/8[bgz];" .
                    "color=black:s=1080x720:d={$dur},format=rgba,geq=r=0:g=0:b=0:a='245*(Y/720)'[fade];" .
                    "[bgz][fade]overlay=0:1200[bg];" .
                    "[2:v]format=rgba[cov];" .
                    "[bg][cov]overlay=(W-w)/2:'380+20*sin(t*0.9)'[base]";

                if ($titleOverlayPath && file_exists($titleOverlayPath)) {
                    $filter .= ";[base][3:v]overlay=(W-w)/2:1380,format=yuv420p[v]";
                    $cmd = sprintf(
                        "%s -y -loop 1 -framerate 30 -i %s -i %s -loop 1 -i %s -loop 1 -i %s " .
                        "-filter_complex %s -map %s -map 1:a:0 " .
                        "-c:v libx264 -preset fast -crf 22 -r 30 -pix_fmt yuv420p " .
                        "-c:a aac -b:a 192k -shortest -movflags +faststart %s 2>&1",
                        $ffmpeg, escapeshellarg($imagePath), escapeshellarg($audioPath),
                        escapeshellarg($coverArtPath), escapeshellarg($titleOverlayPath),
                        escapeshellarg($filter), escapeshellarg("[v]"), escapeshellarg($outputPath)
                    );
                } else {
                    $filter .= ",format=yuv420p[v]";
                    $cmd = sprintf(
                        "%s -y -loop 1 -framerate 30 -i %s -i %s -loop 1 -i %s " .
                        "-filter_complex %s -map %s -map 1:a:0 " .
                        "-c:v libx264 -preset fast -crf 22 -r 30 -pix_fmt yuv420p " .
                        "-c:a aac -b:a 192k -shortest -movflags +faststart %s 2>&1",
                        $ffmpeg, escapeshellarg($imagePath), escapeshellarg($audioPath),
                        escapeshellarg($coverArtPath),
                        escapeshellarg($filter), escapeshellarg("[v]"), escapeshellarg($outputPath)
                    );
                }
            } else {
                // ---- No-cover fallback: Shrang brand background + center icon + rings ----
                $ncStill = base_path("public/images/nc-still.png");
                $ncRings = base_path("public/images/nc-rings.png");
                $ncIglow = base_path("public/images/nc-iglow.png");
                $ncIcon  = base_path("public/images/shrang-icon.png");

                if (file_exists($ncStill) && file_exists($ncRings) && file_exists($ncIglow) && file_exists($ncIcon)) {
                    $ncFilter =
                        "[0:v]scale=2160:3840:force_original_aspect_ratio=increase,crop=2160:3840," .
                        "scale=w='2160*(1+min(0.0010*t,0.06))':h=-1:eval=frame,crop=1080:1920,vignette=angle=PI/6[bg];" .
                        "[2:v]scale=w='1100*(1+0.03*sin(t*0.55))':h=-1:eval=frame[rings];" .
                        "[bg][rings]overlay=x='(W-w)/2':y='720-h/2'[a];" .
                        "[a][3:v]overlay=x='(W-w)/2':y=370[b];" .
                        "[4:v]scale=460:-1[ic];" .
                        "[b][ic]overlay=x='(W-w)/2':y=490";

                    if ($titleOverlayPath && file_exists($titleOverlayPath)) {
                        $ncFilter .= "[c];[c][5:v]overlay=(W-w)/2:1180,format=yuv420p[v]";
                        $cmd = sprintf(
                            "%s -y -loop 1 -framerate 30 -i %s -i %s -loop 1 -i %s -loop 1 -i %s -loop 1 -i %s -loop 1 -i %s " .
                            "-filter_complex %s -map %s -map 1:a:0 " .
                            "-c:v libx264 -preset fast -crf 22 -r 30 -pix_fmt yuv420p " .
                            "-c:a aac -b:a 192k -shortest -movflags +faststart %s 2>&1",
                            $ffmpeg, escapeshellarg($ncStill), escapeshellarg($audioPath),
                            escapeshellarg($ncRings), escapeshellarg($ncIglow), escapeshellarg($ncIcon),
                            escapeshellarg($titleOverlayPath),
                            escapeshellarg($ncFilter), escapeshellarg("[v]"), escapeshellarg($outputPath)
                        );
                    } else {
                        $ncFilter .= ",format=yuv420p[v]";
                        $cmd = sprintf(
                            "%s -y -loop 1 -framerate 30 -i %s -i %s -loop 1 -i %s -loop 1 -i %s -loop 1 -i %s " .
                            "-filter_complex %s -map %s -map 1:a:0 " .
                            "-c:v libx264 -preset fast -crf 22 -r 30 -pix_fmt yuv420p " .
                            "-c:a aac -b:a 192k -shortest -movflags +faststart %s 2>&1",
                            $ffmpeg, escapeshellarg($ncStill), escapeshellarg($audioPath),
                            escapeshellarg($ncRings), escapeshellarg($ncIglow), escapeshellarg($ncIcon),
                            escapeshellarg($ncFilter), escapeshellarg("[v]"), escapeshellarg($outputPath)
                        );
                    }
                } else {
                    // Safety fallback: simple background if brand assets are missing
                    $filter = "[0:v]scale=2160:3840:force_original_aspect_ratio=increase,crop=2160:3840," .
                        "{$bgZoom},gblur=sigma=10,eq=brightness=-0.03:saturation=1.04,vignette=angle=PI/8[bg]";
                    if ($titleOverlayPath && file_exists($titleOverlayPath)) {
                        $filter .= ";[bg][2:v]overlay=(W-w)/2:1380,format=yuv420p[v]";
                        $cmd = sprintf(
                            "%s -y -loop 1 -framerate 30 -i %s -i %s -loop 1 -i %s " .
                            "-filter_complex %s -map %s -map 1:a:0 " .
                            "-c:v libx264 -preset fast -crf 22 -r 30 -pix_fmt yuv420p " .
                            "-c:a aac -b:a 192k -shortest -movflags +faststart %s 2>&1",
                            $ffmpeg, escapeshellarg($imagePath), escapeshellarg($audioPath),
                            escapeshellarg($titleOverlayPath),
                            escapeshellarg($filter), escapeshellarg("[v]"), escapeshellarg($outputPath)
                        );
                    } else {
                        $filter .= ",format=yuv420p[v]";
                        $cmd = sprintf(
                            "%s -y -loop 1 -framerate 30 -i %s -i %s " .
                            "-filter_complex %s -map %s -map 1:a:0 " .
                            "-c:v libx264 -preset fast -crf 22 -r 30 -pix_fmt yuv420p " .
                            "-c:a aac -b:a 192k -shortest -movflags +faststart %s 2>&1",
                            $ffmpeg, escapeshellarg($imagePath), escapeshellarg($audioPath),
                            escapeshellarg($filter), escapeshellarg("[v]"), escapeshellarg($outputPath)
                        );
                    }
                }
            }

            Log::info("GenerateReelJob: running FFmpeg", [
                "clip_id"    => $this->clipId,
                "image_path" => $imagePath,
                "audio_path" => $audioPath,
                "duration"   => $dur,
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
        } finally {
            foreach ($titleTempPaths as $tempPath) {
                if ($tempPath && file_exists($tempPath)) {
                    @unlink($tempPath);
                }
            }
        }
    }

    /** Build the FFmpeg command for the MINIMAL DARK template (cover hero on warm charcoal). */
    private function buildMinimalDark(string $overlayDir, $clip, string $coverPath, string $audioPath, string $outputPath, ?string $titleOverlayPath, array &$titleTempPaths): string
    {
        $C = "/usr/bin/convert";
        $base = "{$overlayDir}/{$clip->id}-{$this->generationJobId}-md";
        $still = "{$base}-still.png"; $cov = "{$base}-cover.png";
        $gA = "{$base}-base.png"; $gW = "{$base}-warm.png"; $fd = "{$base}-fade.png"; $cr = "{$base}-cr.png";
        foreach ([$still,$cov,$gA,$gW,$fd,$cr] as $p) { $titleTempPaths[] = $p; }

        exec(sprintf("%s -size 1080x1920 radial-gradient:'#241C16'-'#120D0A' %s 2>&1", $C, escapeshellarg($gA)));
        exec(sprintf("%s -size 1000x1000 radial-gradient:'#E8732A'-'#E8732A00' -blur 0x95 %s 2>&1", $C, escapeshellarg($gW)));
        exec(sprintf("%s -size 1080x600 gradient:none-'#0C0805' %s 2>&1", $C, escapeshellarg($fd)));
        exec(sprintf("%s %s \\( %s -channel A -evaluate multiply 0.20 +channel \\) -geometry +40+220 -compose screen -composite %s -geometry +0+1320 -compose over -composite %s 2>&1",
            $C, escapeshellarg($gA), escapeshellarg($gW), escapeshellarg($fd), escapeshellarg($still)));
        exec(sprintf("%s %s -resize 760x760^ -gravity center -extent 760x760 \\( +clone -alpha extract -draw 'fill black polygon 0,0 0,44 44,0 fill white circle 44,44 44,0' \\( +clone -flip \\) -compose Multiply -composite \\( +clone -flop \\) -compose Multiply -composite \\) -alpha off -compose CopyOpacity -composite PNG32:%s 2>&1",
            $C, escapeshellarg($coverPath), escapeshellarg($cr)));
        exec(sprintf("%s %s \\( +clone -background black -shadow 55x20+0+12 \\) +swap -background none -layers merge +repage PNG32:%s 2>&1",
            $C, escapeshellarg($cr), escapeshellarg($cov)));

        $bg = "[0:v]scale=2160:3840:force_original_aspect_ratio=increase,crop=2160:3840,scale=w='2160*(1+min(0.0008*t,0.05))':h=-1:eval=frame,crop=1080:1920,vignette=angle=PI/4.5[bg];[2:v]format=rgba[cov];[bg][cov]overlay=(W-w)/2:'350+16*sin(t*0.8)'[b]";
        if ($titleOverlayPath && file_exists($titleOverlayPath)) {
            $bg .= ";[b][3:v]overlay=(W-w)/2:1330,format=yuv420p[v]";
            return sprintf("/usr/bin/ffmpeg -y -loop 1 -framerate 30 -i %s -i %s -loop 1 -i %s -loop 1 -i %s -filter_complex %s -map %s -map 1:a:0 -c:v libx264 -preset fast -crf 22 -r 30 -pix_fmt yuv420p -c:a aac -b:a 192k -shortest -movflags +faststart %s 2>&1",
                escapeshellarg($still), escapeshellarg($audioPath), escapeshellarg($cov), escapeshellarg($titleOverlayPath),
                escapeshellarg($bg), escapeshellarg("[v]"), escapeshellarg($outputPath));
        }
        $bg .= ",format=yuv420p[v]";
        return sprintf("/usr/bin/ffmpeg -y -loop 1 -framerate 30 -i %s -i %s -loop 1 -i %s -filter_complex %s -map %s -map 1:a:0 -c:v libx264 -preset fast -crf 22 -r 30 -pix_fmt yuv420p -c:a aac -b:a 192k -shortest -movflags +faststart %s 2>&1",
            escapeshellarg($still), escapeshellarg($audioPath), escapeshellarg($cov),
            escapeshellarg($bg), escapeshellarg("[v]"), escapeshellarg($outputPath));
    }

    /** Build the FFmpeg command for the POETRY POSTER template (cover-colored ambient wash). */
    private function buildPoetryPoster(string $overlayDir, $clip, string $coverPath, string $audioPath, string $outputPath, ?string $titleOverlayPath, array &$titleTempPaths): string
    {
        $C = "/usr/bin/convert";
        $base = "{$overlayDir}/{$clip->id}-{$this->generationJobId}-pp";
        $bgfull = "{$base}-bg.png"; $cov = "{$base}-cover.png"; $line = "{$base}-line.png";
        $bgt = "{$base}-bgt.png"; $fb = "{$base}-fb.png"; $ft = "{$base}-ft.png"; $cr = "{$base}-cr.png";
        foreach ([$bgfull,$cov,$line,$bgt,$fb,$ft,$cr] as $p) { $titleTempPaths[] = $p; }

        exec(sprintf("%s %s -resize 1080x1920^ -gravity center -extent 1080x1920 -blur 0x45 -modulate 70,115 -fill black -colorize 18%% %s 2>&1",
            $C, escapeshellarg($coverPath), escapeshellarg($bgt)));
        exec(sprintf("%s -size 1080x820 gradient:none-'#08060A' %s 2>&1", $C, escapeshellarg($fb)));
        exec(sprintf("%s -size 1080x460 gradient:'#08060A'-none %s 2>&1", $C, escapeshellarg($ft)));
        exec(sprintf("%s %s %s -gravity south -geometry +0+0 -compose over -composite %s -gravity north -geometry +0+0 -compose over -composite %s 2>&1",
            $C, escapeshellarg($bgt), escapeshellarg($fb), escapeshellarg($ft), escapeshellarg($bgfull)));
        exec(sprintf("%s %s -resize 720x720^ -gravity center -extent 720x720 \\( +clone -alpha extract -draw 'fill black polygon 0,0 0,36 36,0 fill white circle 36,36 36,0' \\( +clone -flip \\) -compose Multiply -composite \\( +clone -flop \\) -compose Multiply -composite \\) -alpha off -compose CopyOpacity -composite PNG32:%s 2>&1",
            $C, escapeshellarg($coverPath), escapeshellarg($cr)));
        exec(sprintf("%s %s \\( +clone -background black -shadow 60x24+0+14 \\) +swap -background none -layers merge +repage PNG32:%s 2>&1",
            $C, escapeshellarg($cr), escapeshellarg($cov)));
        exec(sprintf("%s -size 130x4 xc:'#FFD3A8' -alpha set -channel A -evaluate multiply 0.85 +channel PNG32:%s 2>&1",
            $C, escapeshellarg($line)));

        $bg = "[0:v]scale=2160:3840:force_original_aspect_ratio=increase,crop=2160:3840,scale=w='2160*(1+min(0.0007*t,0.045))':h=-1:eval=frame,crop=1080:1920[bg];[2:v]format=rgba[cov];[bg][cov]overlay=(W-w)/2:'330+10*sin(t*0.7)'[b];[b][3:v]overlay=(W-w)/2:1300[c]";
        if ($titleOverlayPath && file_exists($titleOverlayPath)) {
            $bg .= ";[c][4:v]overlay=(W-w)/2:1345,format=yuv420p[v]";
            return sprintf("/usr/bin/ffmpeg -y -loop 1 -framerate 30 -i %s -i %s -loop 1 -i %s -loop 1 -i %s -loop 1 -i %s -filter_complex %s -map %s -map 1:a:0 -c:v libx264 -preset fast -crf 22 -r 30 -pix_fmt yuv420p -c:a aac -b:a 192k -shortest -movflags +faststart %s 2>&1",
                escapeshellarg($bgfull), escapeshellarg($audioPath), escapeshellarg($cov), escapeshellarg($line), escapeshellarg($titleOverlayPath),
                escapeshellarg($bg), escapeshellarg("[v]"), escapeshellarg($outputPath));
        }
        $bg .= ",format=yuv420p[v]";
        return sprintf("/usr/bin/ffmpeg -y -loop 1 -framerate 30 -i %s -i %s -loop 1 -i %s -loop 1 -i %s -filter_complex %s -map %s -map 1:a:0 -c:v libx264 -preset fast -crf 22 -r 30 -pix_fmt yuv420p -c:a aac -b:a 192k -shortest -movflags +faststart %s 2>&1",
            escapeshellarg($bgfull), escapeshellarg($audioPath), escapeshellarg($cov), escapeshellarg($line),
            escapeshellarg($bg), escapeshellarg("[v]"), escapeshellarg($outputPath));
    }
}
