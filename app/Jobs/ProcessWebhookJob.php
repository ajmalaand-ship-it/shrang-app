<?php
namespace App\Jobs;
use App\Actions\TopUpCreditsAction;
use App\Models\WebhookEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
class ProcessWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public int $tries   = 5;
    public int $timeout = 60;
    public function __construct(private readonly string $webhookEventId) {}
    public function handle(TopUpCreditsAction $topUp): void
    {
        $event = WebhookEvent::findOrFail($this->webhookEventId);
        if ($event->status === "processed") {
            return;
        }
        $event->update(["status" => "processing"]);
        try {
            $payload = $event->payload;
            $type    = $payload["type"] ?? "";
            if ($type === "payment_intent.succeeded") {
                $intentId = $payload["data"]["object"]["id"] ?? null;
                if ($intentId) {
                    $topUp->execute($intentId);
                }
            }
            $event->update([
                "status"       => "processed",
                "processed_at" => now(),
            ]);
        } catch (\Exception $e) {
            $event->update([
                "status"        => "failed",
                "error_message" => $e->getMessage(),
            ]);
            Log::error("ProcessWebhookJob failed", [
                "webhook_event_id" => $this->webhookEventId,
                "error"            => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
