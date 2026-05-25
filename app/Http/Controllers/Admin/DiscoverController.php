<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Clip;
use App\Models\ClipFeature;
use App\Services\DiscoverService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
class DiscoverController extends Controller
{
    public function __construct(private readonly DiscoverService $discoverService) {}
    public function index(Request $request)
    {
        $featured  = $this->discoverService->getFeaturedForAdmin();
        $available = $this->discoverService->getPublicClipsForAdmin($request->get('search', ''));
        return view('pages.admin.discover.index', compact('featured', 'available'));
    }
    public function feature(Request $request, Clip $clip)
    {
        if ($clip->visibility !== 'public' || $clip->status !== 'ready') {
            return back()->with('error', 'Only public ready clips can be featured.');
        }
        ClipFeature::firstOrCreate(['clip_id' => $clip->id], [
            'id' => Str::uuid(), 'featured_by' => auth()->id(),
            'is_pinned' => false, 'is_blocked' => false, 'featured_at' => now(),
        ]);
        return back()->with('success', 'Clip added to Discover.');
    }
    public function unfeature(Clip $clip)
    {
        ClipFeature::where('clip_id', $clip->id)->delete();
        return back()->with('success', 'Clip removed from Discover.');
    }
    public function pin(Clip $clip)
    {
        $f = ClipFeature::where('clip_id', $clip->id)->firstOrFail();
        $f->update(['is_pinned' => !$f->is_pinned]);
        return back()->with('success', $f->is_pinned ? 'Pinned.' : 'Unpinned.');
    }
    public function block(Clip $clip)
    {
        $f = ClipFeature::where('clip_id', $clip->id)->firstOrFail();
        $f->update(['is_blocked' => !$f->is_blocked]);
        return back()->with('success', $f->is_blocked ? 'Blocked.' : 'Unblocked.');
    }
}
