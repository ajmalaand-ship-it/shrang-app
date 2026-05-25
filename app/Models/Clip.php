<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Clip extends Model
{
    use HasUuids;
    protected $fillable = [
        "user_id", "title", "lyrics_input", "language",
        "script_direction", "status", "visibility",
        "ai_metadata", "cover_image_key",
        "slug",
        "allow_download",
        "lyrics_public",
    ];
    protected $casts = [
        "ai_metadata" => "array",
    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function generationJobs(): HasMany
    {
        return $this->hasMany(GenerationJob::class);
    }
    public function mediaAssets(): HasMany
    {
        return $this->hasMany(MediaAsset::class);
    }
    public function feature() { return $this->hasOne(ClipFeature::class); }
    public function likes() { return $this->hasMany(ClipLike::class); }
    public function stat() { return $this->hasOne(ClipStat::class); }
    public function reels(): HasMany
    {
        return $this->hasMany(MediaAsset::class)->where("type", "reel_video");
    }
}
