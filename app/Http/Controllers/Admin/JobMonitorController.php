<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\GenerationJob;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
class JobMonitorController extends Controller
{
    public function index(Request $request): View
    {
        $jobs = GenerationJob::query()
            ->with("user", "clip")
            ->when($request->status, fn($q) => $q->where("status", $request->status))
            ->orderByDesc("created_at")
            ->paginate(30);
        return view("pages.admin.jobs.index", compact("jobs"));
    }
}
