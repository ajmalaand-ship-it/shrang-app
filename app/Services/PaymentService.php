<?php
namespace App\Services;
use App\Models\CreditPackage;
use App\Models\PaymentOrder;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Checkout\Session;
class PaymentService
{
    public function __construct()
    {
        Stripe::setApiKey(config("services.stripe.secret"));
    }
    public function createCheckoutSession(User $user, CreditPackage $package): string
    {
        $session = Session::create([
            "payment_method_types" => ["card"],
            "line_items" => [[
                "price_data" => [
                    "currency"     => strtolower($package->currency ?? "usd"),
                    "unit_amount"  => $package->price_cents,
                    "product_data" => [
                        "name"        => $package->name . " — " . number_format($package->credits) . " Credits",
                        "description" => "Shrang AI Music Credits",
                    ],
                ],
                "quantity" => 1,
            ]],
            "mode"        => "payment",
            "success_url" => route("credits") . "?success=1",
            "cancel_url"  => route("credits") . "?cancelled=1",
            "metadata"    => [
                "user_id"           => $user->id,
                "credit_package_id" => $package->id,
            ],
        ]);
        PaymentOrder::create([
            "user_id"                  => $user->id,
            "credit_package_id"        => $package->id,
            "stripe_payment_intent_id" => $session->payment_intent ?? $session->id,
            "amount_cents"             => $package->price_cents,
            "currency"                 => $package->currency ?? "USD",
            "status"                   => "pending",
        ]);
        return $session->url;
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
