<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class WebhookEvent extends Model
{
    public $timestamps = false;
    const CREATED_AT = "created_at";
    protected $fillable = [
        "source", "event_type", "idempotency_key",
        "payload", "signature_verified", "status",
        "error_message", "processed_at",
    ];
    protected $casts = [
        "payload"            => "array",
        "signature_verified" => "boolean",
        "processed_at"       => "datetime",
        "created_at"         => "datetime",
    ];
}
