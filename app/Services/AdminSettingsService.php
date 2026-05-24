<?php
namespace App\Services;
use App\Models\AdminSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
class AdminSettingsService
{
    private const CACHE_TTL = 300;
    public function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember("admin_setting:{$key}", self::CACHE_TTL, function () use ($key, $default) {
            $setting = AdminSetting::where("key", $key)->first();
            if (!$setting) return $default;
            return match ($setting->cast) {
                "int"  => (int) $setting->value,
                "bool" => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
                "json" => json_decode($setting->value, true),
                default => $setting->value,
            };
        });
    }
    public function set(string $key, mixed $value, string $updatedBy = null): void
    {
        AdminSetting::updateOrCreate(
            ["key" => $key],
            ["value" => is_array($value) ? json_encode($value) : (string) $value,
             "updated_by" => $updatedBy]
        );
        Cache::forget("admin_setting:{$key}");
    }
    public function creditCost(string $type): int
    {
        return (int) $this->get("credit_cost_{$type}", config("credits.costs.{$type}", 10));
    }
    public function freeTierDailyLimit(string $type): int
    {
        return (int) $this->get("free_tier_daily_{$type}", config("credits.free_tier.daily_{$type}_limit", 2));
    }
    public function flush(string $key): void
    {
        Cache::forget("admin_setting:{$key}");
    }
}
