<?php

namespace App\Http\Controllers\Studio;

use App\Http\Controllers\Controller;
use App\Jobs\GenerateCoverImageJob;
use App\Models\Clip;
use App\Models\GenerationJob;
use App\Models\MediaAsset;
use App\Services\GenerationBillingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CoverController extends Controller
{
    public function __construct(
        private readonly GenerationBillingService $billing,
    ) {}

    public function upload(Request $request, Clip $clip): RedirectResponse
    {
        $this->authorize("update", $clip);
        $request->validate([
            "cover_file" => [
                "required",
                "file",
                "mimes:jpg,jpeg,png,webp",
                "max:5120",
            ],
        ]);
        $file     = $request->file("cover_file");
        $ext      = $file->getClientOriginalExtension();
        $filename = "covers/" . $clip->id . "-" . Str::random(8) . "." . strtolower($ext);
        Storage::disk("public")->putFileAs(
            "covers",
            $file,
            basename($filename)
        );
        $fullPath = storage_path("app/public/" . $filename);
        if (!file_exists($fullPath)) {
            return redirect()->route("studio.show", $clip)
                ->with("error", "Cover upload failed. File could not be saved. Please try again.");
        }
        $fileSize = filesize($fullPath);
        $coverUrl = Storage::disk("public")->url($filename);
        MediaAsset::where("clip_id", $clip->id)
            ->where("type", "cover_image")
            ->update(["is_primary" => false]);
        MediaAsset::create([
            "clip_id"         => $clip->id,
            "user_id"         => $request->user()->id,
            "type"            => "cover_image",
            "storage_disk"    => "public",
            "storage_key"     => $filename,
            "cdn_url"         => $coverUrl,
            "mime_type"       => $file->getMimeType(),
            "file_size_bytes" => $fileSize,
            "is_primary"      => true,
            "is_temp"         => false,
        ]);
        $clip->update(["cover_image_key" => $filename]);
        return redirect()->route("studio.show", $clip)
            ->with("success", "Cover image uploaded successfully.");
    }

    public function store(Request $request, Clip $clip): RedirectResponse
    {
        $this->authorize("update", $clip);
        $validated = $request->validate([
            "style"            => ["nullable", "string", "in:artistic,photo,poetic,cultural,cinematic,minimal,dramatic"],
            "mood"             => ["nullable", "string", "in:calm,romantic,sad,hopeful,patriotic,mystical,joyful,dramatic"],
            "visual_direction" => ["nullable", "string", "max:500"],
            "text_on_cover"    => ["nullable", "string", "in:none,title"],
        ]);

        $generationJob = GenerationJob::create([
            "clip_id"          => $clip->id,
            "user_id"          => $request->user()->id,
            "job_class"        => GenerateCoverImageJob::class,
            "ai_provider"      => "gemini",
            "status"           => "pending",
            "credits_reserved" => 0,
        ]);

        $billing = $this->billing->checkAndReserve($request->user(), "cover", $generationJob->id);

        if (!$billing["ok"]) {
            $generationJob->delete();
            return redirect()->route("studio.show", $clip)
                ->withErrors(["credits" => $billing["message"]]);
        }

        GenerateCoverImageJob::dispatch($clip->id, [
            "user_id"           => $request->user()->id,
            "generation_job_id" => $generationJob->id,
            "title"             => $clip->title,
            "lyrics"            => $clip->lyrics_input,
            "language"          => $clip->language,
            "style"             => $validated["style"] ?? "artistic",
            "mood"              => $validated["mood"] ?? "",
            "visual_direction"  => $validated["visual_direction"] ?? "",
            "text_on_cover"     => $validated["text_on_cover"] ?? "none",
        ])->onQueue("ai-generation");

        return redirect()->route("studio.show", $clip)
            ->with("success", "Your cover image is being generated. This may take up to a minute. The page will update automatically.");
    }
}
