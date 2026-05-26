@extends('layouts.public')
@section('title', $clip->title . ' — Shrang')
@section('head_extra')
<x-og-meta
    :title="$clip->title . ' — Shrang'"
    :description="Str::limit($clip->lyrics_input, 160)"
    :imageUrl="$coverUrl ?? ''"
    :pageUrl="$shareUrl"
/>
@endsection
@section('content')
<div class="sh-page-wrap player-wrap">

    {{-- Cover --}}
    @if($coverUrl)
    <div class="player-cover">
        <img src="{{ $coverUrl }}" alt="{{ $clip->title }}" class="player-cover__img">
        <div class="player-cover__overlay">
            <h1 class="player-cover__title">{{ $clip->title }}</h1>
            <div class="player-cover__badges">
                <span class="sh-badge sh-badge--lang">{{ strtoupper($clip->language) }}</span>
                @if($clip->script_direction === 'rtl')<span class="sh-badge">RTL</span>@endif
            </div>
        </div>
    </div>
    @else
    <div class="player-cover player-cover--no-image">
        <h1 class="player-cover__title">{{ $clip->title }}</h1>
        <div class="player-cover__badges">
            <span class="sh-badge sh-badge--lang">{{ strtoupper($clip->language) }}</span>
        </div>
    </div>
    @endif

    {{-- Audio player --}}
    <div class="sh-card player-card">
        <div class="sh-card__body">
            @if($audioUrl)
                <audio controls class="player-audio" preload="metadata">
                    <source src="{{ $audioUrl }}" type="audio/mpeg">
                </audio>
            @else
                <div class="sh-notice sh-notice--info">Audio not available.</div>
            @endif

            {{-- Actions --}}
            <div class="player-actions">
                {{-- Share --}}
                <button type="button" class="sh-btn sh-btn--ghost" id="share-btn"
                        data-url="{{ $shareUrl }}">
                    Share
                </button>
                <span class="player-actions__copied" id="share-copied">Link copied!</span>

                {{-- Download --}}
                @if($downloadUrl)
                    <a href="{{ $downloadUrl }}" download class="sh-btn sh-btn--primary">
                        ↓ Download
                    </a>
                @endif
            </div>

            {{-- Share URL display --}}
            <div class="player-share-box">
                <label class="sh-label">Share link</label>
                <div class="player-share-box__row">
                    <input type="text" class="sh-input" value="{{ $shareUrl }}" readonly id="share-url-input">
                    <button type="button" class="sh-btn sh-btn--ghost sh-btn--sm" onclick="copyShareUrl()">Copy</button>
                </div>
            </div>

            {{-- Embed --}}
            <div class="player-share-box">
                <label class="sh-label">Embed</label>
                <div class="player-share-box__row">
                    <input type="text" class="sh-input" value="{{ $embedCode }}" readonly id="embed-input">
                    <button type="button" class="sh-btn sh-btn--ghost sh-btn--sm" onclick="copyEmbed()">Copy</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Lyrics --}}
    @if($clip->lyrics_input && $audioUrl)
    <div class="sh-card">
        <div class="sh-card__header">Lyrics</div>
        <div class="sh-card__body">
            <div class="studio-page__lyrics {{ $clip->script_direction === 'rtl' ? 'sh-script-rtl' : '' }}"
                 dir="{{ $clip->script_direction }}">
                {!! nl2br(e($clip->lyrics_input)) !!}
            </div>
        </div>
    </div>
    @endif

</div>
@endsection
@section('page_js')
<script>
function copyShareUrl() {
    var input = document.getElementById('share-url-input');
    input.select();
    navigator.clipboard.writeText(input.value).then(function() {
        showCopied();
    });
}
function copyEmbed() {
    var input = document.getElementById('embed-input');
    input.select();
    navigator.clipboard.writeText(input.value).then(function() {
        showCopied();
    });
}
document.getElementById('share-btn').addEventListener('click', function() {
    copyShareUrl();
});
function showCopied() {
    var el = document.getElementById('share-copied');
    el.style.opacity = '1';
    setTimeout(function() { el.style.opacity = '0'; }, 2000);
}
</script>
@endsection
