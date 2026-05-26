<?php
namespace App\Services;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter as RateLimiterFacade;
class RateLimiterService
{
    public function tooManyGenerationAttempts(Request $request): bool
    {
        $key = $this->resolveKey($request, 'generate');
        return RateLimiterFacade::tooManyAttempts($key, $this->maxAttempts($request));
    }
    public function hitGenerationLimit(Request $request): void
    {
        $key = $this->resolveKey($request, 'generate');
        RateLimiterFacade::hit($key, 3600);
    }
    public function tooManyLikeAttempts(Request $request): bool
    {
        $key = $this->resolveKey($request, 'like');
        return RateLimiterFacade::tooManyAttempts($key, 10);
    }
    public function hitLikeLimit(Request $request): void
    {
        $key = $this->resolveKey($request, 'like');
        RateLimiterFacade::hit($key, 3600);
    }
    public function tooManyPlayAttempts(Request $request, string $slug): bool
    {
        $key = 'play:' . $slug . ':' . $request->ip();
        return RateLimiterFacade::tooManyAttempts($key, 1);
    }
    public function hitPlayLimit(Request $request, string $slug): void
    {
        $key = 'play:' . $slug . ':' . $request->ip();
        RateLimiterFacade::hit($key, 3600);
    }
    private function resolveKey(Request $request, string $action): string
    {
        if ($request->user()) {
            return $action . ':user:' . $request->user()->id;
        }
        return $action . ':guest:' . $request->ip();
    }
    private function maxAttempts(Request $request): int
    {
        if ($request->user()) {
            return 20;
        }
        return 3;
    }
}
