@extends('layouts.app')
@section('title', $clip->title . ' — Clip Studio')
@section('content')
<?php
$langNames = ['ps'=>'Pashto','fa'=>'Dari','ur'=>'Urdu','ar'=>'Arabic','hi'=>'Hindi','en'=>'English'];
$langLabel = $langNames[$clip->language] ?? strtoupper($clip->language);
$typeLabel = $audioAsset ? ($audioAsset->type === 'bed_audio' ? 'Bed Music' : ($audioAsset->type === 'uploaded_audio' ? 'Uploaded Audio' : 'Song')) : 'Song';
$isRtl = in_array($clip->language, ['ps','fa','ur','ar']);
?>
<div class="studio-wrap">

{{-- BACK --}}
<div class="studio-back">
    <a href="{{ route('dashboard') }}" class="sh-btn sh-btn--ghost sh-btn--sm">&#8592; My Clips</a>
</div>

{{-- FLASH --}}
@if(session('success'))
    <div class="sh-notice sh-notice--success" style="margin-bottom:1rem;">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="sh-notice sh-notice--danger" style="margin-bottom:1rem;">{{ session('error') }}</div>
@endif

{{-- PROCESSING --}}
@if($clip->status === 'processing')
<div class="sh-card">
    <div class="sh-card__body studio-processing">
        <p class="sh-heading">Generating your {{ strtolower($typeLabel) }}...</p>
        <p class="sh-text-muted">This usually takes 30–180 seconds. Page will refresh automatically.</p>
        <div class="studio-progress" style="margin-top:1.5rem;">
            <div class="studio-progress__bar studio-progress__bar--pulse" id="progress-bar"></div>
        </div>
    </div>
</div>
<script>
(function(){
    var jobId = '{{ $latestJob ? $latestJob->id : "" }}';
    var csrf = document.querySelector('meta[name=csrf-token]') ? document.querySelector('meta[name=csrf-token]').content : '';
    if(!jobId){ setTimeout(function(){ location.reload(); }, 5000); return; }
    var tries = 0;
    function poll(){
        tries++;
        if(tries > 80){ location.reload(); return; }
        fetch('/studio/job-status/' + jobId, {headers:{'Accept':'application/json','X-CSRF-TOKEN':csrf,'X-Requested-With':'XMLHttpRequest'}})
        .then(function(r){ return r.json(); })
        .then(function(d){
            if(d.status === 'done' || d.status === 'failed'){ setTimeout(function(){ location.reload(); }, 500); }
            else { setTimeout(poll, 3000); }
        })
        .catch(function(){ setTimeout(poll, 5000); });
    }
    setTimeout(poll, 3000);
})();
</script>

{{-- FAILED --}}
@elseif($clip->status === 'failed')
<div class="sh-card">
    <div class="sh-card__body">
        <div class="sh-notice sh-notice--danger">
            Generation failed. Your credits have been released.
            @if($latestJob && $latestJob->error_message)
                <br><small>{{ $latestJob->error_message }}</small>
            @endif
        </div>
        <a href="{{ route('create') }}" class="sh-btn sh-btn--primary" style="margin-top:1rem;">Try Again</a>
    </div>
</div>

{{-- READY --}}
@else

