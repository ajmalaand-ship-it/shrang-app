<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class AiUsageLog extends Model
{
    public $timestamps = false;
    const CREATED_AT = "created_at";
    protected $fillable = [
        "user_id", "generation_job_id", "provider",
        "capability", "prompt_tokens", "output_tokens",
        "duration_seconds", "provider_cost_usd",
        "latency_ms", "status", "error_code",
    ];
    protected $casts = [
        "provider_cost_usd" => "decimal:6",
        "created_at"        => "datetime",
    ];
}
