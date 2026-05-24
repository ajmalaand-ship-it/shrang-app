<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
class CdnCacheHeaders
{
    public function handle(Request $request, Closure $next, int $maxAge = 3600): Response
    {
        $response = $next($request);
        if ($response->isSuccessful()) {
            $response->headers->set(
                "Cache-Control",
                "public, max-age={$maxAge}, s-maxage={$maxAge}, stale-while-revalidate=60"
            );
        }
        return $response;
    }
}
