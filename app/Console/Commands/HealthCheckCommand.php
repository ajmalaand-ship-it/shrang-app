<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
class HealthCheckCommand extends Command
{
    protected $signature   = "shrang:health";
    protected $description = "Check database, storage, cache, and queue connectivity";
    public function handle(): void
    {
        $this->info("Shrang Health Check");
        $this->line("-------------------");
        // Database
        try {
            DB::connection()->getPdo();
            $this->info("Database:  OK");
        } catch (\Exception $e) {
            $this->error("Database:  FAILED — " . $e->getMessage());
        }
        // Cache
        try {
            Cache::put("health_check", "ok", 10);
            $val = Cache::get("health_check");
            if ($val === "ok") {
                $this->info("Cache:     OK (" . config("cache.default") . ")");
            } else {
                $this->error("Cache:     FAILED — unexpected value");
            }
        } catch (\Exception $e) {
            $this->error("Cache:     FAILED — " . $e->getMessage());
        }
        // Storage
        try {
            Storage::disk("public")->put("health_check.txt", "ok");
            Storage::disk("public")->delete("health_check.txt");
            $this->info("Storage:   OK");
        } catch (\Exception $e) {
            $this->error("Storage:   FAILED — " . $e->getMessage());
        }
        // Queue
        try {
            $size = DB::table("jobs")->count();
            $this->info("Queue:     OK ({$size} jobs pending) (" . config("queue.default") . ")");
        } catch (\Exception $e) {
            $this->warn("Queue:     could not check — " . $e->getMessage());
        }
        // Admin settings
        try {
            $count = DB::table("admin_settings")->count();
            $this->info("Settings:  OK ({$count} settings in database)");
        } catch (\Exception $e) {
            $this->error("Settings:  FAILED — " . $e->getMessage());
        }
        $this->line("-------------------");
        $this->info("Health check complete.");
    }
}
