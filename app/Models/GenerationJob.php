<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class GenerationJob extends Model
{
    use HasUuids;
    protected $fillable = [
        "user_id", "clip_id", "job_class", "ai_provider",
        "status", "progress_pct", "progress_message",
        "credits_reserved", "credits_charged",
        "provider_job_id", "provider_response",
        "error_message", "attempts",
        "started_at", "completed_at",
    ];
    protected $casts = [
        "provider_response" => "array",
        "started_at"        => "datetime",
        "completed_at"      => "datetime",
    ];
    public function clip(): BelongsTo
    {
        return $this->belongsTo(Clip::class);
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
