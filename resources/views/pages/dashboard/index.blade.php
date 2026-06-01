@extends('layouts.app')
@section('title', 'My Clips')
@section('content')
<div class="sh-page-wrap sh-page-wrap--wide">

    <div class="dashboard-header">
        <div class="dashboard-header__text">
            <h1 class="sh-heading">My Clips</h1>
            <p class="sh-text-muted">{{ $clips->total() }} clip{{ $clips->total() !== 1 ? 's' : '' }}</p>
        </div>
        <a href="{{ route('create') }}" class="sh-btn sh-btn--primary">+ Create New</a>
    </div>

    @if(session('success'))
        <div class="sh-notice sh-notice--success dashboard-notice">{{ session('success') }}</div>
    @endif

    {{-- Filter tabs --}}
    <div class="dashboard-filters">
        <a href="{{ route('dashboard', ['filter' => 'all']) }}" class="dashboard-filters__tab {{ $filter === 'all' ? 'dashboard-filters__tab--active' : '' }}">All</a>
        <a href="{{ route('dashboard', ['filter' => 'ready']) }}" class="dashboard-filters__tab {{ $filter === 'ready' ? 'dashboard-filters__tab--active' : '' }}">Ready</a>
        <a href="{{ route('dashboard', ['filter' => 'failed']) }}" class="dashboard-filters__tab {{ $filter === 'failed' ? 'dashboard-filters__tab--active' : '' }}">Failed</a>
    </div>

    @if($clips->isEmpty())
        <div class="sh-card">
            <div class="sh-card__body dashboard-empty">
                <svg class="dashboard-empty__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>
                <p class="sh-heading--sm dashboard-empty__title">
                    @if($filter === 'ready') No ready clips yet
                    @elseif($filter === 'failed') No failed clips
                    @else No clips yet
                    @endif
                </p>
                <p class="sh-text-muted dashboard-empty__sub">
                    @if($filter === 'ready') Your finished songs will appear here.
                    @elseif($filter === 'failed') Great — nothing has gone wrong.
                    @else Create your first song from poetry or lyrics.
                    @endif
                </p>
                @if($filter === 'all')
                    <a href="{{ route('create') }}" class="sh-btn sh-btn--primary">Create Your First Song</a>
                @endif
            </div>
        </div>
    @else
        <div class="dashboard-grid">
            @foreach($clips as $clip)
            @php
                $assets    = $clip->mediaAssets->keyBy('type');
                $cover     = $assets->get('cover_image');
                $audio     = $assets->get('song_audio') ?? $assets->get('bed_audio') ?? $assets->get('uploaded_audio');
                $reel      = $assets->get('reel_video');
                $langLabel = $langNames[$clip->language] ?? strtoupper($clip->language);
                $typeLabel = 'Song';
                if ($audio?->type === 'bed_audio')          $typeLabel = 'Bed Music';
                elseif ($audio?->type === 'uploaded_audio') $typeLabel = 'Uploaded';
            @endphp
            <div class="sh-card dashboard-clip">

                {{-- Cover preview --}}
                <a href="{{ route('studio.show', $clip) }}" class="dashboard-clip__cover-link">
                    @if($cover && $cover->cdn_url)
                        <img src="{{ $cover->cdn_url }}" alt="{{ $clip->display_title }}" class="dashboard-clip__cover-img">
                    @else
                        <div class="dashboard-clip__cover-placeholder">
                            <svg class="dashboard-clip__cover-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>
                        </div>
                    @endif
                    @if($clip->status === 'processing')
                        <div class="dashboard-clip__overlay">
                            <div class="sh-waveform">
                                @for($i=0;$i<5;$i++)<div class="sh-waveform__bar" style="animation-delay:{{ $i*0.15 }}s;height:{{ rand(30,90) }}%"></div>@endfor
                            </div>
                            <span class="dashboard-clip__overlay-label">Generating...</span>
                        </div>
                    @elseif($clip->status === 'failed')
                        <div class="dashboard-clip__overlay dashboard-clip__overlay--failed">
                            <span class="dashboard-clip__overlay-label">Failed</span>
                        </div>
                    @endif
                </a>

                <div class="dashboard-clip__body">

                    {{-- Title --}}
                    <p class="dashboard-clip__title">{{ $clip->display_title }}</p>

                    {{-- Badges --}}
                    <div class="dashboard-clip__badges">
                        <span class="sh-badge sh-badge--lang">{{ $langLabel }}</span>
                        <span class="sh-badge">{{ $typeLabel }}</span>
                        @if($clip->status === 'ready')
                            <span class="sh-badge sh-badge--ready">Ready</span>
                        @elseif($clip->status === 'processing')
                            <span class="sh-badge sh-badge--processing">Processing</span>
                        @elseif($clip->status === 'failed')
                            <span class="sh-badge sh-badge--failed">Failed</span>
                        @endif
                        <span class="sh-badge dashboard-clip__vis-badge dashboard-clip__vis-badge--{{ $clip->visibility }}">{{ ucfirst($clip->visibility) }}</span>
                    </div>

                    {{-- Asset chips --}}
                    <div class="dashboard-clip__chips">
                        <span class="dashboard-chip {{ $audio ? 'dashboard-chip--on' : '' }}">Audio</span>
                        <span class="dashboard-chip {{ $cover ? 'dashboard-chip--on' : '' }}">Cover</span>
                        <span class="dashboard-chip {{ $reel  ? 'dashboard-chip--on' : '' }}">Reel</span>
                    </div>

                    {{-- Actions --}}
                    <div class="dashboard-clip__actions">
                        <a href="{{ route('studio.show', $clip) }}" class="sh-btn sh-btn--ghost sh-btn--sm dashboard-clip__main-action">Open Studio</a>
                        <div class="dashboard-clip__icon-actions">
                            @if($clip->visibility === 'public' && $clip->status === 'ready' && $clip->slug)
                                <a href="{{ route('player.show', $clip->slug) }}" class="sh-btn sh-btn--ghost sh-btn--sm dashboard-clip__icon-btn" target="_blank" title="Play">
                                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>
                                </a>
                            @endif
                            @if($audio && $audio->cdn_url)
                                <a href="{{ $audio->cdn_url }}" download class="sh-btn sh-btn--ghost sh-btn--sm dashboard-clip__icon-btn" title="Download MP3">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 15V3m0 12l-4-4m4 4l4-4M2 17l.621 2.485A2 2 0 004.561 21h14.878a2 2 0 001.94-1.515L22 17"/></svg>
                                    <span class="dashboard-clip__icon-label">MP3</span>
                                </a>
                            @endif
                            @if($reel && $reel->cdn_url)
                                <a href="{{ $reel->cdn_url }}" download class="sh-btn sh-btn--ghost sh-btn--sm dashboard-clip__icon-btn dashboard-clip__icon-btn--reel" title="Download Reel">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="2"/><path d="M10 8l6 4-6 4V8z"/></svg>
                                    <span class="dashboard-clip__icon-label">Reel</span>
                                </a>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
            @endforeach
        </div>
        <div class="dashboard-pagination">{{ $clips->links() }}</div>
    @endif

</div>
@endsection
