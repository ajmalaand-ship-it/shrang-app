<?php
namespace App\Services;
use App\Models\CreditPackage;
use App\Models\PaymentOrder;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\PaymentIntent;
class PaymentService
{
    public function __construct()
    {
        Stripe::setApiKey(config("services.stripe.secret"));
    }
    public function createIntent(User $user, CreditPackage $package): array
    {
        try {
            $intent = PaymentIntent::create([
                "amount"   => $package->price_cents,
                "currency" => strtolower($package->currency ?? "usd"),
                "metadata" => [
                    "user_id"           => $user->id,
                    "credit_package_id" => $package->id,
                ],
            ]);
            $order = PaymentOrder::create([
                "user_id"                  => $user->id,
                "credit_package_id"        => $package->id,
                "stripe_payment_intent_id" => $intent->id,
                "amount_cents"             => $package->price_cents,
                "currency"                 => $package->currency ?? "USD",
                "status"                   => "pending",
            ]);
            return [
                "client_secret" => $intent->client_secret,
                "order_id"      => $order->id,
                "amount_cents"  => $package->price_cents,
                "currency"      => $package->currency ?? "USD",
            ];
        } catch (\Exception $e) {
            Log::error("PaymentService createIntent failed", ["error" => $e->getMessage()]);
            throw $e;
        }
    }
}
