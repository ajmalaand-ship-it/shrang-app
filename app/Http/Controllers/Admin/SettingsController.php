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
        $settings = AdminSetting::orderBy("group")->orderBy("key")->get();
        return view("pages.admin.settings.index", compact("settings"));
    }
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            "key"   => ["required", "string", "max:100"],
            "value" => ["required", "string", "max:1000"],
        ]);
        $this->settings->set($validated["key"], $validated["value"], $request->user()->id);
        return redirect()->route("admin.settings.index")
            ->with("success", "Setting updated.");
    }
}
