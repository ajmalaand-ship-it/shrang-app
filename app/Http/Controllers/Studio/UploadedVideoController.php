<?php

namespace App\Http\Controllers\Studio;

use App\Http\Controllers\Controller;
use App\Models\Clip;
use App\Models\MediaAsset;
use App\Services\AdminSettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadedVideoController extends Controller
{
    public function __construct(private readonly AdminSettingsService $settings) {}

    /**
     * Upload a user video to be used as the visual layer of a reel.
     * Mirrors CoverController::upload. Stores as MediaAsset type "uploaded_video".
     * Audio is NOT taken from this video; final reel audio is the clip audio.
     */
    public function upload(Request $request, Clip $clip): RedirectResponse
    {
        $this->authorize("update", $clip);

        if (!$this->settings->get("upload_video_enabled", true)) {
            return redirect()->route("studio.show", $clip)
                ->with("error", "Video upload is currently disabled.");
        }

        $maxMb   = (int) $this->settings->get("upload_video_max_mb", 100);
        if ($maxMb < 1) { $maxMb = 100; }
        $maxKb   = $maxMb * 1024;

        $formatsRaw = (string) $this->settings->get("upload_video_formats", "mp4,mov,webm");
        $formats = array_filter(array_map(fn($x) => strtolower(trim($x)), explode(",", $formatsRaw)));
        if (empty($formats)) { $formats = ["mp4", "mov", "webm"]; }
        $mimes = implode(",", $formats);

        $request->validate([
            "video_file" => [
                "required",
                "file",
                "mimes:{$mimes}",
                "max:{$maxKb}",
            ],
        ]);

        $file     = $request->file("video_file");
        $ext      = strtolower($file->getClientOriginalExtension());
        $filename = "uploaded-videos/" . $clip->id . "-" . Str::random(8) . "." . $ext;

        Storage::disk("public")->putFileAs(
            "uploaded-videos",
            $file,
            basename($filename)
        );

        $fullPath = storage_path("app/public/" . $filename);
        if (!file_exists($fullPath)) {
            return redirect()->route("studio.show", $clip)
                ->with("error", "Video upload failed. File could not be saved. Please try again.");
        }

        $fileSize = filesize($fullPath);
        $videoUrl = Storage::disk("public")->url($filename);

        // Only one active uploaded video per clip.
        MediaAsset::where("clip_id", $clip->id)
            ->where("type", "uploaded_video")
            ->update(["is_primary" => false]);

        MediaAsset::create([
            "clip_id"         => $clip->id,
            "user_id"         => $request->user()->id,
            "type"            => "uploaded_video",
            "storage_disk"    => "public",
            "storage_key"     => $filename,
            "cdn_url"         => $videoUrl,
            "mime_type"       => $file->getMimeType(),
            "file_size_bytes" => $fileSize,
            "is_primary"      => true,
            "is_temp"         => false,
        ]);

        return redirect()->route("studio.show", $clip)
            ->with("success", "Video uploaded successfully.");
    }

    /**
     * Delete the current uploaded video for this clip (file + DB row).
     */
    public function destroy(Request $request, Clip $clip): RedirectResponse
    {
        $this->authorize("update", $clip);

        $assets = MediaAsset::where("clip_id", $clip->id)
            ->where("type", "uploaded_video")
            ->get();

        foreach ($assets as $asset) {
            if ($asset->storage_key) {
                Storage::disk("public")->delete($asset->storage_key);
            }
            $asset->delete();
        }

        return redirect()->route("studio.show", $clip)
            ->with("success", "Uploaded video removed.");
    }
}
