<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
class CreditPackage extends Model
{
    use HasUuids;
    protected $fillable = [
        "name", "credits", "price_cents",
        "currency", "is_active", "sort_order",
    ];
    protected $casts = [
        "is_active" => "boolean",
    ];
}
