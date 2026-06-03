@extends('layouts.public')
@section('title', $clip->display_title . ' — Shrang')
@section('head_extra')
<x-og-meta
    :title="$clip->display_title . ' — Shrang'"
    :description="Str::limit($clip->lyrics_input, 160)"
    :imageUrl="$coverUrl ?? ''"
    :pageUrl="$shareUrl"
/>
@endsection
@section('content')
<div class="sh-page-wrap player-wrap">

    {{-- Cover --}}
    @php
        $langNames = ["ps"=>"Pashto","fa"=>"Dari","ur"=>"Urdu","ar"=>"Arabic","hi"=>"Hindi","en"=>"English"];
        $langLabel = $langNames[$clip->language] ?? strtoupper($clip->language);
    @endphp
    @if($coverUrl)
    <div class="player-cover">
        <img src="{{ $coverUrl }}" alt="{{ $clip->display_title }}" class="player-cover__img">
        <div class="player-cover__overlay">
            <h1 class="player-cover__title" dir="auto">{{ $clip->display_title }}</h1>
            <div class="player-cover__badges">
                <span class="sh-badge sh-badge--lang">{{ $langLabel }}</span>
            </div>
        </div>
    </div>
    @else
    <div class="player-cover player-cover--no-image">
        <h1 class="player-cover__title" dir="auto">{{ $clip->display_title }}</h1>
        <div class="player-cover__badges">
            <span class="sh-badge sh-badge--lang">{{ $langLabel }}</span>
        </div>
    </div>
    @endif

    {{-- Audio player --}}
    <div class="sh-card player-card">
        <div class="sh-card__body">
            @if($audioUrl)
                <div class="sh-audio-player" id="pubPlayer">
                    <div class="sh-audio-player__ui">
                        <button class="sh-audio-player__playbtn" id="pubPlayBtn" aria-label="Play">
                            <svg id="pubIconPlay" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M8 5v14l11-7z"/></svg>
                            <svg id="pubIconPause" viewBox="0 0 24 24" fill="currentColor" style="display:none;" aria-hidden="true"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>
                        </button>
                        <div class="sh-audio-player__progress-wrap">
                            <div class="sh-audio-player__bar" id="pubBar" role="slider" aria-label="Seek" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" tabindex="0">
                                <div class="sh-audio-player__bar-fill" id="pubFill"></div>
                                <div class="sh-audio-player__bar-thumb" id="pubThumb"></div>
                            </div>
                        </div>
                        <span class="sh-audio-player__time" id="pubTime">0:00 / 0:00</span>
                    </div>
                    <audio id="pubAudio" class="sh-audio-player__native" preload="metadata">
                        <source src="{{ $audioUrl }}" type="audio/mpeg">
                    </audio>
                </div>
            @else
                <div class="sh-notice sh-notice--info">Audio not available.</div>
            @endif

            {{-- Download row (primary actions) --}}
            <div class="player-actions player-actions--downloads">
                @if($reelUrl)
                    <a href="{{ $reelUrl }}" download class="sh-btn sh-btn--primary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" width="16" height="16"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Download Reel
                    </a>
                    @if($downloadUrl)
                    <a href="{{ $downloadUrl }}" download class="sh-btn sh-btn--ghost">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" width="16" height="16"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Download MP3
                    </a>
                    @endif
                @elseif($downloadUrl)
                    <a href="{{ $downloadUrl }}" download class="sh-btn sh-btn--primary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" width="16" height="16"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Download MP3
                    </a>
                @endif
            </div>

            {{-- Share row --}}
            <div class="player-actions player-actions--share">
                <button type="button" class="sh-btn sh-btn--ghost" id="share-btn"
                        data-url="{{ $shareUrl }}" data-title="{{ $clip->display_title }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" width="16" height="16"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
                    Share
                </button>
                <a href="https://wa.me/?text={{ urlencode($clip->display_title . ' — ' . $shareUrl) }}"
                   target="_blank" rel="noopener"
                   class="sh-btn sh-btn--ghost player-btn--whatsapp">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                    WhatsApp
                </a>
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($shareUrl) }}"
                   target="_blank" rel="noopener"
                   class="sh-btn sh-btn--ghost player-btn--facebook">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    Facebook
                </a>
                <span class="player-actions__copied" id="share-copied">Link copied!</span>
            </div>

            {{-- Share URL display --}}
            <div class="player-share-box">
                <label class="sh-label">Share link</label>
                <div class="player-share-box__row">
                    <input type="text" class="sh-input" value="{{ $shareUrl }}" readonly id="share-url-input">
                    <button type="button" class="sh-btn sh-btn--ghost sh-btn--sm" onclick="copyShareUrl()">Copy</button>
                </div>
            </div>

            {{-- Embed (collapsed by default) --}}
            <details class="player-share-box player-share-box--collapsible">
                <summary class="player-share-box__summary">Show embed code</summary>
                <div class="player-share-box__row" style="margin-top:0.5rem;">
                    <input type="text" class="sh-input" value="{{ $embedCode }}" readonly id="embed-input">
                    <button type="button" class="sh-btn sh-btn--ghost sh-btn--sm" onclick="copyEmbed()">Copy</button>
                </div>
            </details>

            {{-- Shrang brand mark --}}
            <div class="player-brand-mark">
                <span class="player-brand-mark__label">Made with</span>
                <a href="/" class="player-brand-mark__logo">
                    <span class="player-brand-mark__logo-ar">شرنګ</span>
                    <span class="player-brand-mark__logo-en">Shrang</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Lyrics --}}
    @if($clip->lyrics_input && $audioUrl)
    <div class="sh-card player-lyrics-card">
        <div class="sh-card__header player-lyrics-card__header">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" width="18" height="18"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>
            <span>{{ $clip->script_direction === 'rtl' ? 'Lyrics / Poem' : 'Lyrics' }}</span>
        </div>
        <div class="sh-card__body">
            <div class="studio-lyrics {{ $clip->script_direction === 'rtl' ? 'studio-lyrics--rtl' : 'studio-lyrics--ltr' }}" dir="{{ $clip->script_direction }}">{{ trim($clip->lyrics_input) }}</div>
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
    var url = this.getAttribute('data-url');
    var title = this.getAttribute('data-title');
    if (navigator.share) {
        navigator.share({ title: title, url: url }).catch(function(){});
    } else {
        copyShareUrl();
    }
});
function showCopied() {
    var el = document.getElementById('share-copied');
    el.style.opacity = '1';
    setTimeout(function() { el.style.opacity = '0'; }, 2000);
}

