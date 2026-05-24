<?php
namespace App\Http\Controllers\Studio;
use App\Http\Controllers\Controller;
use App\Models\Clip;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
class ClipController extends Controller
{
    public function show(Request $request, Clip $clip): View
    {
        $this->authorize("view", $clip);
        $mediaAssets = $clip->generationJobs()
            ->with("clip")
            ->get();
        return view("pages.studio.index", compact("clip", "mediaAssets"));
    }
    public function updateVisibility(Request $request, Clip $clip): RedirectResponse
    {
        $this->authorize("update", $clip);
        $validated = $request->validate([
            "visibility" => ["required", "in:public,private"],
        ]);
        $clip->update(["visibility" => $validated["visibility"]]);
        return redirect()
            ->route("studio.show", $clip)
            ->with("success", "Visibility updated.");
    }
}
