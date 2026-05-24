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
        $this->authorize("generateCover", $clip);
        $validated = $request->validate([
            "description" => ["nullable", "string", "max:500"],
        ]);
        GenerateCoverImageJob::dispatch($clip->id, [
            "user_id"     => $request->user()->id,
            "lyrics"      => $clip->lyrics_input,
            "title"       => $clip->title,
            "description" => $validated["description"] ?? "",
        ])->onQueue("media-processing");
        return redirect()
            ->route("studio.show", $clip)
            ->with("success", "Cover generation started.");
    }
}