// ── Public Player — branded audio player ─────────────────────
(function() {
    var audio   = document.getElementById('pubAudio');
    var playBtn = document.getElementById('pubPlayBtn');
    var iconPlay  = document.getElementById('pubIconPlay');
    var iconPause = document.getElementById('pubIconPause');
    var bar     = document.getElementById('pubBar');
    var fill    = document.getElementById('pubFill');
    var thumb   = document.getElementById('pubThumb');
    var timeEl  = document.getElementById('pubTime');

    if (!audio || !playBtn) return;

    function fmt(s) {
        s = Math.floor(s || 0);
        var m = Math.floor(s / 60);
        var sec = s % 60;
        return m + ':' + (sec < 10 ? '0' : '') + sec;
    }
    function updateBar() {
        var pct = audio.duration ? (audio.currentTime / audio.duration) * 100 : 0;
        fill.style.width = pct + '%';
        thumb.style.left = pct + '%';
        if (bar) bar.setAttribute('aria-valuenow', Math.round(pct));
        timeEl.textContent = fmt(audio.currentTime) + ' / ' + fmt(audio.duration);
    }
    function setPlaying(playing) {
        iconPlay.style.display  = playing ? 'none'  : 'block';
        iconPause.style.display = playing ? 'block' : 'none';
        playBtn.setAttribute('aria-label', playing ? 'Pause' : 'Play');
    }
    playBtn.addEventListener('click', function() {
        if (audio.paused) { audio.play(); } else { audio.pause(); }
    });
    audio.addEventListener('play',  function() { setPlaying(true); });
    audio.addEventListener('pause', function() { setPlaying(false); });
    audio.addEventListener('ended', function() { setPlaying(false); updateBar(); });
    audio.addEventListener('timeupdate', updateBar);
    audio.addEventListener('loadedmetadata', updateBar);
    audio.addEventListener('durationchange', updateBar);
    if (audio.readyState >= 1) { updateBar(); }

    var dragging = false;
    function seek(e) {
        if (!audio.duration) return;
        var rect = bar.getBoundingClientRect();
        var x = (e.touches ? e.touches[0].clientX : e.clientX) - rect.left;
        var pct = Math.max(0, Math.min(1, x / rect.width));
        audio.currentTime = pct * audio.duration;
        updateBar();
    }
    bar.addEventListener('mousedown', function(e) { dragging = true; seek(e); });
    document.addEventListener('mousemove', function(e) { if (dragging) seek(e); });
    document.addEventListener('mouseup', function() { dragging = false; });
    bar.addEventListener('touchstart', function(e) { seek(e); e.preventDefault(); }, { passive: false });
    bar.addEventListener('touchmove',  function(e) { seek(e); e.preventDefault(); }, { passive: false });
    bar.addEventListener('keydown', function(e) {
        if (!audio.duration) return;
        if (e.key === 'ArrowRight') { audio.currentTime = Math.min(audio.duration, audio.currentTime + 5); }
        if (e.key === 'ArrowLeft')  { audio.currentTime = Math.max(0, audio.currentTime - 5); }
        updateBar();
    });
})();
</script>
@endsection
