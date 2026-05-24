<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class CreditReservation extends Model
{
    protected $fillable = [
        "user_id", "generation_job_id", "amount",
        "status", "expires_at", "committed_at", "released_at",
    ];
    protected $casts = [
        "expires_at"    => "datetime",
        "committed_at"  => "datetime",
        "released_at"   => "datetime",
    ];
}
