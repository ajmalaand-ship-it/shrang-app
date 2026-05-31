<?php
namespace App\Http\Controllers;
use App\Models\Clip;
use Illuminate\Http\Request;
use Illuminate\View\View;
class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $clips = Clip::where("user_id", $request->user()->id)
            ->with(["mediaAssets" => function ($q) {
                $q->where("is_primary", true)
                  ->whereIn("type", ["song_audio","bed_audio","uploaded_audio","cover_image","reel_video"]);
            }])
            ->orderByDesc("created_at")
            ->paginate(20);

        $langNames = [
            "ps" => "Pashto", "fa" => "Dari", "ur" => "Urdu",
            "ar" => "Arabic", "hi" => "Hindi", "en" => "English",
        ];

        return view("pages.dashboard.index", compact("clips", "langNames"));
    }
}
