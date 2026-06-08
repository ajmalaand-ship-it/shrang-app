<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\AdminSetting;
use App\Services\AdminSettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
class SettingsController extends Controller
{
    public function __construct(private readonly AdminSettingsService $settings) {}
    public function index(): View
    {
        $settings = AdminSetting::whereNotIn("key", ["lyria_song_mode", "lyria_bed_mode", "ai_music_provider", "song_duration_seconds", "bed_duration_seconds"])->orderBy("group")->orderBy("key")->get();
        return view("pages.admin.settings.index", compact("settings"));
    }
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            "key"   => ["required", "string", "max:100"],
            "value" => ["required", "string", "max:1000",
                \Illuminate\Validation\Rule::when(
                    in_array($request->input("key"), ["lyria_song_mode", "lyria_bed_mode"]),
                    ["in:dev_clip_30,dev_pro_60,dev_pro_180,vertex_002_30"]
                )],
        ]);
        $this->settings->set($validated["key"], $validated["value"], $request->user()->id);
        return redirect()->route("admin.settings.index")
            ->with("success", "Setting updated.");
    }
}
