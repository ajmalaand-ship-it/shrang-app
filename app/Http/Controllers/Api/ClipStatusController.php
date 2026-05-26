<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Clip;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
class ClipStatusController extends Controller
{
    public function show(Request $request, Clip $clip): JsonResponse
    {
        $this->authorize('view', $clip);
        $coverAsset = $clip->mediaAssets()
            ->where('type', 'cover_image')
            ->where('is_primary', true)
            ->first();
        $coverJob = $clip->generationJobs()
            ->where('job_class', 'LIKE', '%Cover%')
            ->latest()
            ->first();
        return response()->json([
            'clip_status'   => $clip->status,
            'cover_ready'   => $coverAsset ? true : false,
            'cover_url'     => $coverAsset ? $coverAsset->cdn_url : null,
            'cover_status'  => $coverJob ? $coverJob->status : null,
        ]);
    }
}
