<?php
namespace App\Http\Controllers\Studio;
use App\Http\Controllers\Controller;
use App\Models\Clip;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
class ReelController extends Controller
{
    public function store(Request $request, Clip $clip): RedirectResponse
    {
        $this->authorize("generateReel", $clip);
        return redirect()
            ->route("studio.show", $clip)
            ->with("success", "Reel generation coming in Phase 8.");
    }
}
