@extends('layouts.app')
@section('title', $clip->display_title . ' — Clip Studio')

@section('head_extra')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;500;600;700&display=swap">
<link rel="stylesheet" href="{{ asset('css/studio.css') }}">
@endsection

@section('content')
@php
$langNames    = ['ps'=>'Pashto','fa'=>'Dari','ur'=>'Urdu','ar'=>'Arabic','hi'=>'Hindi','en'=>'English'];
$langLabel    = $langNames[$clip->language] ?? strtoupper($clip->language);
$typeLabel    = 'Song';
if ($audioAsset) {
    if ($audioAsset->type === 'bed_audio')          $typeLabel = 'Bed Music';
    elseif ($audioAsset->type === 'uploaded_audio') $typeLabel = 'Uploaded Audio';
}
$isRtl        = in_array($clip->language, ['ps','fa','ur','ar']);
$displayTitle = $clip->display_title;
@endphp

<div class="studio-wrap">

{{-- Studio context --}}
<div class="studio-context">
    <a href="{{ route('dashboard') }}" class="sh-btn sh-btn--ghost sh-btn--sm">&#8592; My Clips</a>
    <div class="studio-context__text">
        <span class="studio-context__label">Clip Studio</span>
        <span class="studio-context__sub">Manage, share, and improve this clip.</span>
    </div>
</div>

@if(session('success'))
    <div class="sh-notice sh-notice--success studio-notice">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="sh-notice sh-notice--danger studio-notice">{{ session('error') }}</div>
@endif

{{-- ═══════════════════ PROCESSING ═══════════════════ --}}
@if($clip->status === 'processing')
<div class="sh-card">
    <div class="sh-card__body studio-processing">
        <p class="sh-heading">Generating your {{ strtolower($typeLabel) }}...</p>
        <p class="sh-text-muted" style="margin-bottom:1.5rem;">This usually takes 30–180 seconds. The page will refresh automatically when ready.</p>
        <div class="studio-progress">
            <div class="studio-progress__bar studio-progress__bar--pulse" id="progress-bar"></div>
        </div>
    </div>
</div>
<script>
(function(){
    var jobId = '{{ $latestJob ? $latestJob->id : "" }}';
    var csrf  = document.querySelector('meta[name=csrf-token]') ? document.querySelector('meta[name=csrf-token]').content : '';
    if (!jobId) { setTimeout(function(){ location.reload(); }, 5000); return; }
    var tries = 0;
    function poll() {
        tries++;
        if (tries > 80) { location.reload(); return; }
        fetch('/studio/job-status/' + jobId, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function(r) { return r.json(); })
        .then(function(d) {
            if (d.status === 'done' || d.status === 'failed') { setTimeout(function() { location.reload(); }, 500); }
            else { setTimeout(poll, 3000); }
        })
        .catch(function() { setTimeout(poll, 5000); });
    }
    setTimeout(poll, 3000);
})();
</script>

{{-- ═══════════════════ FAILED ═══════════════════ --}}
@elseif($clip->status === 'failed')
<div class="sh-card">
    <div class="sh-card__body studio-failed">
        <div class="studio-failed__icon">&#9888;</div>
        <h2 class="studio-failed__heading">Your song could not be generated</h2>
        <p class="studio-failed__body">This sometimes happens with very short lyrics, unusual characters, or a temporary issue with the AI service. Your credits have been returned to your account.</p>
        <p class="studio-failed__suggestion">Try adding more lines to your lyrics, then create a new song. If the problem continues, please contact support.</p>
        @if($latestJob && $latestJob->error_message)
            <p class="studio-failed__technical">Technical detail: {{ $latestJob->error_message }}</p>
        @endif
        <div class="studio-failed__actions">
            <a href="{{ route('create') }}" class="sh-btn sh-btn--primary">Create New Song</a>
            <form method="POST" action="{{ route('studio.delete', $clip) }}" onsubmit="return confirm('Delete this clip permanently? This cannot be undone.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="sh-btn sh-btn--ghost">Delete This Clip</button>
            </form>
        </div>
    </div>
</div>

{{-- ═══════════════════ READY ═══════════════════ --}}
@else


