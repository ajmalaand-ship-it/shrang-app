<?php
namespace App\Http\Controllers;
use App\Models\Clip;
use App\Services\MediaService;
use Illuminate\Http\Request;
use Illuminate\View\View;
class PlayerController extends Controller
{
    public function __construct(private readonly MediaService $mediaService) {}
    public function show(Request $request, string $clipId): View
    {
        $clip = Clip::where("id", $clipId)
            ->where("visibility", "public")
            ->where("status", "ready")
            ->firstOrFail();
        $audioAsset = $this->mediaService->primaryAssetForClip($clip->id, "song_audio");
        $coverAsset = $this->mediaService->primaryAssetForClip($clip->id, "cover_image");
        $audioUrl = $audioAsset
            ? $this->mediaService->publicUrl($audioAsset)
            : null;
        $coverUrl = $coverAsset
            ? $this->mediaService->publicUrl($coverAsset)
            : null;
        return view("pages.player.show", compact("clip", "audioUrl", "coverUrl"));
    }
}
