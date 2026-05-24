<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class CreditTransaction extends Model
{
    public $timestamps = false;
    const CREATED_AT = "created_at";
    protected $fillable = [
        "user_id", "payment_order_id", "type",
        "amount", "reason", "reference_id",
    ];
    protected $casts = [
        "created_at" => "datetime",
    ];
}