{{-- CREATION STATE TIMELINE --}}
<div class="studio-timeline">
    <div class="studio-timeline__step studio-timeline__step--done">
        <div class="studio-timeline__dot"></div>
        <span class="studio-timeline__label">Lyrics</span>
    </div>
    <div class="studio-timeline__line studio-timeline__line--done"></div>
    <div class="studio-timeline__step {{ $audioAsset ? 'studio-timeline__step--done' : 'studio-timeline__step--missing' }}">
        <div class="studio-timeline__dot"></div>
        <span class="studio-timeline__label">Audio</span>
    </div>
    <div class="studio-timeline__line {{ $coverAsset ? 'studio-timeline__line--done' : '' }}"></div>
    <div class="studio-timeline__step {{ $coverAsset ? 'studio-timeline__step--done' : 'studio-timeline__step--missing' }}">
        <div class="studio-timeline__dot"></div>
        <span class="studio-timeline__label">Cover</span>
    </div>
    <div class="studio-timeline__line {{ $reel ? 'studio-timeline__line--done' : '' }}"></div>
    <div class="studio-timeline__step {{ $reel ? 'studio-timeline__step--done' : 'studio-timeline__step--missing' }}">
        <div class="studio-timeline__dot"></div>
        <span class="studio-timeline__label">Reel</span>
    </div>

</div>

