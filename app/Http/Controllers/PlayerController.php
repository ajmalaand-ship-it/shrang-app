<?php
namespace App\Http\Controllers;
use App\Models\Clip;
use App\Services\MediaService;
use Illuminate\View\View;
class PlayerController extends Controller
{
    public function __construct(private readonly MediaService $mediaService) {}
    public function show(string $slug): View
    {
        $clip = Clip::where("slug", $slug)
            ->where("visibility", "public")
            ->where("status", "ready")
            ->firstOrFail();
        $audioAsset = $this->mediaService->primaryAssetForClip($clip->id, "song_audio")
            ?? $this->mediaService->primaryAssetForClip($clip->id, "bed_audio");
        $coverAsset  = $this->mediaService->primaryAssetForClip($clip->id, "cover_image");
        $audioUrl    = $audioAsset ? $this->mediaService->publicUrl($audioAsset) : null;
        $coverUrl    = $coverAsset ? $this->mediaService->publicUrl($coverAsset) : null;
        $downloadUrl = $audioAsset ? $this->mediaService->signedDownloadUrl($audioAsset) : null;
        $shareUrl    = route("player.show", $clip->slug);
        $embedCode   = '<iframe src="' . route("player.show", $clip->slug) . '" width="100%" height="180" frameborder="0" allow="autoplay"></iframe>';
        return view("pages.player.show", compact(
            "clip", "audioUrl", "coverUrl", "downloadUrl", "shareUrl", "embedCode"
        ));
    }
}
