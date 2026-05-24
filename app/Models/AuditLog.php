<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class AuditLog extends Model
{
    public $timestamps = false;
    const CREATED_AT = "created_at";
    protected $fillable = [
        "actor_id", "actor_type", "action",
        "target_type", "target_id",
        "before", "after",
        "ip_address", "user_agent",
    ];
    protected $casts = [
        "before"     => "array",
        "after"      => "array",
        "created_at" => "datetime",
    ];
    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, "actor_id");
    }
}