{{-- CLIP HERO --}}
<div class="studio-hero">

    {{-- Cover / Reel (smart asset hierarchy: reel > cover > placeholder) --}}
    <div class="studio-hero__cover">
        @if($reel && $reel->cdn_url)
            <video class="studio-hero__reel-preview" src="{{ $reel->cdn_url }}" controls playsinline loop></video>
            <p class="studio-hero__reel-label">Reel Preview</p>
        @elseif($coverAsset && $coverAsset->cdn_url)
            <img src="{{ $coverAsset->cdn_url }}" alt="{{ $displayTitle }}" class="studio-hero__cover-img">
        @else
            <div class="studio-hero__cover-placeholder">
                <span class="studio-hero__cover-icon">&#9834;</span>
                <span class="studio-hero__cover-text">No cover yet</span>
            </div>
        @endif
    </div>

    {{-- Panel --}}
    <div class="studio-hero__panel">

        {{-- Title + badges --}}
        <div class="studio-hero__identity">
            <h1 class="studio-hero__title">{{ $displayTitle }}</h1>
            <div class="studio-badge-row">
                <span class="sh-badge sh-badge--lang">{{ $langLabel }}</span>
                <span class="sh-badge">{{ $typeLabel }}</span>
                <span class="sh-badge sh-badge--{{ $clip->status }}">{{ ucfirst($clip->status) }}</span>
                <span class="sh-badge">{{ ucfirst($clip->visibility) }}</span>
            </div>
        </div>

        {{-- Custom audio player --}}
        @if($audioAsset && $audioAsset->cdn_url)
        <div class="sh-audio-player" id="studioPlayer">
            <p class="sh-audio-player__label">Listen to your {{ strtolower($typeLabel) }}</p>
            <div class="sh-audio-player__ui">
                <button class="sh-audio-player__playbtn" id="studioPlayBtn" aria-label="Play">
                    <svg id="studioIconPlay" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M8 5v14l11-7z"/></svg>
                    <svg id="studioIconPause" viewBox="0 0 24 24" fill="currentColor" style="display:none;" aria-hidden="true"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>
                </button>
                <div class="sh-audio-player__progress-wrap">
                    <div class="sh-audio-player__bar" id="studioBar" role="slider" aria-label="Seek" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" tabindex="0">
                        <div class="sh-audio-player__bar-fill" id="studioFill"></div>
                        <div class="sh-audio-player__bar-thumb" id="studioThumb"></div>
                    </div>
                </div>
                <span class="sh-audio-player__time" id="studioTime">0:00 / 0:00</span>
            </div>
            {{-- Native audio kept as DOM fallback — hidden, still functional if JS fails --}}
            <audio id="studioAudio" class="sh-audio-player__native" controls>
                <source src="{{ $audioAsset->cdn_url }}" type="{{ $audioAsset->mime_type ?? 'audio/mpeg' }}">
            </audio>
        </div>
        @else
        <div class="sh-notice sh-notice--info studio-hero__no-audio">Audio is being processed. Please refresh in a moment.</div>
        @endif

        {{-- Actions --}}
        {{-- Next Best Action system --}}
        <div class="studio-hero__actions">

            @if($reel && $reel->cdn_url)
                {{-- STATE: Reel ready --}}
                <p class="studio-nba__label">&#10003; Your reel is ready</p>
                <a href="{{ $reel->cdn_url }}" download class="sh-btn sh-btn--primary studio-hero__action-primary"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" width="15" height="15"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg> Download Reel</a>


            @elseif(isset($reelJob) && $reelJob && in_array($reelJob->status, ['pending', 'running']))
                {{-- STATE: Reel generating --}}
                <p class="studio-nba__label">Creating your reel...</p>
                <button type="button" class="sh-btn sh-btn--primary studio-hero__action-primary studio-hero__action--muted" disabled>Creating Reel...</button>
                <p class="studio-hero__action-hint">This page will update automatically when your reel is ready.</p>

            @elseif(isset($reelJob) && $reelJob && $reelJob->status === 'failed')
                {{-- STATE: Reel failed --}}
                <p class="studio-nba__label studio-nba__label--warn">Reel could not be created</p>
                <form method="POST" action="{{ route('studio.reel', $clip) }}" class="studio-hero__reel-form">
                    @csrf
                    <button type="submit" class="sh-btn sh-btn--primary studio-hero__action-primary">Try Again</button>
                </form>
                <p class="studio-hero__action-hint">If it keeps failing, try regenerating your cover first.</p>

            @elseif(!$coverAsset)
                {{-- STATE: No cover — suggest generate cover first --}}
                <p class="studio-nba__label">&#8594; Next: Generate a cover image</p>
                <a href="#studio-cover" class="sh-btn sh-btn--primary studio-hero__action-primary" onclick="document.querySelector('.studio-cover-card').scrollIntoView({behavior:'smooth'});return false;">Generate Cover</a>
                <form method="POST" action="{{ route('studio.reel', $clip) }}" class="studio-hero__reel-form" style="margin-top:0.5rem;">
                    @csrf
                    <button type="submit" class="sh-btn sh-btn--ghost studio-hero__action-secondary">Skip — Create Reel Without Cover</button>
                </form>

            @else
                {{-- STATE: Has cover, no reel — suggest create reel --}}
                <p class="studio-nba__label">&#8594; Next: Create a shareable reel</p>
                <form method="POST" action="{{ route('studio.reel', $clip) }}" class="studio-hero__reel-form">
                    @csrf
                    <button type="submit" class="sh-btn sh-btn--primary studio-hero__action-primary">Create Reel</button>
                </form>
            @endif

            {{-- Secondary row: always available when assets exist --}}
            <div class="studio-hero__action-row" style="margin-top:0.75rem;">
                @if($audioAsset && $audioAsset->cdn_url)
                <a href="{{ $audioAsset->cdn_url }}" download class="sh-btn {{ $reel && $reel->cdn_url ? 'sh-btn--ghost studio-hero__action-secondary' : 'sh-btn--primary studio-hero__action-primary' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" width="15" height="15"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Download MP3
                </a>
                @endif
                @if($clip->visibility === 'public' && $clip->slug)
                <button type="button" class="sh-btn sh-btn--ghost studio-hero__action-secondary" onclick="studioShare(this)" data-url="{{ route('player.show', $clip->slug) }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" width="15" height="15"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                    Copy Link
                </button>
                <a href="{{ route('player.show', $clip->slug) }}" target="_blank" class="sh-btn sh-btn--ghost studio-hero__action-secondary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" width="15" height="15"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                    Open Public Player
                </a>
                @endif
            </div>
            @if($clip->visibility !== 'public')
            <p class="studio-hero__action-hint">Set visibility to Public to share this clip</p>
            @endif

        </div>

        {{-- Visibility --}}
        <div class="studio-hero__visibility">
            <form method="POST" action="{{ route('studio.visibility', $clip) }}" class="studio-hero__vis-form">
                @csrf
                @method('PATCH')
                <span class="studio-hero__vis-label">Visibility:</span>
                <select name="visibility" class="sh-select sh-select--sm" onchange="this.form.submit()">
                    <option value="private" {{ $clip->visibility === 'private' ? 'selected' : '' }}>Private</option>
                    <option value="public"  {{ $clip->visibility === 'public'  ? 'selected' : '' }}>Public</option>
                </select>
            </form>
            <p class="studio-hero__vis-hint">Public clips are shareable. Discover display requires admin approval.</p>
        </div>

    </div>
