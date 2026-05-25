<?php
namespace App\Policies;
use App\Models\Clip;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
class ClipPolicy
{
    use HandlesAuthorization;
    public function view(User $user, Clip $clip): bool
    {
        return $user->id === $clip->user_id;
    }
    public function update(User $user, Clip $clip): bool
    {
        return $user->id === $clip->user_id;
    }
    public function generateCover(User $user, Clip $clip): bool
    {
        return $user->id === $clip->user_id && $clip->status === 'ready';
    }
    public function generateReel(User $user, Clip $clip): bool
    {
        return $user->id === $clip->user_id && $clip->status === 'ready';
    }
}
