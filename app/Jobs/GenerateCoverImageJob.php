<?php
namespace App\Jobs;
use App\Models\Clip;
use App\Models\MediaAsset;
use App\Services\AI\GeminiProvider;
use App\Services\PromptService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
class GenerateCoverImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public int $timeout = 120;
    public int $tries   = 3;
    public function __construct(
        private readonly string $clipId,
        private readonly array  $params
    ) {}
    public function handle(PromptService $promptService): void
    {
        $clip = Clip::findOrFail($this->clipId);
        try {
            $gemini = new GeminiProvider();
            $prompt = $promptService->buildCoverPrompt($this->params);
            $result = $gemini->generateCover(array_merge($this->params, ["prompt" => $prompt]));
            if ($result["status"] === "done" && !empty($result["image_data"])) {
                $ext        = str_contains($result["mime_type"] ?? "", "jpeg") ? "jpg" : "png";
                $filename   = "covers/" . $this->clipId . "." . $ext;
                Storage::disk("public")->put($filename, base64_decode($result["image_data"]));
                $coverUrl   = Storage::disk("public")->url($filename);
                MediaAsset::where("clip_id", $clip->id)
                    ->where("type", "cover_image")
                    ->update(["is_primary" => false]);
                MediaAsset::create([
                    "clip_id"         => $clip->id,
                    "user_id"         => $this->params["user_id"],
                    "type"            => "cover_image",
                    "storage_disk"    => "public",
                    "storage_key"     => $filename,
                    "cdn_url"         => $coverUrl,
                    "mime_type"       => $result["mime_type"] ?? "image/png",
                    "file_size_bytes" => strlen(base64_decode($result["image_data"])),
                    "is_primary"      => true,
                    "is_temp"         => false,
                ]);
                $clip->update(["cover_image_key" => $filename]);
                Log::info("Cover image generated", ["clip_id" => $this->clipId, "url" => $coverUrl]);
            } else {
                Log::error("GenerateCoverImageJob: no image data returned", ["clip_id" => $this->clipId, "result" => $result]);
            }
        } catch (\Exception $e) {
            Log::error("GenerateCoverImageJob failed", ["clip_id" => $this->clipId, "error" => $e->getMessage()]);
            throw $e;
        }
    }
}
