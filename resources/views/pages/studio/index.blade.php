@extends('layouts.app')
@section('title', $clip->title . ' — Clip Studio')

@section('head_extra')
<link rel="stylesheet" href="{{ asset('css/studio.css') }}">
@endsection

@section('content')
<?php
$langNames = ['ps'=>'Pashto','fa'=>'Dari','ur'=>'Urdu','ar'=>'Arabic','hi'=>'Hindi','en'=>'English'];
$langLabel = $langNames[$clip->language] ?? strtoupper($clip->language);
$typeLabel = 'Song';
if ($audioAsset) {
    if ($audioAsset->type === 'bed_audio') $typeLabel = 'Bed Music';
    elseif ($audioAsset->type === 'uploaded_audio') $typeLabel = 'Uploaded Audio';
}
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

    {{-- ═══════════════════════════════════════════════════════
         PROCESSING STATE
    ═══════════════════════════════════════════════════════ --}}
    @if($clip->status === 'processing')
    <div class="sh-card">
        <div class="sh-card__body studio-processing">
            <p class="sh-heading">Generating your {{ strtolower($typeLabel) }}...</p>
            <p class="sh-text-muted" style="margin-bottom:1.5rem;">
                This usually takes 30–180 seconds. The page will refresh automatically when ready.
            </p>
            <div class="studio-progress">
                <div class="studio-progress__bar studio-progress__bar--pulse" id="progress-bar"></div>
            </div>
        </div>
    </div>
    <script>
    (function(){
        var jobId = '{{ $latestJob ? $latestJob->id : "" }}';
        var csrf  = document.querySelector('meta[name=csrf-token]')
                  ? document.querySelector('meta[name=csrf-token]').content : '';
        if (!jobId) { setTimeout(function(){ location.reload(); }, 5000); return; }
        var tries = 0;
        function poll() {
            tries++;
            if (tries > 80) { location.reload(); return; }
            fetch('/studio/job-status/' + jobId, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(function(r) { return r.json(); })
            .then(function(d) {
                if (d.status === 'done' || d.status === 'failed') {
                    setTimeout(function() { location.reload(); }, 500);
                } else {
                    setTimeout(poll, 3000);
                }
            })
            .catch(function() { setTimeout(poll, 5000); });
        }
        setTimeout(poll, 3000);
    })();
    </script>

    {{-- ═══════════════════════════════════════════════════════
         FAILED STATE
    ═══════════════════════════════════════════════════════ --}}
    @elseif($clip->status === 'failed')
    <div class="sh-card">
        <div class="sh-card__body">
            <div class="sh-notice sh-notice--danger">
                Generation failed. Your credits have been released.
                @if($latestJob && $latestJob->error_message)
                    <br><small>{{ $latestJob->error_message }}</small>
                @endif
            </div>
            <a href="{{ route('create') }}" class="sh-btn sh-btn--primary" style="margin-top:1rem;display:inline-block;">
                Try Again
            </a>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════
         READY STATE
    ═══════════════════════════════════════════════════════ --}}
    @else

    <div class="studio-layout">

        {{-- ── LEFT COLUMN: Cover + Cover Tools ─────────────── --}}
        <div class="studio-left">

            {{-- Large cover image --}}
            <div class="studio-cover-block">
                @if($coverAsset && $coverAsset->cdn_url)
                    <img src="{{ $coverAsset->cdn_url }}"
                         alt="{{ $clip->title }}"
                         class="studio-cover-block__img">
                @else
                    <div class="studio-cover-block__placeholder">
                        <div class="studio-cover-block__placeholder-icon">&#9834;</div>
                        <p class="studio-cover-block__placeholder-text">No cover yet</p>
                    </div>
                @endif
            </div>

            {{-- Cover tools --}}
            <div class="sh-card studio-cover-tools">
                <div class="sh-card__header">Cover Image</div>
                <div class="sh-card__body">
                    <form method="POST" action="{{ route('studio.cover', $clip) }}">
                        @csrf
                        <input type="text" name="description" class="sh-input"
                               placeholder="Describe the cover style (optional)">
                        <button type="submit" class="sh-btn sh-btn--primary sh-btn--full sh-btn--sm"
                                style="margin-top:0.6rem;">
                            @if($coverAsset)
                                Regenerate AI Cover
                            @else
                                Generate AI Cover
                            @endif
                        </button>
                    </form>
                    <button class="sh-btn sh-btn--ghost sh-btn--full sh-btn--sm"
                            disabled
                            style="margin-top:0.5rem;opacity:0.4;cursor:not-allowed;">
                        Upload Cover <small>(coming soon)</small>
                    </button>
                </div>
            </div>

        </div>{{-- end studio-left --}}

        {{-- ── RIGHT COLUMN: Hero, Player, Actions, Manage, Details ── --}}
        <div class="studio-right">

            {{-- Hero card: title, badges, player, quick actions --}}
            <div class="sh-card studio-hero-card">
                <div class="sh-card__body">

                    <h1 class="studio-clip-title">{{ $clip->title }}</h1>

                    <div class="studio-badge-row">
                        <span class="sh-badge sh-badge--lang">{{ $langLabel }}</span>
                        <span class="sh-badge">{{ $typeLabel }}</span>
                        <span class="sh-badge sh-badge--{{ $clip->status }}">{{ ucfirst($clip->status) }}</span>
                        <span class="sh-badge">{{ ucfirst($clip->visibility) }}</span>
                    </div>

                    {{-- Audio player --}}
                    @if($audioAsset && $audioAsset->cdn_url)
                    <div class="studio-player-block">
                        <p class="studio-player-block__label">Listen to your {{ strtolower($typeLabel) }}</p>
                        <audio controls class="studio-player-block__audio">
                            <source src="{{ $audioAsset->cdn_url }}"
                                    type="{{ $audioAsset->mime_type ?? 'audio/mpeg' }}">
                        </audio>
                    </div>
                    @else
                    <div class="sh-notice sh-notice--info" style="margin:1rem 0;">
                        Audio is being processed. Please refresh in a moment.
                    </div>
                    @endif

                    {{-- Quick actions --}}
                    <div class="studio-quick-actions">
                        @if($audioAsset && $audioAsset->cdn_url)
                        <a href="{{ $audioAsset->cdn_url }}" download
                           class="sh-btn sh-btn--primary sh-btn--sm">
                            &#8595; Download Audio
                        </a>
                        @endif

                        @if($clip->visibility === 'public')
                        <button type="button"
                                class="sh-btn sh-btn--ghost sh-btn--sm"
                                onclick="studioShare(this)"
                                data-url="{{ route('player.show', $clip) }}">
                            Copy Share Link
                        </button>
                        @endif

                        @if($reel && $reel->cdn_url)
                        <a href="{{ $reel->cdn_url }}" download
                           class="sh-btn sh-btn--ghost sh-btn--sm">
                            &#8595; Download Reel
                        </a>
                        @else
                        <form method="POST" action="{{ route('studio.reel', $clip) }}"
                              style="display:inline;">
                            @csrf
                            <button type="submit" class="sh-btn sh-btn--ghost sh-btn--sm">
                                Create Reel
                            </button>
                        </form>
                        @endif
                    </div>

                </div>
            </div>{{-- end hero card --}}

            {{-- Manage --}}
            <div class="sh-card studio-manage-card">
                <div class="sh-card__header">Manage</div>
                <div class="sh-card__body">

                    <div class="studio-manage-row">
                        <label class="sh-label">Rename clip</label>
                        <form method="POST"
                              action="{{ route('studio.rename', $clip) }}"
                              class="studio-inline-form">
                            @csrf
                            @method('PATCH')
                            <input type="text" name="title"
                                   class="sh-input sh-input--sm"
                                   value="{{ $clip->title }}">
                            <button type="submit"
                                    class="sh-btn sh-btn--ghost sh-btn--sm">Save</button>
                        </form>
                    </div>

                    <div class="studio-manage-row">
                        <label class="sh-label">Visibility</label>
                        <form method="POST"
                              action="{{ route('studio.visibility', $clip) }}"
                              class="studio-inline-form">
                            @csrf
                            @method('PATCH')
                            <select name="visibility"
                                    class="sh-select sh-select--sm"
                                    onchange="this.form.submit()">
                                <option value="private"
                                    {{ $clip->visibility === 'private' ? 'selected' : '' }}>
                                    Private
                                </option>
                                <option value="public"
                                    {{ $clip->visibility === 'public' ? 'selected' : '' }}>
                                    Public / Shareable
                                </option>
                            </select>
                        </form>
                        <p class="sh-field-hint" style="margin-top:0.4rem;">
                            Public clips are shareable but only appear on Discover after admin approval.
                        </p>
                    </div>

                </div>
            </div>{{-- end manage --}}

            {{-- Details --}}
            <div class="sh-card studio-details-card">
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
            </div>{{-- end details --}}

        </div>{{-- end studio-right --}}
    </div>{{-- end studio-layout --}}

    {{-- LYRICS — full width below both columns --}}
    @if($clip->lyrics_input && $typeLabel !== 'Bed Music')
    <div class="sh-card studio-lyrics-card">
        <div class="sh-card__header">
            Lyrics@if($isRtl) / شعر@endif
        </div>
        <div class="sh-card__body">
            <div class="studio-lyrics {{ $isRtl ? 'studio-lyrics--rtl' : 'studio-lyrics--ltr' }}"
                 dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
                {!! nl2br(e($clip->lyrics_input)) !!}
            </div>
        </div>
    </div>
    @endif

    {{-- DANGER ZONE — always last --}}
    <div class="sh-card studio-danger-card">
        <div class="sh-card__header">Danger Zone</div>
        <div class="sh-card__body">
            <p class="studio-danger-warning">
                Deleting this clip is permanent. The audio file, cover image, and all associated
                data will be removed and cannot be recovered.
            </p>
            <form method="POST"
                  action="{{ route('studio.delete', $clip) }}"
                  onsubmit="return confirm('Delete this clip permanently? This cannot be undone.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="sh-btn sh-btn--danger">Delete Clip</button>
            </form>
        </div>
    </div>

    @endif{{-- end ready state --}}

</div>{{-- end studio-wrap --}}

<script>
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

// TODO: Cover polling currently uses session('success') string check which is fragile.
// Replace with a proper cover job status endpoint check in the next functional pass.
(function() {
    var hasCover  = {{ $coverAsset ? 'true' : 'false' }};
    var sessionMsg = '{{ addslashes(session("success") ?? "") }}';
    if (!hasCover && sessionMsg.indexOf('generated') !== -1) {
        var clipId = '{{ $clip->id }}';
        var csrf   = document.querySelector('meta[name=csrf-token]')
                   ? document.querySelector('meta[name=csrf-token]').content : '';
        var t = 0;
        function pollCover() {
            t++;
            if (t > 40) return;
            fetch('/studio/clip-status/' + clipId, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(function(r) { return r.json(); })
            .then(function(d) {
                if (d.cover_ready) { location.reload(); }
                else { setTimeout(pollCover, 4000); }
            })
            .catch(function() { setTimeout(pollCover, 6000); });
        }
        setTimeout(pollCover, 4000);
    }
})();
</script>
@endsection