</div>

{{-- CLIP TITLE (rename) --}}
<div class="studio-tool-row">
    <span class="studio-tool-row__label">Clip Title</span>
    <form method="POST" action="{{ route('studio.rename', $clip) }}" class="studio-inline-form">
        @csrf
        @method('PATCH')
        <input type="text" name="title" class="sh-input sh-input--sm" value="{{ $clip->title }}" placeholder="Enter a title for this clip">
        <button type="submit" class="sh-btn sh-btn--ghost sh-btn--sm">Save</button>
    </form>
</div>

{{-- UPLOAD OWN COVER --}}
<div class="sh-card studio-cover-card">
    <div class="sh-card__header">Upload Your Own Cover</div>
    <div class="sh-card__body">
        <form method="POST" action="{{ route('studio.cover.upload', $clip) }}" enctype="multipart/form-data" class="studio-cover-upload-form">
            @csrf
            <div class="studio-cover-upload-row">
                <input type="file" name="cover_file" id="cover_file" accept=".jpg,.jpeg,.png,.webp" class="studio-cover-upload-input">
                <label for="cover_file" class="sh-btn sh-btn--ghost sh-btn--sm studio-cover-upload-label">Choose Image</label>
                <span class="studio-cover-upload-name" id="cover-file-name">JPG, PNG, or WebP — max 5MB</span>
                <button type="submit" class="sh-btn sh-btn--primary sh-btn--sm">Upload Cover</button>
            </div>
            @error("cover_file")
                <p class="sh-field-error" style="margin-top:0.5rem;">{{ $message }}</p>
            @enderror
        </form>
    </div>
</div>

{{-- COVER TOOLS --}}
<div class="sh-card studio-cover-card">
    <div class="sh-card__header">Cover Image</div>
    <div class="sh-card__body">
        <form method="POST" action="{{ route('studio.cover', $clip) }}">
            @csrf
            <div class="studio-cover-grid">
                <div class="sh-field">
                    <label class="sh-label">Cover Style</label>
                    <select name="style" class="sh-select sh-select--sm">
                        <option value="artistic">Artistic music album cover</option>
                        <option value="photo">Photo-realistic scene</option>
                        <option value="poetic">Poetic / symbolic</option>
                        <option value="cultural">Traditional cultural style</option>
                        <option value="cinematic">Modern cinematic</option>
                        <option value="minimal">Minimal / clean</option>
                        <option value="dramatic">Dramatic emotional</option>
                    </select>
                </div>
                <div class="sh-field">
                    <label class="sh-label">Mood</label>
                    <select name="mood" class="sh-select sh-select--sm">
                        <option value="">Any mood</option>
                        <option value="calm">Calm</option>
                        <option value="romantic">Romantic</option>
                        <option value="sad">Sad</option>
                        <option value="hopeful">Hopeful</option>
                        <option value="patriotic">Patriotic</option>
                        <option value="mystical">Mystical</option>
                        <option value="joyful">Joyful</option>
                        <option value="dramatic">Dramatic</option>
                    </select>
                </div>
                <div class="sh-field">
                    <label class="sh-label">Text on Cover</label>
                    <select name="text_on_cover" class="sh-select sh-select--sm">
                        <option value="none">No text</option>
                        <option value="title">Song title only</option>
                    </select>
                </div>
            </div>
            <div class="sh-field" style="margin-top:0.75rem;">
                <label class="sh-label">Visual Direction <span class="studio-optional">(optional)</span></label>
                <input type="text" name="visual_direction" class="sh-input sh-input--sm" placeholder="e.g. a lonely traveler under moonlight, traditional Pashto rubab mood">
            </div>
            <div style="margin-top:0.85rem;">
                <button type="submit" class="sh-btn sh-btn--primary sh-btn--sm">
                    {{ $coverAsset ? 'Regenerate AI Cover' : 'Generate AI Cover' }}
                </button>
            </div>
        </form>
    </div>
