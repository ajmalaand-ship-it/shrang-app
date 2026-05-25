<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasUuids;

    protected $fillable = [
        "name",
        "email",
        "password",
        "preferred_language",
        "locale",
        "credit_balance",
        "role",
        "is_active",
    ];

    protected $hidden = [
        "password",
        "remember_token",
    ];

    protected function casts(): array
    {
        return [
            "email_verified_at" => "datetime",
            "is_active"         => "boolean",
            "credit_balance"    => "integer",
            "password"          => "hashed",
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === "admin";
    }

    public function creditTransactions()
    {
        return $this->hasMany(\App\Models\CreditTransaction::class);
    }
    public function clips()
    {
        return $this->hasMany(\App\Models\Clip::class);
    }
}
