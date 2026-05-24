<?php
namespace App\Services;
use Illuminate\Support\Facades\RateLimiter;
class RateLimiterService
{
    public function tooManyCreationAttempts(string $userId): bool
    {
        $key = "create:{$userId}";
        if (RateLimiter::tooManyAttempts($key, 10)) {
            return true;
        }
        RateLimiter::hit($key, 3600);
        return false;
    }
    public function tooManyGuestAttempts(string $ip): bool
    {
        $key = "guest:{$ip}";
        if (RateLimiter::tooManyAttempts($key, 30)) {
            return true;
        }
        RateLimiter::hit($key, 3600);
        return false;
    }
    public function tooManyApiAttempts(string $userId): bool
    {
        $key = "api:{$userId}";
        if (RateLimiter::tooManyAttempts($key, 60)) {
            return true;
        }
        RateLimiter::hit($key, 60);
        return false;
    }
    public function clearAttempts(string $userId): void
    {
        RateLimiter::clear("create:{$userId}");
        RateLimiter::clear("api:{$userId}");
    }
}
