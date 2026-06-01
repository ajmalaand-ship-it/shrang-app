<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiUsageLog;
use Illuminate\View\View;

class AiUsageController extends Controller
{
    public function index(): View
    {
        $stats = [
            "total_calls"    => AiUsageLog::count(),
            "total_errors"   => AiUsageLog::where("status", "error")->count(),
            "total_cost_usd" => AiUsageLog::sum("provider_cost_usd"),
            "avg_latency_ms" => (int) AiUsageLog::avg("latency_ms"),
            "today_cost_usd" => AiUsageLog::whereDate("created_at", today())->sum("provider_cost_usd"),
            "week_cost_usd"  => AiUsageLog::where("created_at", ">=", now()->startOfWeek())->sum("provider_cost_usd"),
            "today_calls"    => AiUsageLog::whereDate("created_at", today())->count(),
            "week_calls"     => AiUsageLog::where("created_at", ">=", now()->startOfWeek())->count(),
        ];
        $recent = AiUsageLog::orderByDesc("created_at")->limit(50)->get();
        return view("pages.admin.ai-usage.index", compact("stats", "recent"));
    }
}
