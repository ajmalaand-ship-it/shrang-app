<?php
namespace App\Http\Controllers;
use App\Models\Clip;
use App\Models\ClipLike;
use App\Services\ClipStatsService;
use App\Services\MediaService;
use Illuminate\Http\Request;
use Illuminate\View\View;
class PlayerController extends Controller
{
    public function __construct(
        private readonly MediaService $mediaService,
        private readonly ClipStatsService $statsService,
    ) {}
    public function show(Request $request, string $slug): View
    {
        $clip = Clip::where("slug", $slug)
            ->where("visibility", "public")
            ->where("status", "ready")
            ->firstOrFail();
        $audioAsset = $this->mediaService->primaryAssetForClip($clip->id, "song_audio")
            ?? $this->mediaService->primaryAssetForClip($clip->id, "bed_audio")
            ?? $this->mediaService->primaryAssetForClip($clip->id, "uploaded_audio");
        $coverAsset  = $this->mediaService->primaryAssetForClip($clip->id, "cover_image");
        $reelAsset   = $this->mediaService->primaryAssetForClip($clip->id, "reel_video");
        $audioUrl    = $audioAsset ? $this->mediaService->publicUrl($audioAsset) : null;
        $coverUrl    = $coverAsset ? $this->mediaService->publicUrl($coverAsset) : null;
        $reelUrl     = $reelAsset ? $this->mediaService->publicUrl($reelAsset) : null;
        $downloadUrl = $audioAsset ? $this->mediaService->signedDownloadUrl($audioAsset) : null;
        $shareUrl    = route("player.show", $clip->slug);
        $embedCode   = '<iframe src="' . route("player.show", $clip->slug) . '" width="100%" height="180" frameborder="0" allow="autoplay"></iframe>';

        $guestToken = $this->statsService->getGuestToken($request);
        $likeCount = $clip->stat?->like_count ?? 0;

        if (auth()->check()) {
            $liked = ClipLike::where("clip_id", $clip->id)
                ->where("user_id", auth()->id())
                ->exists();
        } else {
            $liked = ClipLike::where("clip_id", $clip->id)
                ->where("guest_token", $guestToken)
                ->exists();
        }
        // OG image uses cover until reel thumbnail extraction is available.
        // Do not use MP4 reel URL as og:image.
        $ogImageUrl  = $coverUrl;
        return view("pages.player.show", compact(
            "clip",
            "audioUrl",
            "coverUrl",
            "reelUrl",
            "downloadUrl",
            "shareUrl",
            "embedCode",
            "ogImageUrl",
            "liked",
            "likeCount",
            "guestToken"
        ));
    }
}
