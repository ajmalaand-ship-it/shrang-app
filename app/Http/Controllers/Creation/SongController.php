<?php

namespace App\Http\Controllers\Creation;

use App\Actions\CreateClipAction;
use App\Http\Controllers\Controller;
use App\Jobs\GenerateSongJob;
use App\Services\GenerationBillingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SongController extends Controller
{
    public function __construct(
        private readonly GenerationBillingService $billing,
        private readonly CreateClipAction $createClip,
    ) {}

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            "title"              => ["nullable", "string", "max:200"],
            "lyrics"             => ["required", "string", "max:5000"],
            "language"           => ["required", "in:ps,fa,ur,ar,hi,en"],
            "style"              => ["nullable", "string", "max:100"],
            "voice"              => ["nullable", "in:male,female,no_preference"],
            "creative_direction" => ["nullable", "string", "max:500"],
            "visibility"         => ["nullable", "in:public,private"],
            "legal"              => ["required", "accepted"],
        ]);

        $user = $request->user();

        $result = $this->createClip->execute([
            "user_id"          => $user->id,
            "title"            => $validated["title"] ?? "",
            "lyrics"           => $validated["lyrics"],
            "language"         => $validated["language"],
            "visibility"       => $validated["visibility"] ?? "private",
            "job_class"        => GenerateSongJob::class,
            "ai_provider"      => "lyria",
            "credits_reserved" => 0,
        ]);

        $billing = $this->billing->checkAndReserve($user, "song", $result["job"]->id);

        if (!$billing["ok"]) {
            return redirect()->route("create")
                ->withErrors(["credits" => $billing["message"]]);
        }

        GenerateSongJob::dispatch($result["job"]->id, [
            "user_id"            => $user->id,
            "generation_job_id"  => $result["job"]->id,
            "lyrics"             => $validated["lyrics"],
            "language"           => $validated["language"],
            "title"              => $validated["title"] ?? "",
            "style"              => $validated["style"] ?? "",
            "voice"              => $validated["voice"] ?? "no_preference",
            "creative_direction" => $validated["creative_direction"] ?? "",
        ])->onQueue("ai-generation");

        return redirect()->route("studio.show", $result["clip"]->id);
    }
}
