@extends('layouts.admin')
@section('title', 'Discover Management')
@section('content')

<div class="sh-grid" style="grid-template-columns:1fr 400px;gap:1.5rem;align-items:start;">

    {{-- Featured clips --}}
    <div class="sh-card">
        <div class="sh-card__header">
            Featured on Discover
            <span class="sh-badge">{{ $featured->total() }}</span>
        </div>
        <div class="sh-card__body" style="padding:0;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Clip</th>
                        <th>Language</th>
                        <th>Stats</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($featured as $feature)
                    @php $clip = $feature->clip; @endphp
                    <tr>
                        <td>
                            <div style="font-weight:500;">{{ $clip->title }}</div>
                            <div style="font-size:0.75rem;color:var(--sh-text-muted);">{{ $clip->slug }}</div>
                        </td>
                        <td><span class="sh-badge sh-badge--lang">{{ strtoupper($clip->language) }}</span></td>
                        <td style="font-size:0.8rem;color:var(--sh-text-muted);">
                            ▶ {{ number_format($clip->stat?->play_count ?? 0) }}
                            ♥ {{ number_format($clip->stat?->like_count ?? 0) }}
                            ↓ {{ number_format($clip->stat?->download_count ?? 0) }}
                        </td>
                        <td>
                            @if($feature->is_pinned)<span class="sh-badge" style="color:var(--sh-gold);">📌 Pinned</span>@endif
                            @if($feature->is_blocked)<span class="sh-badge sh-badge--status-failed">Blocked</span>@endif
                            @if(!$feature->is_pinned && !$feature->is_blocked)<span class="sh-badge sh-badge--status-ready">Live</span>@endif
                        </td>
                        <td>
                            <div style="display:flex;gap:0.5rem;flex-wrap:wrap;">
                                <form method="POST" action="{{ route('admin.discover.pin', $clip) }}" style="display:inline;">
                                    @csrf @method('PATCH')
                                    <button class="sh-btn sh-btn--sm sh-btn--ghost">{{ $feature->is_pinned ? 'Unpin' : 'Pin' }}</button>
                                </form>
                                <form method="POST" action="{{ route('admin.discover.block', $clip) }}" style="display:inline;">
                                    @csrf @method('PATCH')
                                    <button class="sh-btn sh-btn--sm sh-btn--ghost">{{ $feature->is_blocked ? 'Unblock' : 'Block' }}</button>
                                </form>
                                <form method="POST" action="{{ route('admin.discover.unfeature', $clip) }}" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button class="sh-btn sh-btn--sm sh-btn--danger" onclick="return confirm('Remove from Discover?')">Remove</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" style="text-align:center;color:var(--sh-text-muted);padding:2rem;">No featured clips yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div style="padding:1rem;">{{ $featured->links() }}</div>
        </div>
    </div>

    {{-- Add clips --}}
    <div class="sh-card">
        <div class="sh-card__header">Add Public Clip to Discover</div>
        <div class="sh-card__body">
            <form method="GET" action="{{ route('admin.discover.index') }}" style="margin-bottom:1rem;">
                <div style="display:flex;gap:0.5rem;">
                    <input type="text" name="search" class="sh-input" placeholder="Search by title" value="{{ request('search') }}">
                    <button type="submit" class="sh-btn sh-btn--ghost">Search</button>
                </div>
            </form>
            @forelse($available as $clip)
            <div style="display:flex;align-items:center;justify-content:space-between;padding:0.75rem 0;border-bottom:1px solid var(--sh-border);">
                <div>
                    <div style="font-size:0.875rem;font-weight:500;">{{ $clip->title }}</div>
                    <div style="font-size:0.75rem;color:var(--sh-text-muted);">{{ strtoupper($clip->language) }} · {{ $clip->slug }}</div>
                </div>
                <form method="POST" action="{{ route('admin.discover.feature', $clip) }}">
                    @csrf
                    <button class="sh-btn sh-btn--sm sh-btn--primary">+ Feature</button>
                </form>
            </div>
            @empty
            <p class="sh-text-muted">No public clips available to feature.</p>
            @endforelse
            <div style="margin-top:1rem;">{{ $available->links() }}</div>
        </div>
    </div>

</div>
@endsection
