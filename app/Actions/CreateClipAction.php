<?php
namespace App\Actions;
use App\Models\Clip;
use App\Models\GenerationJob;
use App\Services\LanguageService;
use Illuminate\Support\Str;
class CreateClipAction
{
    public function __construct(private readonly LanguageService $languageService) {}
    public function execute(array $data): array
    {
        $language  = $data["language"] ?? "en";
        $direction = $this->languageService->getDirection($language)->value;
        $clip = Clip::create([
            "user_id"          => $data["user_id"],
            "title"            => $data["title"] ?? "Untitled",
            "lyrics_input"     => $data["lyrics"] ?? "",
            "language"         => $language,
            "script_direction" => $direction,
            "status"           => "processing",
            "visibility"       => "private",
        ]);
        $job = GenerationJob::create([
            "user_id"          => $data["user_id"],
            "clip_id"          => $clip->id,
            "job_class"        => $data["job_class"],
            "ai_provider"      => $data["ai_provider"] ?? "lyria",
            "status"           => "pending",
            "credits_reserved" => $data["credits_reserved"] ?? 0,
            "progress_pct"     => 0,
        ]);
        return [
            "clip" => $clip,
            "job"  => $job,
        ];
    }
}