</div>

{{-- LYRICS --}}
@if($clip->lyrics_input && $typeLabel !== 'Bed Music')
<div class="sh-card studio-lyrics-card">
    <div class="sh-card__header">{{ $isRtl ? 'Lyrics / Poem' : 'Lyrics' }}</div>
    <div class="sh-card__body">
        <div class="studio-lyrics {{ $isRtl ? 'studio-lyrics--rtl' : 'studio-lyrics--ltr' }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
            {{ $clip->lyrics_input }}
        </div>
    </div>
</div>
@endif

{{-- DETAILS (no heading, muted) --}}
<div class="sh-card studio-details-card">
    <div class="sh-card__body">
        <div class="studio-meta-grid">
            <div class="studio-meta-item">
                <span class="studio-meta-label">Language</span>
                <span class="studio-meta-value">{{ $langLabel }}</span>
            </div>
            <div class="studio-meta-item">
                <span class="studio-meta-label">Type</span>
                <span class="studio-meta-value">{{ $typeLabel }}</span>
            </div>
            <div class="studio-meta-item">
                <span class="studio-meta-label">Status</span>
                <span class="studio-meta-value">{{ ucfirst($clip->status) }}</span>
            </div>
            <div class="studio-meta-item">
                <span class="studio-meta-label">Visibility</span>
                <span class="studio-meta-value">{{ ucfirst($clip->visibility) }}</span>
            </div>
            @if($latestJob && $latestJob->credits_charged !== null)
            <div class="studio-meta-item">
                <span class="studio-meta-label">Credits used</span>
                <span class="studio-meta-value">{{ $latestJob->credits_charged }}</span>
            </div>
            @endif
            <div class="studio-meta-item">
                <span class="studio-meta-label">Created</span>
                <span class="studio-meta-value">{{ $clip->created_at->diffForHumans() }}</span>
            </div>
        </div>
    </div>
</div>

{{-- DANGER ZONE --}}
<div class="sh-card studio-danger-card">
    <div class="sh-card__header">Danger Zone</div>
    <div class="sh-card__body">
        <p class="studio-danger-warning">Deleting this clip is permanent. The audio file, cover image, and all associated data will be removed and cannot be recovered.</p>
        <form method="POST" action="{{ route('studio.delete', $clip) }}" onsubmit="return confirm('Delete this clip permanently? This cannot be undone.')">
            @csrf
            @method('DELETE')
            <button type="submit" class="sh-btn sh-btn--danger">Delete Clip</button>
        </form>
    </div>
</div>

@endif
</div>

<script>
// ── Upload cover filename display ──────────────────────────────────
document.getElementById("cover_file") && document.getElementById("cover_file").addEventListener("change", function() {
    var name = this.files[0] ? this.files[0].name : "JPG, PNG, or WebP — max 5MB";
    document.getElementById("cover-file-name").textContent = name;
});

// ── Share button ─────────────────────────────────────────────────
function studioShare(btn) {
    var url = btn.getAttribute('data-url');
    if (navigator.clipboard) {
        navigator.clipboard.writeText(url).then(function() {
            var orig = btn.textContent;
            btn.textContent = 'Copied!';
            setTimeout(function() { btn.textContent = orig; }, 2000);
        });
    } else {
        window.prompt('Copy this link:', url);
    }
}

