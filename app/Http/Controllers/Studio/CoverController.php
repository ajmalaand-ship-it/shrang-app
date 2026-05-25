<?php
namespace App\Http\Controllers\Studio;
use App\Http\Controllers\Controller;
use App\Jobs\GenerateCoverImageJob;
use App\Models\Clip;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
class CoverController extends Controller
{
    public function store(Request $request, Clip $clip): RedirectResponse
    {
        $this->authorize("update", $clip);
        $validated = $request->validate([
            "description" => ["nullable", "string", "max:500"],
        ]);
        GenerateCoverImageJob::dispatch($clip->id, [
            "user_id"     => $request->user()->id,
            "title"       => $clip->title,
            "lyrics"      => $clip->lyrics_input,
            "description" => $validated["description"] ?? "",
            "language"    => $clip->language,
        ])->onQueue("ai-generation");
        return redirect()->route("studio.show", $clip)
            ->with("success", "Cover image is being generated. Refresh in a few seconds.");
    }
}
