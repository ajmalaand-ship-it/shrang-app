@extends('layouts.public')
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
                <option value="bed" {{ ($filters['type'] ?? '') === 'bed' ? 'selected' : '' }}>Background Music</option>
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
            <div class="discover-card {{ !$cover ? 'discover-card--no-cover' : '' }} {{ $reel ? 'discover-card--has-reel' : '' }}"
                 data-slug="{{ $clip->slug }}"
                 data-url="{{ route('player.show', $clip->slug) }}"
                 role="link"
                 tabindex="0">

                {{-- Cover --}}
                <div class="discover-card__cover">
                    @if($reel)
                        <video class="discover-card__cover-img discover-card__cover-reel" src="{{ $reel->cdn_url }}" muted loop playsinline preload="metadata" @if($cover) poster="{{ $cover->cdn_url }}" @endif></video>
                    @elseif($cover)
                        <img src="{{ $cover->cdn_url }}" alt="{{ $clip->display_title }}" class="discover-card__cover-img">
                    @else
                        <div class="discover-card__cover-placeholder">
                            <span class="discover-card__cover-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="40" height="40"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg></span>
                        </div>
                    @endif
                    <div class="discover-card__cover-overlay"></div>
                </div>

                {{-- Info --}}
                <div class="discover-card__body">
                    @php $langNames = ["ps"=>"Pashto","fa"=>"Dari","ur"=>"Urdu","ar"=>"Arabic","hi"=>"Hindi","en"=>"English"]; @endphp
                    <div class="discover-card__title" dir="{{ $clip->script_direction ?? 'auto' }}">{{ $clip->display_title }}</div>
                    <div class="discover-card__meta-tags">
                        <span>{{ $langNames[$clip->language] ?? strtoupper($clip->language) }}</span>
                        <span>{{ $type }}</span>
                        @if($clip->is_pinned) <span>Featured</span> @endif
                    </div>
                    @if($clip->lyrics_public && $clip->lyrics_input)
                        <div class="discover-card__excerpt {{ $clip->script_direction === 'rtl' ? 'sh-script-rtl' : '' }}" dir="{{ $clip->script_direction }}">
                            {{ Str::limit($clip->lyrics_input, 80) }}
                        </div>
                    @endif

                    {{-- Actions --}}
                    <div class="discover-card__actions">
                        <button class="sh-btn sh-btn--sm {{ $liked ? 'sh-btn--primary' : 'sh-btn--ghost' }} discover-like-btn"
                                data-slug="{{ $clip->slug }}"
                                data-liked="{{ $liked ? '1' : '0' }}">
                            ♥ <span class="like-count">{{ number_format($clip->like_count ?? 0) }}</span>
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div style="margin-top:2rem;">{{ $clips->links() }}</div>
    @endif
</div>

@endsection

@section('page_js')
<script>
const csrfToken = document.querySelector('meta[name=csrf-token]').content;

document.querySelectorAll('.discover-card').forEach(card => {
    card.addEventListener('click', function(e) {
        if (e.target.closest('button, a')) return;
        window.location.href = this.dataset.url;
    });

    card.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' || e.key === ' ') {
            if (e.target.closest('button, a')) return;
            e.preventDefault();
            window.location.href = this.dataset.url;
        }
    });
});

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

// ── Reel preview on Discover cards (desktop hover only) ────────
(function() {
    var reels = document.querySelectorAll('.discover-card__cover-reel');
    if (!reels.length) return;

    reels.forEach(function(video) {
        var card = video.closest('.discover-card');
        if (!card) return;

        card.addEventListener('mouseenter', function() {
            var p = video.play();
            if (p && p.catch) p.catch(function(){});
        });
        card.addEventListener('mouseleave', function() {
            video.pause();
            video.currentTime = 0;
        });
    });
})();
</script>
@endsection
