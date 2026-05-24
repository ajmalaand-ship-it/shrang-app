<?php
namespace App\Jobs;
use App\Models\Clip;
use App\Models\MediaAsset;
use App\Services\AI\AIService;
use App\Services\PromptService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
class GenerateCoverImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public int $timeout = 300;
    public int $tries   = 3;
    public function __construct(
        private readonly string $clipId,
        private readonly array  $params
    ) {}
    public function handle(AIService $aiService, PromptService $promptService): void
    {
        $clip = Clip::findOrFail($this->clipId);
        try {
            $prompt = $promptService->buildCoverPrompt($this->params);
            $result = $aiService->generateCover(array_merge($this->params, ["prompt" => $prompt]));
            if ($result["status"] === "done" && isset($result["image_url"])) {
                MediaAsset::create([
                    "clip_id"      => $clip->id,
                    "user_id"      => $this->params["user_id"],
                    "type"         => "cover_image",
                    "storage_disk" => "public",
                    "storage_key"  => $result["image_url"],
                    "cdn_url"      => $result["image_url"],
                    "mime_type"    => "image/jpeg",
                    "file_size_bytes" => 0,
                    "is_primary"   => true,
                    "is_temp"      => false,
                ]);
                $clip->update(["cover_image_key" => $result["image_url"]]);
            }
        } catch (\Exception $e) {
            Log::error("GenerateCoverImageJob failed", [
                "clip_id" => $this->clipId,
                "error"   => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