<div class="studio-layout">

    {{-- LEFT: Cover + Cover Tools --}}
    <div class="studio-left">

        {{-- LARGE COVER --}}
        <div class="studio-hero-cover">
            @if($coverAsset && $coverAsset->cdn_url)
                <img src="{{ $coverAsset->cdn_url }}"
                     alt="{{ $clip->title }}"
                     class="studio-hero-cover__img">
            @else
                <div class="studio-hero-cover__placeholder">
                    <svg viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg" class="studio-hero-cover__icon">
                        <circle cx="40" cy="40" r="40" fill="#2a2a2a"/>
                        <path d="M32 52V30l24 8-24 14z" fill="#E8732A" opacity="0.8"/>
                    </svg>
                    <p class="studio-hero-cover__label">No cover yet</p>
                </div>
            @endif
        </div>

        {{-- COVER TOOLS --}}
        <div class="sh-card studio-section">
            <div class="sh-card__header">Cover Image</div>
            <div class="sh-card__body">
                <form method="POST" action="{{ route('studio.cover', $clip) }}">
                    @csrf
                    <input type="text" name="description" class="sh-input"
                           placeholder="Describe the cover style (optional)"
                           style="margin-bottom:0.6rem;">
                    <button type="submit" class="sh-btn sh-btn--primary sh-btn--full sh-btn--sm">
                        {{ $coverAsset ? '&#8635; Regenerate AI Cover' : '&#10024; Generate AI Cover' }}
                    </button>
                </form>
                <button class="sh-btn sh-btn--ghost sh-btn--full sh-btn--sm"
                        disabled style="margin-top:0.5rem;opacity:0.45;cursor:not-allowed;">
                    &#8593; Upload Cover <small>(coming soon)</small>
                </button>
            </div>
        </div>

    </div>{{-- end studio-left --}}

    {{-- RIGHT: Hero + Actions + Manage + Details --}}
    <div class="studio-right">

        {{-- HERO CARD --}}
        <div class="sh-card studio-section studio-hero-card">
            <div class="sh-card__body">
                <h1 class="studio-clip-title">{{ $clip->title }}</h1>
                <div class="studio-badges">
                    <span class="sh-badge sh-badge--lang">{{ $langLabel }}</span>
                    <span class="sh-badge">{{ $typeLabel }}</span>
                    <span class="sh-badge sh-badge--{{ $clip->status }}">{{ ucfirst($clip->status) }}</span>
                    <span class="sh-badge">{{ ucfirst($clip->visibility) }}</span>
                </div>

                {{-- AUDIO PLAYER --}}
                @if($audioAsset && $audioAsset->cdn_url)
                <div class="studio-player">
                    <p class="studio-player__label">Listen to your {{ strtolower($typeLabel) }}</p>
                    <audio controls class="studio-player__audio">
                        <source src="{{ $audioAsset->cdn_url }}"
                                type="{{ $audioAsset->mime_type ?? 'audio/mpeg' }}">
                    </audio>
                </div>
                @else
                <div class="sh-notice sh-notice--info" style="margin-top:1rem;">
                    Audio is being processed. Please refresh in a moment.
                </div>
                @endif

                {{-- QUICK ACTIONS --}}
                <div class="studio-quick-actions">
                    @if($audioAsset && $audioAsset->cdn_url)
                    <a href="{{ $audioAsset->cdn_url }}" download
                       class="sh-btn sh-btn--primary sh-btn--sm">&#8595; Download</a>
                    @endif
                    @if($clip->visibility === 'public')
                    <button type="button" class="sh-btn sh-btn--ghost sh-btn--sm"
                            onclick="studioShare(this)"
                            data-url="{{ route('player.show', $clip) }}">
                        &#128279; Copy Link
                    </button>
                    @endif
                    @if($reel && $reel->cdn_url)
                    <a href="{{ $reel->cdn_url }}" download
                       class="sh-btn sh-btn--ghost sh-btn--sm">&#8595; Reel</a>
                    @else
                    <form method="POST" action="{{ route('studio.reel', $clip) }}" style="display:inline;">
                        @csrf
                        <button type="submit" class="sh-btn sh-btn--ghost sh-btn--sm">&#127902; Create Reel</button>
                    </form>
                    @endif
                </div>
            </div>
        </div>

        {{-- MANAGE --}}
        <div class="sh-card studio-section">
            <div class="sh-card__header">Manage</div>
            <div class="sh-card__body">
                <div class="studio-manage-item">
                    <label class="sh-label">Rename clip</label>
                    <form method="POST" action="{{ route('studio.rename', $clip) }}" class="studio-inline-form">
                        @csrf
                        @method('PATCH')
                        <input type="text" name="title" class="sh-input sh-input--sm"
                               value="{{ $clip->title }}">
                        <button type="submit" class="sh-btn sh-btn--ghost sh-btn--sm">Save</button>
                    </form>
                </div>
                <div class="studio-manage-item">
                    <label class="sh-label">Visibility</label>
                    <form method="POST" action="{{ route('studio.visibility', $clip) }}" class="studio-inline-form">
                        @csrf
                        @method('PATCH')
                        <select name="visibility" class="sh-select sh-select--sm" onchange="this.form.submit()">
                            <option value="private" {{ $clip->visibility === 'private' ? 'selected' : '' }}>Private</option>
                            <option value="public" {{ $clip->visibility === 'public' ? 'selected' : '' }}>Public / Shareable</option>
                        </select>
                    </form>
                    <p class="sh-field-hint">Public clips are shareable but only appear on Discover after admin approval.</p>
                </div>
            </div>
        </div>

        {{-- DETAILS --}}
        <div class="sh-card studio-section">
            <div class="sh-card__header">Details</div>
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

    </div>{{-- end studio-right --}}
