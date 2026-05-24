<?php
namespace App\Policies;
use App\Models\Clip;
use App\Models\User;
class ClipPolicy
{
    public function view(User $user, Clip $clip): bool
    {
        if ($clip->visibility === "public") {
            return true;
        }
        return $user->id === $clip->user_id;
    }

    public function update(User $user, Clip $clip): bool
    {
        return $user->id === $clip->user_id;
    }

    public function delete(User $user, Clip $clip): bool
    {
        return $user->id === $clip->user_id;
    }

    public function generateCover(User $user, Clip $clip): bool
    {
        return $user->id === $clip->user_id;
    }

    public function generateReel(User $user, Clip $clip): bool
    {
        return $user->id === $clip->user_id;
    }
}
