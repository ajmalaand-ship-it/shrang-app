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
            ->orderByDesc("created_at")
            ->paginate(20);
        return view("pages.dashboard.index", compact("clips"));
    }
}
