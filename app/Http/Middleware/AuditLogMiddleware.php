<?php
namespace App\Http\Middleware;
use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
class AuditLogMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        if ($request->method() !== "GET") {
            try {
                AuditLog::create([
                    "actor_id"   => $request->user()?->id,
                    "actor_type" => "admin",
                    "action"     => $request->method() . " " . $request->path(),
                    "ip_address" => $request->ip(),
                    "user_agent" => $request->userAgent(),
                ]);
            } catch (\Exception $e) {
                // Never block a request due to audit log failure
            }
        }
        return $response;
    }
}
