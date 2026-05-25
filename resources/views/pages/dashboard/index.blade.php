@extends('layouts.app')
@section('title', 'My Clips')
@section('content')
<div class="sh-page-wrap sh-page-wrap--wide">

    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;margin-bottom:2rem;">
        <div>
            <h1 class="sh-heading">My Clips</h1>
            <p class="sh-text-muted">{{ $clips->total() }} clip{{ $clips->total() !== 1 ? 's' : '' }} created</p>
        </div>
        <a href="{{ route('create') }}" class="sh-btn sh-btn--primary">+ Create New</a>
    </div>

    @if(session('success'))
        <div class="sh-notice sh-notice--success">{{ session('success') }}</div>
    @endif

    @if($clips->isEmpty())
        <div class="sh-card">
            <div class="sh-card__body" style="text-align:center;padding:4rem 2rem;">
                <div style="font-size:3rem;margin-bottom:1rem;">🎵</div>
                <p class="sh-heading--sm" style="margin-bottom:0.5rem;">No clips yet</p>
                <p class="sh-text-muted" style="margin-bottom:1.5rem;">Create your first song from poetry or lyrics.</p>
                <a href="{{ route('create') }}" class="sh-btn sh-btn--primary">Create Your First Song</a>
            </div>
        </div>
    @else
        <div class="dashboard-grid">
            @foreach($clips as $clip)
            @php
                $cover = $clip->mediaAssets()->where('type','cover_image')->where('is_primary',true)->first();
                $audio = $clip->mediaAssets()->whereIn('type',['song_audio','bed_audio'])->where('is_primary',true)->first();
                $type  = $audio?->type === 'bed_audio' ? 'Bed Music' : ($audio ? 'Song' : '');
            @endphp
            <div class="sh-card dashboard-clip">

                {{-- Cover --}}
                <a href="{{ route('studio.show', $clip) }}" class="dashboard-clip__cover-link">
                    @if($cover)
                        <img src="{{ $cover->cdn_url }}" alt="{{ $clip->title }}" class="dashboard-clip__cover-img">
                    @else
                        <div class="dashboard-clip__cover-placeholder">
                            <span style="font-size:2.5rem;">🎵</span>
                        </div>
                    @endif
                    {{-- Status overlay for processing/failed --}}
                    @if($clip->status === 'processing')
                        <div class="dashboard-clip__status-overlay">
                            <div class="sh-waveform">
                                @for($i=0;$i<5;$i++)<div class="sh-waveform__bar" style="animation-delay:{{ $i*0.15 }}s;height:{{ rand(30,90) }}%"></div>@endfor
                            </div>
                            <p style="font-size:0.75rem;color:#fff;margin-top:0.5rem;">Generating...</p>
                        </div>
                    @elseif($clip->status === 'failed')
                        <div class="dashboard-clip__status-overlay dashboard-clip__status-overlay--failed">
                            <p style="font-size:0.85rem;color:var(--sh-danger);">Generation failed</p>
                        </div>
                    @endif
                </a>

                {{-- Info --}}
                <div class="sh-card__body" style="padding:0.875rem;">
                    <div style="font-size:0.9375rem;font-weight:600;margin-bottom:0.4rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        {{ $clip->title ?: 'Untitled' }}
                    </div>
                    <div style="display:flex;gap:0.4rem;flex-wrap:wrap;margin-bottom:0.5rem;">
                        <span class="sh-badge sh-badge--lang">{{ strtoupper($clip->language) }}</span>
                        @if($type)<span class="sh-badge">{{ $type }}</span>@endif
                        @if($clip->status === 'ready')<span class="sh-badge sh-badge--status-ready">Ready</span>@endif
                        @if($clip->status === 'processing')<span class="sh-badge sh-badge--status-processing">Processing</span>@endif
                        @if($clip->status === 'failed')<span class="sh-badge sh-badge--status-failed">Failed</span>@endif
                        @if($clip->visibility === 'public')<span class="sh-badge">Public</span>@endif
                    </div>
                    <p class="sh-text-muted" style="font-size:0.78rem;margin-bottom:0.75rem;">{{ $clip->created_at->diffForHumans() }}</p>
                    <div style="display:flex;gap:0.5rem;">
                        <a href="{{ route('studio.show', $clip) }}" class="sh-btn sh-btn--ghost sh-btn--sm" style="flex:1;justify-content:center;">
                            Open Studio
                        </a>
                        @if($clip->visibility === 'public' && $clip->status === 'ready')
                            <a href="{{ route('player.show', $clip->slug) }}" class="sh-btn sh-btn--ghost sh-btn--sm" target="_blank">
                                ▶
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div style="margin-top:1.5rem;">{{ $clips->links() }}</div>
    @endif

</div>
@endsection
