<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
class ClipLike extends Model
{
    use HasUuids;
    public $timestamps = false;
    protected $fillable = ['clip_id', 'user_id', 'guest_token', 'ip_address'];
    public function clip() { return $this->belongsTo(Clip::class); }
    public function user() { return $this->belongsTo(User::class); }
}
