<?php
namespace App\Http\Controllers\Creation;
use App\Actions\CreateClipAction;
use App\Http\Controllers\Controller;
use App\Jobs\GenerateSongJob;
use App\Services\CreditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
class SongController extends Controller
{
    public function __construct(
        private readonly CreditService $creditService,
        private readonly CreateClipAction $createClip
    ) {}
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            "title"    => ["nullable", "string", "max:200"],
            "lyrics"   => ["required", "string", "max:5000"],
            "language" => ["required", "in:ps,fa,ur,ar,hi,en"],
        ]);
        $user = $request->user();
        $reserved = $this->creditService->checkAndReserve($user, "song", "pending");
        if (!$reserved) {
            return response()->json(["error" => "Insufficient credits"], 422);
        }
        $result = $this->createClip->execute([
            "user_id"          => $user->id,
            "title"            => $validated["title"] ?? "Untitled",
            "lyrics"           => $validated["lyrics"],
            "language"         => $validated["language"],
            "job_class"        => GenerateSongJob::class,
            "ai_provider"      => "lyria",
            "credits_reserved" => 10,
        ]);
        $this->creditService->checkAndReserve($user, "song", $result["job"]->id);
        GenerateSongJob::dispatch($result["job"]->id, [
            "user_id"           => $user->id,
            "generation_job_id" => $result["job"]->id,
            "lyrics"            => $validated["lyrics"],
            "language"          => $validated["language"],
            "title"             => $validated["title"] ?? "",
        ])->onQueue("ai-generation");
        return response()->json([
            "job_id"  => $result["job"]->id,
            "clip_id" => $result["clip"]->id,
            "status"  => "pending",
        ]);
    }
}
