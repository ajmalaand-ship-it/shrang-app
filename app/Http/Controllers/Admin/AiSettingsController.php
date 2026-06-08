<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminSettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AiSettingsController extends Controller
{
    public function __construct(private readonly AdminSettingsService $settings) {}

    public function index(): View
    {
        $songMode = $this->settings->get('lyria_song_mode', 'dev_pro_60');
        $bedMode  = $this->settings->get('lyria_bed_mode',  'dev_pro_180');

        return view('pages.admin.ai.index', compact('songMode', 'bedMode'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'key'   => ['required', 'string', 'in:lyria_song_mode,lyria_bed_mode'],
            'value' => ['required', 'string', 'in:dev_clip_30,dev_pro_60,dev_pro_180,vertex_002_30,vertex_lyria3_pro'],
        ]);

        $this->settings->set($validated['key'], $validated['value'], $request->user()->id);

        return redirect()->route('admin.ai.index')
            ->with('success', 'AI setting updated.');
    }
}
