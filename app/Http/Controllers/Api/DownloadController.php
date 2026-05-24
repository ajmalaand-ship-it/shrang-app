<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\MediaAsset;
use App\Services\MediaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
class DownloadController extends Controller
{
    public function __construct(private readonly MediaService $mediaService) {}
    public function show(Request $request, MediaAsset $asset): JsonResponse
    {
        $clip = $asset->clip;
        if (!$clip) {
            abort(404);
        }
        $isOwner = $request->user() && $request->user()->id === $clip->user_id;
        $isPublic = $clip->visibility === "public";
        if (!$isOwner && !$isPublic) {
            abort(403);
        }
        $url = $this->mediaService->signedDownloadUrl($asset, 60);
        return response()->json([
            "url"        => $url,
            "expires_in" => 3600,
            "filename"   => $clip->title . "." . $this->getExtension($asset->mime_type),
        ]);
    }
    private function getExtension(string $mimeType): string
    {
        return match ($mimeType) {
            "audio/mpeg"  => "mp3",
            "audio/wav"   => "wav",
            "video/mp4"   => "mp4",
            "image/jpeg"  => "jpg",
            "image/png"   => "png",
            default       => "bin",
        };
    }
}
