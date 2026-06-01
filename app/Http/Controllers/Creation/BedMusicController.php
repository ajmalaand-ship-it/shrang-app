<?php

namespace App\Http\Controllers\Creation;

use App\Actions\CreateClipAction;
use App\Http\Controllers\Controller;
use App\Jobs\GenerateBedMusicJob;
use App\Services\GenerationBillingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BedMusicController extends Controller
{
    public function __construct(
        private readonly GenerationBillingService $billing,
        private readonly CreateClipAction $createClip,
    ) {}

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            "title"       => ["nullable", "string", "max:200"],
            "description" => ["nullable", "string", "max:5000"],
            "language"    => ["required", "in:ps,fa,ur,ar,hi,en"],
            "mood"        => ["nullable", "string", "max:100"],
            "purpose"     => ["nullable", "string", "max:100"],
            "visibility"  => ["nullable", "in:public,private"],
        ]);

        $user = $request->user();

        $result = $this->createClip->execute([
            "user_id"          => $user->id,
            "title"            => $validated["title"] ?? "Background Music",
            "lyrics"           => $validated["description"] ?? "",
            "language"         => $validated["language"],
            "job_class"        => GenerateBedMusicJob::class,
            "ai_provider"      => "lyria",
            "credits_reserved" => 0,
        ]);

        $billing = $this->billing->checkAndReserve($user, "bed", $result["job"]->id);

        if (!$billing["ok"]) {
            return redirect()->route("create")
                ->withErrors(["credits" => $billing["message"]]);
        }

        GenerateBedMusicJob::dispatch($result["job"]->id, [
            "user_id"           => $user->id,
            "generation_job_id" => $result["job"]->id,
            "lyrics"            => $validated["description"] ?? "",
            "language"          => $validated["language"],
            "mood"              => $validated["mood"] ?? "",
            "purpose"           => $validated["purpose"] ?? "",
            "title"             => $validated["title"] ?? "Background Music",
        ])->onQueue("ai-generation");

        return redirect()->route("studio.show", $result["clip"]->id);
    }
}
