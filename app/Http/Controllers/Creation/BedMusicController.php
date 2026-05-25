<?php
namespace App\Http\Controllers\Creation;
use App\Actions\CreateClipAction;
use App\Http\Controllers\Controller;
use App\Jobs\GenerateBedMusicJob;
use App\Services\AdminSettingsService;
use App\Services\CreditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
class BedMusicController extends Controller
{
    public function __construct(
        private readonly CreditService $creditService,
        private readonly CreateClipAction $createClip,
        private readonly AdminSettingsService $settings
    ) {}
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            "title"       => ["nullable", "string", "max:200"],
            "description" => ["nullable", "string", "max:5000"],
            "language"    => ["required", "in:ps,fa,ur,ar,hi,en"],
        ]);
        $user       = $request->user();
        $creditCost = $this->settings->creditCost("bed");
        $spendable  = $this->creditService->spendableBalance($user);
        if ($spendable < $creditCost) {
            return redirect()->route("create")
                ->withErrors(["credits" => "Insufficient credits. You need {$creditCost} credits but have {$spendable}."]);
        }
        $result = $this->createClip->execute([
            "user_id"          => $user->id,
            "title"            => $validated["title"] ?? "Background Music",
            "lyrics"           => $validated["description"] ?? "",
            "language"         => $validated["language"],
            "job_class"        => GenerateBedMusicJob::class,
            "ai_provider"      => "lyria",
            "credits_reserved" => $creditCost,
        ]);
        $reserved = $this->creditService->checkAndReserve(
            $user, "bed", $result["job"]->id
        );
        if (!$reserved) {
            return redirect()->route("create")
                ->withErrors(["credits" => "Could not reserve credits. Please try again."]);
        }
        GenerateBedMusicJob::dispatch($result["job"]->id, [
            "user_id"           => $user->id,
            "generation_job_id" => $result["job"]->id,
            "lyrics"            => $validated["description"] ?? "",
            "language"          => $validated["language"],
            "title"             => $validated["title"] ?? "Background Music",
        ])->onQueue("ai-generation");
        return redirect()->route("studio.show", $result["clip"]->id);
    }
}
