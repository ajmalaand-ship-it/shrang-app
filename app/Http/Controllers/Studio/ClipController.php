<?php
namespace App\Http\Controllers\Studio;
use App\Http\Controllers\Controller;
use App\Models\Clip;
use App\Models\MediaAsset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
class ClipController extends Controller
{
    public function show(Request $request, Clip $clip): View
    {
        $this->authorize("view", $clip);
        $latestJob  = $clip->generationJobs()->latest()->first();
        $audioAsset = MediaAsset::where("clip_id", $clip->id)
            ->whereIn("type", ["song_audio", "bed_audio", "uploaded_audio"])
            ->where("is_primary", true)
            ->first();
        return view("pages.studio.index", compact("clip", "latestJob", "audioAsset"));
    }
    public function updateVisibility(Request $request, Clip $clip): RedirectResponse
    {
        $this->authorize("update", $clip);
        $validated = $request->validate([
            "visibility" => ["required", "in:public,private"],
        ]);
        $clip->update(["visibility" => $validated["visibility"]]);
        return redirect()->route("studio.show", $clip)->with("success", "Visibility updated.");
    }
}
