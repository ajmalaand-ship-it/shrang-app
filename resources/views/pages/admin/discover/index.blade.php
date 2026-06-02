@extends('layouts.admin')
@section('title', 'Discover Management')
@section('content')

<div class="sh-grid" style="grid-template-columns:1fr 340px;gap:1.5rem;align-items:start;">

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
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($featured as $feature)
                    @php $clip = $feature->clip; @endphp
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:0.75rem;">
                                @php $cover = $clip->mediaAssets->first(); @endphp
                                @if($cover && $cover->cdn_url)
                                    <img src="{{ $cover->cdn_url }}" alt="{{ $clip->title }}" style="width:48px;height:48px;object-fit:cover;border-radius:0.375rem;flex-shrink:0;">
                                @else
                                    <div style="width:48px;height:48px;background:#1c1208;border-radius:0.375rem;flex-shrink:0;display:flex;align-items:center;justify-content:center;">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="var(--sh-orange)" stroke-width="1.5" width="20" height="20"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>
                                    </div>
                                @endif
                                <div style="min-width:0;">
                                    <div style="font-weight:500;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:220px;">{{ $clip->display_title }}</div>
                                    <div style="font-size:0.75rem;color:var(--sh-text-muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:220px;">{{ $clip->slug }}</div>
                                </div>
                            </div>
                        </td>
                        <td><span class="sh-badge sh-badge--lang">{{ strtoupper($clip->language) }}</span></td>
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
                    <tr><td colspan="3" style="text-align:center;color:var(--sh-text-muted);padding:2rem;">No featured clips yet.</td></tr>
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
            <div style="display:flex;align-items:center;justify-content:space-between;gap:0.5rem;padding:0.75rem 0;border-bottom:1px solid var(--sh-border);">
                <div style="display:flex;align-items:center;gap:0.5rem;min-width:0;">
                    @php $availCover = $clip->mediaAssets->first(); @endphp
                    @if($availCover && $availCover->cdn_url)
                        <img src="{{ $availCover->cdn_url }}" style="width:40px;height:40px;object-fit:cover;border-radius:0.25rem;flex-shrink:0;">
                    @else
                        <div style="width:40px;height:40px;background:#1c1208;border-radius:0.25rem;flex-shrink:0;display:flex;align-items:center;justify-content:center;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="var(--sh-orange)" stroke-width="1.5" width="16" height="16"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>
                        </div>
                    @endif
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:0.875rem;font-weight:500;">{{ $clip->display_title }}</div>
                    </div>
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
