<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\AiUsageLog;
use App\Models\Clip;
use App\Models\GenerationJob;
use App\Models\User;
use Illuminate\View\View;
class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            "total_users"    => User::count(),
            "total_clips"    => Clip::count(),
            "pending_jobs"   => GenerationJob::where("status", "pending")->count(),
            "failed_jobs"    => GenerationJob::where("status", "failed")->count(),
            "ai_errors_today"=> AiUsageLog::where("status", "error")
                ->whereDate("created_at", today())->count(),
        ];
        return view("pages.admin.dashboard", compact("stats"));
    }
}
