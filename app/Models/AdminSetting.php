<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class AdminSetting extends Model
{
    public $timestamps = false;
    const UPDATED_AT = "updated_at";
    protected $fillable = [
        "key", "value", "cast", "group",
        "label", "description", "is_public", "updated_by",
    ];
    protected $casts = [
        "is_public"  => "boolean",
        "updated_at" => "datetime",
    ];
}
