<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\View\View;
class AuditLogController extends Controller
{
    public function index(): View
    {
        $logs = AuditLog::with("actor")
            ->orderByDesc("created_at")
            ->paginate(50);
        return view("pages.admin.audit-log.index", compact("logs"));
    }
}
