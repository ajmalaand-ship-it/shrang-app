@extends('layouts.app')
@section('title', 'Discover — Shrang')
@section('content')
<div class="sh-page-wrap sh-page-wrap--wide">

    <div class="discover-header">
        <div>
            <h1 class="sh-heading">Discover</h1>
            <p class="sh-text-muted">Explore AI-generated music from our community.</p>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('discover') }}" class="discover-filters">
        <div class="discover-filters__row">
            <select name="sort" class="sh-select discover-filters__select" onchange="this.form.submit()">
                <option value="featured" {{ ($filters['sort'] ?? '') === 'featured' ? 'selected' : '' }}>Featured</option>
                <option value="latest" {{ ($filters['sort'] ?? '') === 'latest' ? 'selected' : '' }}>Latest</option>
                <option value="liked" {{ ($filters['sort'] ?? '') === 'liked' ? 'selected' : '' }}>Most Liked</option>
                <option value="played" {{ ($filters['sort'] ?? '') === 'played' ? 'selected' : '' }}>Most Played</option>
                <option value="downloaded" {{ ($filters['sort'] ?? '') === 'downloaded' ? 'selected' : '' }}>Most Downloaded</option>
            </select>
            <select name="type" class="sh-select discover-filters__select" onchange="this.form.submit()">
                <option value="" {{ empty($filters['type']) ? 'selected' : '' }}>All Types</option>
                <option value="song" {{ ($filters['type'] ?? '') === 'song' ? 'selected' : '' }}>Songs</option>
                <option value="bed" {{ ($filters['type'] ?? '') === 'bed' ? 'selected' : '' }}>Bed Music</option>
            </select>
            <select name="language" class="sh-select discover-filters__select" onchange="this.form.submit()">
                <option value="" {{ empty($filters['language']) ? 'selected' : '' }}>All Languages</option>
                <option value="ps" {{ ($filters['language'] ?? '') === 'ps' ? 'selected' : '' }}>Pashto</option>
                <option value="fa" {{ ($filters['language'] ?? '') === 'fa' ? 'selected' : '' }}>Dari/Farsi</option>
                <option value="ur" {{ ($filters['language'] ?? '') === 'ur' ? 'selected' : '' }}>Urdu</option>
                <option value="ar" {{ ($filters['language'] ?? '') === 'ar' ? 'selected' : '' }}>Arabic</option>
                <option value="hi" {{ ($filters['language'] ?? '') === 'hi' ? 'selected' : '' }}>Hindi</option>
                <option value="en" {{ ($filters['language'] ?? '') === 'en' ? 'selected' : '' }}>English</option>
            </select>
        </div>
    </form>

    {{-- Clips grid --}}
    @if($clips->isEmpty())
        <div class="sh-card" style="text-align:center;padding:4rem 2rem;">
            <p class="sh-text-muted">No clips found. Try a different filter.</p>
        </div>
    @else
        <div class="discover-grid">
            @foreach($clips as $clip)
            @php
                $cover = $clip->mediaAssets()->where('type','cover_image')->where('is_primary',true)->first();
                $audio = $clip->mediaAssets()->whereIn('type',['song_audio','bed_audio'])->where('is_primary',true)->first();
                $reel  = $clip->mediaAssets()->where('type','reel_video')->where('is_primary',true)->first();
                $liked = in_array($clip->id, $likedIds);
                $type  = $audio?->type === 'bed_audio' ? 'Bed Music' : 'Song';
            @endphp
            <div class="discover-card" data-slug="{{ $clip->slug }}">

                {{-- Cover --}}
                <div class="discover-card__cover">
                    @if($cover)
                        <img src="{{ $cover->cdn_url }}" alt="{{ $clip->title }}" class="discover-card__cover-img">
                    @else
                        <div class="discover-card__cover-placeholder">
                            <span class="discover-card__cover-icon">♪</span>
                        </div>
                    @endif
                    <div class="discover-card__cover-overlay">
                        <div class="discover-card__badges">
                            <span class="sh-badge sh-badge--lang">{{ strtoupper($clip->language) }}</span>
                            <span class="sh-badge">{{ $type }}</span>
                            @if($clip->is_pinned) <span class="sh-badge" style="color:var(--sh-gold);">📌 Pinned</span> @endif
                        </div>
                        @if($audio)
                        <button class="discover-card__play-btn" onclick="togglePlay(this, '{{ $audio->cdn_url }}', '{{ $clip->slug }}')" aria-label="Play">
                            <span class="discover-card__play-icon">▶</span>
                        </button>
                        @endif
                    </div>
                </div>

                {{-- Info --}}
                <div class="discover-card__body">
                    <div class="discover-card__title">{{ $clip->title }}</div>
                    @if($clip->lyrics_public && $clip->lyrics_input)
                        <div class="discover-card__excerpt {{ $clip->script_direction === 'rtl' ? 'sh-script-rtl' : '' }}" dir="{{ $clip->script_direction }}">
                            {{ Str::limit($clip->lyrics_input, 80) }}
                        </div>
                    @endif

                    {{-- Audio player (hidden, shown on play) --}}
                    <audio class="discover-card__audio" preload="none" style="display:none;">
                        @if($audio)<source src="{{ $audio->cdn_url }}" type="audio/mpeg">@endif
                    </audio>

                    {{-- Stats --}}
                    <div class="discover-card__stats">
                        <span title="Plays">▶ {{ number_format($clip->play_count ?? 0) }}</span>
                        <span title="Likes">♥ {{ number_format($clip->like_count ?? 0) }}</span>
                        <span title="Downloads">↓ {{ number_format($clip->download_count ?? 0) }}</span>
                    </div>

                    {{-- Actions --}}
                    <div class="discover-card__actions">
                        <button class="sh-btn sh-btn--sm {{ $liked ? 'sh-btn--primary' : 'sh-btn--ghost' }} discover-like-btn"
                                data-slug="{{ $clip->slug }}"
                                data-liked="{{ $liked ? '1' : '0' }}">
                            ♥ <span class="like-count">{{ number_format($clip->like_count ?? 0) }}</span>
                        </button>
                        @if($clip->allow_download && $audio)
                        <button class="sh-btn sh-btn--sm sh-btn--ghost discover-download-btn"
                                data-slug="{{ $clip->slug }}">
                            ↓ MP3
                        </button>
                        @endif
                        @if($reel)
                        <a href="{{ $reel->cdn_url }}" download class="sh-btn sh-btn--sm sh-btn--ghost">↓ Reel</a>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div style="margin-top:2rem;">{{ $clips->links() }}</div>
    @endif
