<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\GenerationJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
class JobStatusController extends Controller
{
    public function show(Request $request, string $jobId): JsonResponse
    {
        $job = GenerationJob::where("id", $jobId)
            ->where("user_id", $request->user()->id)
            ->firstOrFail();
        return response()->json([
            "id"               => $job->id,
            "status"           => $job->status,
            "progress_pct"     => $job->progress_pct,
            "progress_message" => $job->progress_message,
            "clip_id"          => $job->clip_id,
            "error_message"    => $job->error_message,
            "completed_at"     => $job->completed_at,
        ]);
    }
}