</div>{{-- end studio-layout --}}

{{-- LYRICS full width --}}
@if($clip->lyrics_input && $typeLabel !== 'Bed Music')
<div class="sh-card studio-section">
    <div class="sh-card__header">
        Lyrics @if($isRtl)/ <span>شعر</span>@endif
    </div>
    <div class="sh-card__body">
        <div class="studio-lyrics {{ $isRtl ? 'studio-lyrics--rtl' : 'studio-lyrics--ltr' }}"
             dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
            {!! nl2br(e($clip->lyrics_input)) !!}
        </div>
    </div>
</div>
@endif

{{-- DANGER ZONE --}}
<div class="sh-card studio-section studio-danger-card">
    <div class="sh-card__header studio-danger-card__header">Danger Zone</div>
    <div class="sh-card__body">
        <p class="sh-text-muted" style="font-size:0.875rem;margin-bottom:1rem;">
            Deleting this clip is permanent. The audio, cover, and all data will be removed and cannot be recovered.
        </p>
        <form method="POST" action="{{ route('studio.delete', $clip) }}"
              onsubmit="return confirm('Delete this clip permanently? This cannot be undone.')">
            @csrf
            @method('DELETE')
            <button type="submit" class="sh-btn sh-btn--danger">Delete Clip</button>
        </form>
    </div>
</div>

@endif{{-- end ready --}}
</div>{{-- end studio-wrap --}}

<script>
function studioShare(btn) {
    var url = btn.getAttribute('data-url');
    if (navigator.clipboard) {
        navigator.clipboard.writeText(url).then(function(){
            var orig = btn.textContent;
            btn.textContent = 'Copied!';
            setTimeout(function(){ btn.textContent = orig; }, 2000);
        });
    } else {
        window.prompt('Copy this link:', url);
    }
}
(function(){
    var hasCover = {{ $coverAsset ? 'true' : 'false' }};
    var sessionMsg = '{{ addslashes(session("success") ?? "") }}';
    if(!hasCover && sessionMsg.indexOf('generated') !== -1){
        var clipId = '{{ $clip->id }}';
        var csrf = document.querySelector('meta[name=csrf-token]') ? document.querySelector('meta[name=csrf-token]').content : '';
        var t = 0;
        function pollCover(){
            t++;
            if(t > 40) return;
            fetch('/studio/clip-status/' + clipId, {headers:{'Accept':'application/json','X-CSRF-TOKEN':csrf,'X-Requested-With':'XMLHttpRequest'}})
            .then(function(r){ return r.json(); })
            .then(function(d){ if(d.cover_ready){ location.reload(); } else { setTimeout(pollCover, 4000); } })
            .catch(function(){ setTimeout(pollCover, 6000); });
        }
        setTimeout(pollCover, 4000);
    }
})();
</script>
@endsection