// ── Custom audio player ──────────────────────────────────────────
(function() {
    var audio   = document.getElementById('studioAudio');
    var playBtn = document.getElementById('studioPlayBtn');
    var iconPlay  = document.getElementById('studioIconPlay');
    var iconPause = document.getElementById('studioIconPause');
    var bar     = document.getElementById('studioBar');
    var fill    = document.getElementById('studioFill');
    var thumb   = document.getElementById('studioThumb');
    var timeEl  = document.getElementById('studioTime');

    if (!audio || !playBtn) return;

    function fmt(s) {
        s = Math.floor(s || 0);
        var m = Math.floor(s / 60);
        var sec = s % 60;
        return m + ':' + (sec < 10 ? '0' : '') + sec;
    }

    function updateBar() {
        var pct = audio.duration ? (audio.currentTime / audio.duration) * 100 : 0;
        fill.style.width  = pct + '%';
        thumb.style.left  = pct + '%';
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

    audio.addEventListener('play',  function() { setPlaying(true);  });
    audio.addEventListener('pause', function() { setPlaying(false); });
    audio.addEventListener('ended', function() { setPlaying(false); updateBar(); });
    audio.addEventListener('timeupdate', updateBar);
    audio.addEventListener('loadedmetadata', updateBar);
    audio.addEventListener('durationchange', updateBar);
    if (audio.readyState >= 1) { updateBar(); }

    // Progress bar seeking — mouse
    function seek(e) {
        if (!audio.duration) return;
        var rect = bar.getBoundingClientRect();
        var x    = (e.touches ? e.touches[0].clientX : e.clientX) - rect.left;
        var pct  = Math.max(0, Math.min(1, x / rect.width));
        audio.currentTime = pct * audio.duration;
        updateBar();
    }
    var dragging = false;
    bar.addEventListener('mousedown',  function(e) { dragging = true; seek(e); });
    document.addEventListener('mousemove', function(e) { if (dragging) seek(e); });
    document.addEventListener('mouseup',   function()  { dragging = false; });
    // Touch
    bar.addEventListener('touchstart', function(e) { seek(e); e.preventDefault(); }, { passive: false });
    bar.addEventListener('touchmove',  function(e) { seek(e); e.preventDefault(); }, { passive: false });
    // Keyboard
    bar.addEventListener('keydown', function(e) {
        if (!audio.duration) return;
        if (e.key === 'ArrowRight') { audio.currentTime = Math.min(audio.duration, audio.currentTime + 5); }
        if (e.key === 'ArrowLeft')  { audio.currentTime = Math.max(0, audio.currentTime - 5); }
        updateBar();
    });
})();

// ── Reel polling ────────────────────────────────────────────────────
(function() {
    var reelJobStatus = "{{ isset($reelJob) && $reelJob ? $reelJob->status : 'none' }}";
    if (reelJobStatus === "pending" || reelJobStatus === "running") {
        var clipId = "{{ $clip->id }}";
        var csrf   = document.querySelector("meta[name=csrf-token]") ? document.querySelector("meta[name=csrf-token]").content : "";
        var tries  = 0;
        function pollReel() {
            tries++;
            if (tries > 60) return;
            fetch("/studio/clip-status/" + clipId, {
                headers: { "Accept": "application/json", "X-CSRF-TOKEN": csrf, "X-Requested-With": "XMLHttpRequest" }
            })
            .then(function(r) { return r.json(); })
            .then(function(d) {
                if (d.cover_status === "done" || tries % 5 === 0) { location.reload(); }
                else { setTimeout(pollReel, 5000); }
            })
            .catch(function() { setTimeout(pollReel, 7000); });
        }
        setTimeout(pollReel, 5000);
    }
})();

// ── Cover polling (TODO: replace with proper job status endpoint) ─
(function() {
    var hasCover   = {{ $coverAsset ? 'true' : 'false' }};
    var sessionMsg = '{{ addslashes(session("success") ?? "") }}';
    if (!hasCover) {
        var clipId = '{{ $clip->id }}';
        var csrf   = document.querySelector('meta[name=csrf-token]') ? document.querySelector('meta[name=csrf-token]').content : '';
        var t = 0;
        function pollCover() {
            t++;
            if (t > 40) return;
            fetch('/studio/clip-status/' + clipId, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(function(r) { return r.json(); })
            .then(function(d) { if (d.cover_ready) { location.reload(); } else { setTimeout(pollCover, 4000); } })
            .catch(function() { setTimeout(pollCover, 6000); });
        }
        setTimeout(pollCover, 4000);
    }
})();
</script>
@endsection
