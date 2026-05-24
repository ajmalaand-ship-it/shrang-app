<?php
namespace App\Jobs;
use App\Models\MediaAsset;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
class PurgeOrphanedMediaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public int $timeout = 300;
    public function handle(): void
    {
        $assets = MediaAsset::where("is_temp", true)
            ->where("created_at", "<", now()->subHours(24))
            ->get();
        $count = 0;
        foreach ($assets as $asset) {
            try {
                if ($asset->storage_key) {
                    Storage::disk($asset->storage_disk ?? "public")
                        ->delete($asset->storage_key);
                }
                $asset->delete();
                $count++;
            } catch (\Exception $e) {
                Log::warning("PurgeOrphanedMediaJob: could not delete asset", [
                    "asset_id" => $asset->id,
                    "error"    => $e->getMessage(),
                ]);
            }
        }
        Log::info("PurgeOrphanedMediaJob: purged {$count} orphaned assets");
    }
}
