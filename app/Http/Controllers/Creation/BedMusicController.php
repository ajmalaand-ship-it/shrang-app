<?php
namespace App\Http\Controllers\Creation;
use App\Actions\CreateClipAction;
use App\Http\Controllers\Controller;
use App\Services\CreditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
class BedMusicController extends Controller
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
        $reserved = $this->creditService->checkAndReserve($user, "bed", "pending");
        if (!$reserved) {
            return response()->json(["error" => "Insufficient credits"], 422);
        }
        return response()->json(["status" => "pending", "message" => "Bed music generation coming in Phase 6"]);
    }
}
