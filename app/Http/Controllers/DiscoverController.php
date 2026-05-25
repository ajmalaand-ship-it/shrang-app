<?php
namespace App\Http\Controllers;
use App\Models\Clip;
use App\Services\ClipStatsService;
use App\Services\DiscoverService;
use App\Services\MediaService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
class DiscoverController extends Controller
{
    public function __construct(
        private readonly DiscoverService $discoverService,
        private readonly ClipStatsService $statsService,
        private readonly MediaService $mediaService,
    ) {}
    public function index(Request $request): View
    {
        $filters = $request->only(['language', 'type', 'sort']);
        $clips   = $this->discoverService->getFeatured($filters);
        $guestToken = $this->statsService->getGuestToken($request);
        $likedIds = [];
        if (auth()->check()) {
            $likedIds = \App\Models\ClipLike::where('user_id', auth()->id())
                ->whereIn('clip_id', $clips->pluck('id'))
                ->pluck('clip_id')->toArray();
        } else {
            $likedIds = \App\Models\ClipLike::where('guest_token', $guestToken)
                ->whereIn('clip_id', $clips->pluck('id'))
                ->pluck('clip_id')->toArray();
        }
        return view('pages.discover.index', compact('clips', 'filters', 'likedIds', 'guestToken'));
    }
    public function trackPlay(Request $request, string $slug): JsonResponse
    {
        $clip = Clip::where('slug', $slug)->where('visibility', 'public')->where('status', 'ready')->firstOrFail();
        $this->statsService->incrementPlay($clip);
        return response()->json(['ok' => true]);
    }
    public function like(Request $request, string $slug): JsonResponse
    {
        $clip = Clip::where('slug', $slug)->where('visibility', 'public')->firstOrFail();
        $guestToken = $this->statsService->getGuestToken($request);
        $userId = auth()->id();
        $ok = $this->statsService->like($clip, $userId, $guestToken, $request->ip());
        $count = $clip->fresh()->stat?->like_count ?? 0;
        return response()->json(['ok' => $ok, 'count' => $count])
            ->cookie('shrang_guest', $guestToken, 60 * 24 * 30);
    }
    public function unlike(Request $request, string $slug): JsonResponse
    {
        $clip = Clip::where('slug', $slug)->where('visibility', 'public')->firstOrFail();
        $guestToken = $this->statsService->getGuestToken($request);
        $userId = auth()->id();
        $ok = $this->statsService->unlike($clip, $userId, $guestToken);
        $count = $clip->fresh()->stat?->like_count ?? 0;
        return response()->json(['ok' => $ok, 'count' => $count]);
    }
    public function download(Request $request, string $slug): JsonResponse
    {
        $clip = Clip::where('slug', $slug)->where('visibility', 'public')->where('allow_download', true)->firstOrFail();
        $asset = $this->mediaService->primaryAssetForClip($clip->id, 'song_audio')
            ?? $this->mediaService->primaryAssetForClip($clip->id, 'bed_audio');
        if (!$asset) {
            return response()->json(['error' => 'No audio available'], 404);
        }
        $this->statsService->incrementDownload($clip);
        $url = $this->mediaService->signedDownloadUrl($asset);
        return response()->json(['url' => $url]);
    }
}
