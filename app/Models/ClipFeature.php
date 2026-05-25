<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
class ClipFeature extends Model
{
    use HasUuids;
    public $timestamps = false;
    protected $fillable = ['clip_id', 'featured_by', 'is_pinned', 'is_blocked', 'sort_order', 'featured_at'];
    protected $casts = ['is_pinned' => 'boolean', 'is_blocked' => 'boolean', 'featured_at' => 'datetime'];
    public function clip() { return $this->belongsTo(Clip::class); }
    public function featuredBy() { return $this->belongsTo(User::class, 'featured_by'); }
}
