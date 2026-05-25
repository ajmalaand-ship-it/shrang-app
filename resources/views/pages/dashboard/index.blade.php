@extends('layouts.app')
@section('title', 'My Clips')
@section('content')
<div class="sh-page-wrap">
    <div class="sh-section">
        <h1 class="sh-heading">My Clips</h1>
        <p class="sh-text-muted">All your generated songs and audio</p>
    </div>
    <div style="text-align:right; margin-bottom:1.5rem;">
        <a href="{{ route('create') }}" class="sh-btn sh-btn--primary">+ Create New</a>
    </div>
    @if ($clips->isEmpty())
        <div class="sh-card">
            <div class="sh-card__body" style="text-align:center; padding:3rem;">
                <p class="sh-text-muted" style="margin-bottom:1.5rem;">You have not created any clips yet.</p>
                <a href="{{ route('create') }}" class="sh-btn sh-btn--primary">Create Your First Song</a>
            </div>
        </div>
    @else
        <div class="sh-stack">
            @foreach ($clips as $clip)
                <div class="sh-card">
                    <div class="sh-card__body" style="display:flex; align-items:center; justify-content:space-between; gap:1rem; flex-wrap:wrap;">
                        <div>
                            <div style="display:flex; align-items:center; gap:0.75rem; margin-bottom:0.5rem;">
                                <span style="font-weight:600; font-size:1rem;">{{ $clip->title ?: 'Untitled' }}</span>
                                <span class="sh-badge sh-badge--lang">{{ strtoupper($clip->language) }}</span>
                                @if ($clip->status === 'ready')
                                    <span class="sh-badge sh-badge--status-ready">Ready</span>
                                @elseif ($clip->status === 'processing')
                                    <span class="sh-badge sh-badge--status-processing">Processing</span>
                                @elseif ($clip->status === 'failed')
                                    <span class="sh-badge sh-badge--status-failed">Failed</span>
                                @endif
                            </div>
                            <p class="sh-text-muted" style="font-size:0.8rem;">{{ $clip->created_at->diffForHumans() }}</p>
                        </div>
                        <a href="{{ route('studio.show', $clip) }}" class="sh-btn sh-btn--ghost sh-btn--sm">Open Studio</a>
                    </div>
                </div>
            @endforeach
        </div>
        <div style="margin-top:2rem;">{{ $clips->links() }}</div>
    @endif
</div>
@endsection
