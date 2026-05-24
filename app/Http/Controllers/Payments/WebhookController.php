<?php
namespace App\Http\Controllers\Payments;
use App\Http\Controllers\Controller;
use App\Jobs\ProcessWebhookJob;
use App\Models\WebhookEvent;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;
class WebhookController extends Controller
{
    public function handle(Request $request): Response
    {
        $payload   = $request->getContent();
        $sigHeader = $request->header("Stripe-Signature", "");
        $secret    = config("services.stripe.webhook_secret");
        $verified  = false;
        $eventData = [];
        try {
            $event     = Webhook::constructEvent($payload, $sigHeader, $secret);
            $verified  = true;
            $eventData = json_decode($payload, true);
        } catch (SignatureVerificationException $e) {
            Log::warning("Stripe webhook signature failed", ["error" => $e->getMessage()]);
            $eventData = json_decode($payload, true) ?? [];
        } catch (\Exception $e) {
            Log::error("Stripe webhook parse error", ["error" => $e->getMessage()]);
            return response("Webhook error", 400);
        }
        $idempotencyKey = $eventData["id"] ?? uniqid("stripe_", true);
        $existing = WebhookEvent::where("idempotency_key", $idempotencyKey)->first();
        if ($existing) {
            return response("Already received", 200);
        }
        $webhookEvent = WebhookEvent::create([
            "source"             => "stripe",
            "event_type"         => $eventData["type"] ?? "unknown",
            "idempotency_key"    => $idempotencyKey,
            "payload"            => $eventData,
            "signature_verified" => $verified,
            "status"             => "received",
        ]);
        ProcessWebhookJob::dispatch($webhookEvent->id)
            ->onQueue("notifications");
        return response("Received", 200);
    }
}
