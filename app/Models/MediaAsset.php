<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class MediaAsset extends Model
{
    use HasUuids;
    protected $fillable = [
        "clip_id", "user_id", "generation_job_id",
        "type", "storage_disk", "storage_key",
        "cdn_url", "mime_type", "file_size_bytes",
        "duration_seconds", "width", "height",
        "is_primary", "is_temp", "metadata",
    ];
    protected $casts = [
        "is_primary" => "boolean",
        "is_temp"    => "boolean",
        "metadata"   => "array",
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
