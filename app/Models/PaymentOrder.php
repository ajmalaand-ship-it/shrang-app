<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class PaymentOrder extends Model
{
    use HasUuids;
    protected $fillable = [
        "user_id", "credit_package_id",
        "stripe_payment_intent_id", "amount_cents",
        "currency", "status", "stripe_response", "paid_at",
    ];
    protected $casts = [
        "stripe_response" => "array",
        "paid_at"         => "datetime",
    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function creditPackage(): BelongsTo
    {
        return $this->belongsTo(CreditPackage::class);
    }
}
