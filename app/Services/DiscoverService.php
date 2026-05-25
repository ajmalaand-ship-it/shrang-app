<?php
namespace App\Services;
use App\Models\Clip;
use App\Models\ClipFeature;
use Illuminate\Pagination\LengthAwarePaginator;
class DiscoverService
{
    public function getFeatured(array $filters = []): LengthAwarePaginator
    {
        $query = Clip::query()
            ->join('clip_features', 'clips.id', '=', 'clip_features.clip_id')
            ->leftJoin('clip_stats', 'clips.id', '=', 'clip_stats.clip_id')
            ->where('clips.visibility', 'public')
            ->where('clips.status', 'ready')
            ->where('clip_features.is_blocked', false)
            ->select(
                'clips.*',
                'clip_features.is_pinned',
                'clip_features.sort_order',
                'clip_features.featured_at',
                'clip_stats.play_count',
                'clip_stats.download_count',
                'clip_stats.like_count'
            );
        if (!empty($filters['language'])) {
            $query->where('clips.language', $filters['language']);
        }
        if (!empty($filters['type'])) {
            if ($filters['type'] === 'song') {
                $query->whereHas('mediaAssets', fn($q) => $q->where('type', 'song_audio')->where('is_primary', true));
            } elseif ($filters['type'] === 'bed') {
                $query->whereHas('mediaAssets', fn($q) => $q->where('type', 'bed_audio')->where('is_primary', true));
            }
        }
        $sort = $filters['sort'] ?? 'featured';
        match($sort) {
            'liked'      => $query->orderByDesc('clip_stats.like_count'),
            'played'     => $query->orderByDesc('clip_stats.play_count'),
            'downloaded' => $query->orderByDesc('clip_stats.download_count'),
            'latest'     => $query->orderByDesc('clip_features.featured_at'),
            default      => $query->orderByDesc('clip_features.is_pinned')->orderBy('clip_features.sort_order')->orderByDesc('clip_features.featured_at'),
        };
        return $query->paginate(24);
    }
    public function getPublicClipsForAdmin(string $search = '')
    {
        return Clip::where('visibility', 'public')
            ->where('status', 'ready')
            ->whereDoesntHave('feature')
            ->when($search, fn($q) => $q->where('title', 'like', '%' . $search . '%'))
            ->with(['mediaAssets' => fn($q) => $q->where('type', 'cover_image')->where('is_primary', true)])
            ->latest()
            ->paginate(20);
    }
    public function getFeaturedForAdmin()
    {
        return ClipFeature::with(['clip.mediaAssets' => fn($q) => $q->where('type', 'cover_image')->where('is_primary', true)])
            ->orderByDesc('is_pinned')
            ->orderBy('sort_order')
            ->orderByDesc('featured_at')
            ->paginate(30);
    }
}
