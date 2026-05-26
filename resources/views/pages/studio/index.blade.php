@extends('layouts.app')

@section('title', $clip->title . ' — Clip Studio')

@section('content')
<div class="sh-page-wrap sh-page-wrap--wide">

    {{-- Header --}}
    <div class="studio-page__header">
        <a href="{{ route('dashboard') }}" class="sh-btn sh-btn--ghost sh-btn--sm">← My Clips</a>
        <h1 class="sh-heading" style="margin-top:0.75rem;">{{ $clip->title }}</h1>
        <div style="display:flex;gap:0.5rem;margin-top:0.5rem;flex-wrap:wrap;">
            <span class="sh-badge sh-badge--lang">{{ strtoupper($clip->language) }}</span>
            @if($clip->script_direction === 'rtl')<span class="sh-badge">RTL</span>@endif
            <span class="sh-badge sh-badge--status">{{ ucfirst($clip->status) }}</span>
        </div>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="sh-notice sh-notice--success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="sh-notice sh-notice--danger">{{ session('error') }}</div>
    @endif

    {{-- PROCESSING --}}
    @if($clip->status === 'processing')
        <div class="sh-card">
            <div class="sh-card__body studio-processing">
                <p class="sh-heading">Generating your song...</p>
                <p class="sh-text-muted">This usually takes 30–180 seconds. Page will refresh automatically.</p>
                <div class="studio-progress" style="margin-top:1.5rem;"><div class="studio-progress__bar studio-progress__bar--pulse" id="progress-bar"></div></div>
                
            </div>
        </div>
        <script>
        var fakeInt = null; // no fake progress
        (function(){
            var jobId = '{{ $latestJob ? $latestJob->id : '' }}';
            var csrfToken = document.querySelector('meta[name=csrf-token]') ? document.querySelector('meta[name=csrf-token]').content : '';
            if (!jobId) { setTimeout(function(){ location.reload(); }, 5000); return; }
            var tries = 0;
            function poll(){
                tries++;
                if(tries > 80){ location.reload(); return; }
                fetch('/studio/job-status/' + jobId, {headers:{'Accept':'application/json','X-CSRF-TOKEN':csrfToken,'X-Requested-With':'XMLHttpRequest'}})
                .then(function(r){ return r.json(); })
                .then(function(d){
                    if(d.status === 'done' || d.status === 'failed'){ setTimeout(function(){location.reload();},500); }
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
                <a href="{{ route('create') }}" class="sh-btn sh-btn--primary">Try Again</a>
            </div>
        </div>

    {{-- READY --}}
    {{-- READY --}}
    @else
        {{-- AUDIO PLAYER --}}
        @if($audioAsset && $audioAsset->cdn_url)
        <div class="sh-card" style="margin-bottom:1rem;">
            <div class="sh-card__header">
                Audio
                <span class="sh-badge">
                    @if($audioAsset->type === 'bed_audio') Bed Music
                    @elseif($audioAsset->type === 'uploaded_audio') Uploaded
                    @else Song @endif
                </span>
            </div>
            <div class="sh-card__body">
                <audio controls class="studio-player__audio">
                    <source src="{{ $audioAsset->cdn_url }}"
                            type="{{ $audioAsset->mime_type ?? 'audio/mpeg' }}">
                </audio>
            </div>
        </div>
        @else
        <div class="sh-notice sh-notice--info" style="margin-bottom:1rem;">Audio is being processed.</div>
        @endif

        {{-- COVER SECTION --}}
        <div class="sh-card studio-cover-card" style="margin-bottom:1rem;">
            <div class="sh-card__header">Cover Image</div>
            <div class="sh-card__body">
                <div class="studio-cover-wrap">
                    <div class="studio-cover-preview">
                        @if($coverAsset && $coverAsset->cdn_url)
                            <img src="{{ $coverAsset->cdn_url }}"
                                 alt="Cover for {{ $clip->title }}"
                                 class="studio-cover__img">
                        @else
                            <div class="studio-cover__placeholder">
                                <div class="studio-cover__placeholder-icon">&#9834;</div>
                                <p class="studio-cover__placeholder-label">No cover yet</p>
                            </div>
                        @endif
                    </div>
                    <div class="studio-cover-actions">
                        <form method="POST" action="{{ route('studio.cover', $clip) }}">
                            @csrf
                            <input type="text" name="description" class="sh-input sh-input--sm"
                                   placeholder="Describe the cover (optional)"
                                   style="margin-bottom:0.5rem;">
                            <button type="submit" class="sh-btn sh-btn--primary sh-btn--sm sh-btn--full">
                                {{ $coverAsset ? '&#8635; Regenerate Cover' : '&#10024; Generate AI Cover' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- LYRICS --}}
        @if($clip->lyrics_input && (!$audioAsset || $audioAsset->type !== 'bed_audio'))
        <div class="sh-card" style="margin-bottom:1rem;">
            <div class="sh-card__header">Lyrics</div>
            <div class="sh-card__body">
                <div class="studio-lyrics {{ $clip->script_direction === 'rtl' ? 'studio-lyrics--rtl' : 'studio-lyrics--ltr' }}"
                     dir="{{ $clip->script_direction ?? 'ltr' }}">
                    {!! nl2br(e($clip->lyrics_input)) !!}
                </div>
            </div>
        </div>
        @endif

        {{-- MANAGE --}}
        <div class="sh-card" style="margin-bottom:1rem;">
            <div class="sh-card__header">Manage</div>
            <div class="sh-card__body">
                {{-- Rename --}}
                <div class="studio-action-row">
                    <form method="POST" action="{{ route('studio.rename', $clip) }}" class="studio-rename-form">
                        @csrf
                        @method('PATCH')
                        <input type="text" name="title" class="sh-input sh-input--sm"
                               value="{{ $clip->title }}" placeholder="Clip title">
                        <button type="submit" class="sh-btn sh-btn--ghost sh-btn--sm">Rename</button>
                    </form>
                </div>
                {{-- Download --}}
                @if($audioAsset && $audioAsset->cdn_url)
                <div class="studio-action-row">
                    <span class="studio-action-label">Download</span>
                    <a href="{{ $audioAsset->cdn_url }}" download
                       class="sh-btn sh-btn--ghost sh-btn--sm">&#8595; Download Audio</a>
                </div>
                @endif
                {{-- Visibility --}}
                <div class="studio-action-row">
                    <span class="studio-action-label">Visibility</span>
                    <form method="POST" action="{{ route('studio.visibility', $clip) }}">
                        @csrf
                        @method('PATCH')
                        <select name="visibility" class="sh-select sh-select--sm" onchange="this.form.submit()">
                            <option value="private" {{ $clip->visibility === 'private' ? 'selected' : '' }}>Private</option>
                            <option value="public" {{ $clip->visibility === 'public' ? 'selected' : '' }}>Public</option>
                        </select>
                    </form>
                </div>
                {{-- Share --}}
                @if($clip->visibility === 'public')
                <div class="studio-action-row">
                    <span class="studio-action-label">Share</span>
                    <button type="button" class="sh-btn sh-btn--ghost sh-btn--sm"
                            onclick="studioShareLink(this)"
                            data-url="{{ route('player.show', $clip) }}">
                        Copy Link
                    </button>
                </div>
                @endif
                {{-- Reel --}}
                <div class="studio-action-row">
                    <span class="studio-action-label">Reel</span>
                    @if($reel && $reel->cdn_url)
                        <a href="{{ $reel->cdn_url }}" download class="sh-btn sh-btn--ghost sh-btn--sm">&#8595; Download Reel</a>
                    @else
                        <form method="POST" action="{{ route('studio.reel', $clip) }}">
                            @csrf
                            <button type="submit" class="sh-btn sh-btn--ghost sh-btn--sm">Create Reel</button>
                        </form>
                    @endif
                </div>
                {{-- Delete --}}
                <div class="studio-action-row studio-action-row--danger">
                    <span class="studio-action-label">Delete clip</span>
                    <form method="POST" action="{{ route('studio.delete', $clip) }}"
                          onsubmit="return confirm('Delete this clip permanently? This cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="sh-btn sh-btn--danger sh-btn--sm">Delete</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- DETAILS --}}
        <div class="sh-card" style="margin-bottom:1rem;">
            <div class="sh-card__header">Details</div>
            <div class="sh-card__body">
                <div class="studio-details__row">
                    <span class="sh-label">Language</span>
                    <span class="sh-badge sh-badge--lang">{{ strtoupper($clip->language) }}</span>
                </div>
                <div class="studio-details__row">
                    <span class="sh-label">Visibility</span>
                    <span class="sh-badge">{{ ucfirst($clip->visibility) }}</span>
                </div>
                @if($latestJob && $latestJob->credits_charged !== null)
                <div class="studio-details__row">
                    <span class="sh-label">Credits used</span>
                    <span>{{ $latestJob->credits_charged }}</span>
                </div>
                @endif
                <div class="studio-details__row">
                    <span class="sh-label">Created</span>
                    <span>{{ $clip->created_at->diffForHumans() }}</span>
                </div>
            </div>
        </div>

    @endif{{-- end ready state --}}

</div>{{-- end sh-page-wrap --}}

<script>
// Cover polling - runs when cover is being generated
(function(){
    var hasCover = {{ $coverAsset ? 'true' : 'false' }};
    var sessionMsg = '{{ session("success") }}';
    if (!hasCover && sessionMsg.indexOf('generated') !== -1) {
        var clipId = '{{ $clip->id }}';
        var csrfToken = document.querySelector('meta[name=csrf-token]') ? document.querySelector('meta[name=csrf-token]').content : '';
        var coverTries = 0;
        function pollCover(){
            coverTries++;
            if(coverTries > 40) return;
            fetch('/studio/clip-status/' + clipId, {
                headers:{'Accept':'application/json','X-CSRF-TOKEN':csrfToken,'X-Requested-With':'XMLHttpRequest'}
            })
            .then(function(r){ return r.json(); })
            .then(function(d){
                if(d.cover_ready){ location.reload(); }
                else { setTimeout(pollCover, 4000); }
            })
            .catch(function(){ setTimeout(pollCover, 6000); });
        }
        setTimeout(pollCover, 4000);
    }
})();

function studioShareLink(btn) {
    var url = btn.getAttribute('data-url');
    if (navigator.clipboard) {
        navigator.clipboard.writeText(url).then(function(){
            btn.textContent = 'Copied!';
            setTimeout(function(){ btn.textContent = 'Copy Share Link'; }, 2000);
        });
    } else {
        window.prompt('Copy this link:', url);
    }
}
</script>
@endsection
