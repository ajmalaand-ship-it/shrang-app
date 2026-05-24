<?php
namespace App\Jobs;
use App\Models\Clip;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
class GenerateReelJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public int $timeout = 300;
    public int $tries   = 3;
    public function __construct(
        private readonly string $clipId,
        private readonly array  $params
    ) {}
    public function handle(): void
    {
        Log::info("GenerateReelJob: stub — reel generation coming in Phase 8 full build", [
            "clip_id" => $this->clipId,
        ]);
    }
}
