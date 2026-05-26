<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\GenerationJob;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Bus;
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
    public function retry(GenerationJob $job): RedirectResponse
    {
        if ($job->status !== "failed") {
            return back()->with("error", "Only failed jobs can be retried.");
        }
        $jobClass = $job->job_class;
        if (empty($jobClass) || !class_exists($jobClass)) {
            return back()->with("error", "Job class not found: " . $jobClass);
        }
        $job->update([
            "status"           => "pending",
            "progress_pct"     => 0,
            "progress_message" => null,
            "error_message"    => null,
            "attempts"         => 0,
        ]);
        $jobClass::dispatch($job->id, [
            "user_id"           => $job->user_id,
            "generation_job_id" => $job->id,
        ])->onQueue("ai-generation");
        return back()->with("success", "Job requeued successfully.");
    }
}
