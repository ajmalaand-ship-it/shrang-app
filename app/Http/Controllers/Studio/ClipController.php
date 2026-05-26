<?php
namespace App\Http\Controllers\Studio;
use App\Http\Controllers\Controller;
use App\Models\Clip;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
class ClipController extends Controller
{
    public function show(Clip $clip): View
    {
        $this->authorize('view', $clip);
        $latestJob = $clip->generationJobs()->latest()->first();
        $audioAsset = $clip->mediaAssets()
            ->whereIn('type', ['song_audio', 'bed_audio', 'uploaded_audio'])
            ->where('is_primary', true)
            ->first();
        $coverAsset = $clip->mediaAssets()
            ->where('type', 'cover_image')
            ->where('is_primary', true)
            ->first();
        $reel = $clip->mediaAssets()
            ->where('type', 'reel_video')
            ->where('is_primary', true)
            ->first();
        return view('pages.studio.index', compact('clip', 'latestJob', 'audioAsset', 'coverAsset', 'reel'));
    }
    public function updateVisibility(Request $request, Clip $clip): RedirectResponse
    {
        $this->authorize('update', $clip);
        $validated = $request->validate([
            'visibility' => ['required', 'in:public,private'],
        ]);
        $clip->update(['visibility' => $validated['visibility']]);
        return redirect()->route('studio.show', $clip)->with('success', 'Visibility updated.');
    }
    public function rename(Request $request, Clip $clip): RedirectResponse
    {
        $this->authorize('update', $clip);
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:200'],
        ]);
        $clip->update(['title' => $validated['title']]);
        return redirect()->route('studio.show', $clip)->with('success', 'Clip renamed.');
    }
    public function destroy(Request $request, Clip $clip): RedirectResponse
    {
        $this->authorize('delete', $clip);
        $clip->mediaAssets()->each(function ($asset) {
            if ($asset->storage_key) {
                \Illuminate\Support\Facades\Storage::disk($asset->storage_disk ?? 'public')->delete($asset->storage_key);
            }
            $asset->delete();
        });
        $clip->generationJobs()->delete();
        $clip->delete();
        return redirect()->route('dashboard')->with('success', 'Clip deleted.');
    }
}
