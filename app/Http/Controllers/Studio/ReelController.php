<?php
namespace App\Http\Controllers\Studio;
use App\Http\Controllers\Controller;
use App\Jobs\GenerateReelJob;
use App\Models\Clip;
use App\Models\GenerationJob;
use App\Models\MediaAsset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
class ReelController extends Controller
{
    public function store(Request $request, Clip $clip): RedirectResponse
    {
        $this->authorize("generateReel", $clip);
        // Check audio exists
        $audioAsset = MediaAsset::where("clip_id", $clip->id)
            ->whereIn("type", ["song_audio", "bed_audio", "uploaded_audio"])
            ->where("is_primary", true)
            ->first();
        if (!$audioAsset) {
            return redirect()->route("studio.show", $clip)
                ->with("error", "Audio is not ready yet. Please wait for song generation to complete.");
        }
        $audioPath = storage_path("app/public/" . $audioAsset->storage_key);
        if (!file_exists($audioPath)) {
            return redirect()->route("studio.show", $clip)
                ->with("error", "Audio file is missing from storage. Please regenerate the song.");
        }
        // Get cover path if exists
        $coverAsset = MediaAsset::where("clip_id", $clip->id)
            ->where("type", "cover_image")
            ->where("is_primary", true)
            ->first();
        $coverPath = null;
        if ($coverAsset && $coverAsset->storage_key) {
            $possiblePath = storage_path("app/public/" . $coverAsset->storage_key);
            if (file_exists($possiblePath)) {
                $coverPath = $possiblePath;
            }
        }
        // Phase 6a: optional reel template (plumbing only; all render cover_glow for now)
        $allowedTemplates = ["cover_glow", "minimal_dark", "poetry_poster"];
        $template = (string) $request->input("template", "cover_glow");
        if (!in_array($template, $allowedTemplates, true)) {
            $template = "cover_glow";
        }

        // Create generation job record
        $genJob = GenerationJob::create([
            "clip_id"          => $clip->id,
            "user_id"          => $request->user()->id,
            "job_class"        => GenerateReelJob::class,
            "ai_provider"      => "ffmpeg",
            "status"           => "pending",
            "credits_reserved" => 0,
        ]);
        GenerateReelJob::dispatch($clip->id, $genJob->id, [
            "user_id"    => $request->user()->id,
            "audio_path" => $audioPath,
            "cover_path" => $coverPath,
            "template"   => $template,
        ])->onQueue("default");
        return redirect()->route("studio.show", $clip)
            ->with("success", "Your reel is being created. This may take a minute. The page will update automatically.");
    }
}
