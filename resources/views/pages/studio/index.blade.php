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
                <p class="sh-text-muted" id="progress-msg" style="font-size:0.8rem;margin-top:0.5rem;text-align:center;">Starting...</p>
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
    @else

        {{-- Main grid: cover left, player right --}}
        <div class="studio-page__grid">

            {{-- LEFT: Cover image --}}
            <div class="studio-cover">
                @if($coverAsset && $coverAsset->cdn_url)
                    <img src="{{ $coverAsset->cdn_url }}"
                         alt="Cover for {{ $clip->title }}"
                         class="studio-cover__img">
                @else
                    <div class="studio-cover__placeholder">
                        <div class="studio-cover__placeholder-icon">♪</div>
                        <p class="studio-cover__placeholder-label">No cover image yet</p>
                    </div>
                @endif

                {{-- Generate / Regenerate cover form --}}
                <form method="POST" action="{{ route('studio.cover', $clip) }}" class="studio-cover__form">
                    @csrf
                    <input type="text" name="description" class="sh-input"
                           placeholder="Describe the cover (optional)">
                    <button type="submit" class="sh-btn sh-btn--ghost sh-btn--sm">
                        {{ $coverAsset ? 'Regenerate Cover' : 'Generate Cover' }}
                    </button>
                </form>
            </div>

            {{-- RIGHT: Audio player + clip details --}}
            <div class="studio-panel">

                {{-- Audio player --}}
                @if($audioAsset && $audioAsset->cdn_url)
                    <div class="sh-card">
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
                            @if($audioAsset->duration_seconds)
                                <p class="studio-player__duration sh-text-muted">
                                    Duration: {{ gmdate('i:s', $audioAsset->duration_seconds) }}
                                </p>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="sh-notice sh-notice--info">Audio file is being processed.</div>
                @endif

                {{-- Clip details --}}
                <div class="sh-card">
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

            </div>{{-- end .studio-panel --}}
        </div>{{-- end .studio-page__grid --}}

        {{-- Lyrics --}}
        @if($clip->lyrics_input && (!$audioAsset || $audioAsset->type !== 'bed_audio'))
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

        {{-- Actions --}}
        <div class="sh-card studio-page__actions">
            <div class="sh-card__header">Actions</div>
            <div class="sh-card__body">

                {{-- Download --}}
                @if($audioAsset && $audioAsset->cdn_url)
                <div class="sh-field">
                    <label class="sh-label">Download</label>
                    <a href="{{ $audioAsset->cdn_url }}" download
                       class="sh-btn sh-btn--primary">↓ Download Audio</a>
                </div>
                @endif

                {{-- Visibility --}}
                <div class="sh-field">
                    <label class="sh-label">Visibility</label>
                    <form method="POST" action="{{ route('studio.visibility', $clip) }}">
                        @csrf
                        @method('PATCH')
                        <select name="visibility" class="sh-select" onchange="this.form.submit()">
                            <option value="private" {{ $clip->visibility === 'private' ? 'selected' : '' }}>Private</option>
                            <option value="public" {{ $clip->visibility === 'public' ? 'selected' : '' }}>Public</option>
                        </select>
                    </form>
                </div>

                {{-- Reel --}}
                <div class="sh-field">
                    <label class="sh-label">Reel</label>
                    @if($reel && $reel->cdn_url)
                        <a href="{{ $reel->cdn_url }}" download
                           class="sh-btn sh-btn--ghost">↓ Download Reel</a>
                    @else
                        <form method="POST" action="{{ route('studio.reel', $clip) }}">
                            @csrf
                            <button type="submit" class="sh-btn sh-btn--ghost">Create Reel</button>
                        </form>
                    @endif
                </div>

                {{-- Share --}}
                @if($clip->visibility === 'public')
                <div class="sh-field">
                    <label class="sh-label">Share</label>
                    <button type="button" class="sh-btn sh-btn--ghost"
                            onclick="studioShareLink(this)"
                            data-url="{{ route('player.show', $clip) }}">
                        Copy Share Link
                    </button>
                </div>
                @endif

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
