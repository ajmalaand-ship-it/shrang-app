<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
class ClipStat extends Model
{
    use HasUuids;
    public $timestamps = false;
    protected $fillable = ['clip_id', 'play_count', 'download_count', 'like_count'];
    public function clip() { return $this->belongsTo(Clip::class); }
}