</div>

<audio id="global-player" style="display:none;"></audio>
@endsection

@section('page_js')
<script>
const csrfToken = document.querySelector('meta[name=csrf-token]').content;
let currentSlug = null;
let playTimer = null;

function togglePlay(btn, audioUrl, slug) {
    const globalPlayer = document.getElementById('global-player');
    const icon = btn.querySelector('.discover-card__play-icon');
    if (currentSlug === slug && !globalPlayer.paused) {
        globalPlayer.pause();
        icon.textContent = '▶';
        currentSlug = null;
        clearTimeout(playTimer);
        return;
    }
    document.querySelectorAll('.discover-card__play-icon').forEach(i => i.textContent = '▶');
    clearTimeout(playTimer);
    globalPlayer.src = audioUrl;
    globalPlayer.play();
    icon.textContent = '⏸';
    currentSlug = slug;
    playTimer = setTimeout(() => {
        fetch('/discover/' + slug + '/play', {method:'POST', headers:{'X-CSRF-TOKEN':csrfToken,'Accept':'application/json'}});
    }, 5000);
}

document.querySelectorAll('.discover-like-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const slug = this.dataset.slug;
        const liked = this.dataset.liked === '1';
        const url = liked ? '/discover/' + slug + '/unlike' : '/discover/' + slug + '/like';
        fetch(url, {method:'POST', headers:{'X-CSRF-TOKEN':csrfToken,'Accept':'application/json'}})
        .then(r => r.json())
        .then(data => {
            if (data.ok !== false) {
                this.dataset.liked = liked ? '0' : '1';
                this.classList.toggle('sh-btn--primary');
                this.classList.toggle('sh-btn--ghost');
                this.querySelector('.like-count').textContent = data.count.toLocaleString();
            }
        });
    });
});

document.querySelectorAll('.discover-download-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const slug = this.dataset.slug;
        fetch('/discover/' + slug + '/download', {method:'POST', headers:{'X-CSRF-TOKEN':csrfToken,'Accept':'application/json'}})
        .then(r => r.json())
        .then(data => {
            if (data.url) {
                const a = document.createElement('a');
                a.href = data.url;
                a.download = '';
                a.click();
            }
        });
    });
});
</script>
@endsection
