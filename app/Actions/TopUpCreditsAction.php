<?php
namespace App\Actions;
use App\Models\CreditTransaction;
use App\Models\PaymentOrder;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class TopUpCreditsAction
{
    public function execute(string $paymentIntentId): void
    {
        DB::transaction(function () use ($paymentIntentId) {
            $order = PaymentOrder::where("stripe_payment_intent_id", $paymentIntentId)
                ->lockForUpdate()
                ->first();
            if (!$order) {
                Log::warning("TopUpCreditsAction: order not found", ["intent" => $paymentIntentId]);
                return;
            }
            if ($order->status === "paid") {
                Log::info("TopUpCreditsAction: already processed", ["order_id" => $order->id]);
                return;
            }
            $package = $order->creditPackage;
            if (!$package) {
                Log::error("TopUpCreditsAction: package not found", ["order_id" => $order->id]);
                return;
            }
            $user = User::where("id", $order->user_id)->lockForUpdate()->first();
            if (!$user) return;
            $user->increment("credit_balance", $package->credits);
            CreditTransaction::create([
                "user_id"          => $user->id,
                "payment_order_id" => $order->id,
                "type"             => "credit",
                "amount"           => $package->credits,
                "reason"           => "stripe_payment",
                "reference_id"     => $order->id,
            ]);
            $order->update([
                "status"  => "paid",
                "paid_at" => now(),
            ]);
            Log::info("TopUpCreditsAction: credits added", [
                "user_id" => $user->id,
                "credits" => $package->credits,
            ]);
        });
    }
}
