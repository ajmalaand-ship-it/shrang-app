<?php
namespace App\Services;
use App\Models\Clip;
use App\Models\ClipLike;
use App\Models\ClipStat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
class ClipStatsService
{
    public function incrementPlay(Clip $clip): void
    {
        ClipStat::firstOrCreate(
            ['clip_id' => $clip->id],
            ['play_count' => 0, 'download_count' => 0, 'like_count' => 0]
        );
        DB::table('clip_stats')->where('clip_id', $clip->id)->increment('play_count');
    }
    public function incrementDownload(Clip $clip): void
    {
        ClipStat::firstOrCreate(
            ['clip_id' => $clip->id],
            ['play_count' => 0, 'download_count' => 0, 'like_count' => 0]
        );
        DB::table('clip_stats')->where('clip_id', $clip->id)->increment('download_count');
    }
    public function like(Clip $clip, ?string $userId, string $guestToken, string $ip): bool
    {
        try {
            DB::transaction(function () use ($clip, $userId, $guestToken, $ip) {
                ClipLike::create([
                    'clip_id'     => $clip->id,
                    'user_id'     => $userId,
                    'guest_token' => $userId ? null : $guestToken,
                    'ip_address'  => $ip,
                ]);
                ClipStat::firstOrCreate(
                    ['clip_id' => $clip->id],
                    ['play_count' => 0, 'download_count' => 0, 'like_count' => 0]
                );
                DB::table('clip_stats')->where('clip_id', $clip->id)->increment('like_count');
            });
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
    public function unlike(Clip $clip, ?string $userId, string $guestToken): bool
    {
        $query = ClipLike::where('clip_id', $clip->id);
        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->where('guest_token', $guestToken);
        }
        $deleted = $query->delete();
        if ($deleted) {
            DB::table('clip_stats')->where('clip_id', $clip->id)
                ->where('like_count', '>', 0)
                ->decrement('like_count');
        }
        return $deleted > 0;
    }
    public function hasLiked(Clip $clip, ?string $userId, string $guestToken): bool
    {
        $query = ClipLike::where('clip_id', $clip->id);
        if ($userId) {
            return $query->where('user_id', $userId)->exists();
        }
        return $query->where('guest_token', $guestToken)->exists();
    }
    public function getGuestToken($request): string
    {
        $token = $request->cookie('shrang_guest');
        if (!$token) {
            $token = Str::uuid()->toString();
        }
        return $token;
    }
}
