@extends('layouts.admin')
@section('title', 'User: ' . $user->name)
@section('content')

<div style="margin-bottom:1rem;">
    <a href="{{ route('admin.users.index') }}" class="sh-btn sh-btn--ghost sh-btn--sm">← Back to Users</a>
</div>

<div class="sh-grid" style="grid-template-columns:1fr 1fr;gap:1.5rem;margin-bottom:1.5rem;">

    {{-- User info --}}
    <div class="sh-card">
        <div class="sh-card__header">Account Info</div>
        <div class="sh-card__body">
            <div class="admin-table-row">
                <span class="sh-label">Name</span>
                <span>{{ $user->name }}</span>
            </div>
            <div class="admin-table-row">
                <span class="sh-label">Email</span>
                <span>{{ $user->email }}</span>
            </div>
            <div class="admin-table-row">
                <span class="sh-label">Role</span>
                <span class="sh-badge">{{ ucfirst($user->role) }}</span>
            </div>
            <div class="admin-table-row">
                <span class="sh-label">Status</span>
                <span class="sh-badge {{ $user->is_active ? 'sh-badge--status-ready' : 'sh-badge--status-failed' }}">
                    {{ $user->is_active ? 'Active' : 'Banned' }}
                </span>
            </div>
            <div class="admin-table-row">
                <span class="sh-label">Language</span>
                <span class="sh-badge sh-badge--lang">{{ strtoupper($user->preferred_language) }}</span>
            </div>
            <div class="admin-table-row">
                <span class="sh-label">Joined</span>
                <span>{{ $user->created_at->format('d M Y') }}</span>
            </div>
            <div class="admin-table-row">
                <span class="sh-label">Credit Balance</span>
                <span style="font-weight:600;color:var(--sh-orange);">{{ number_format($user->credit_balance) }}</span>
            </div>
            <div class="admin-table-row">
                <span class="sh-label">Spendable Balance</span>
                <span>{{ number_format($balance) }}</span>
            </div>
        </div>
    </div>

    {{-- Actions --}}
    <div style="display:flex;flex-direction:column;gap:1rem;">

        {{-- Ban / Unban --}}
        <div class="sh-card">
            <div class="sh-card__header">{{ $user->is_active ? 'Ban User' : 'Unban User' }}</div>
            <div class="sh-card__body">
                <p class="sh-text-muted" style="margin-bottom:1rem;font-size:0.875rem;">
                    {{ $user->is_active ? 'Banning will block this user from logging in.' : 'Unbanning will restore access.' }}
                </p>
                <form method="POST" action="{{ route('admin.users.ban', $user) }}">
                    @csrf
                    <button type="submit" class="sh-btn {{ $user->is_active ? 'sh-btn--danger' : 'sh-btn--primary' }}">
                        {{ $user->is_active ? 'Ban This User' : 'Restore Access' }}
                    </button>
                </form>
            </div>
        </div>

        {{-- Adjust credits --}}
        <div class="sh-card">
            <div class="sh-card__header">Adjust Credits</div>
            <div class="sh-card__body">
                <form method="POST" action="{{ route('admin.users.credits', $user) }}">
                    @csrf
                    <div class="sh-field">
                        <label class="sh-label">Amount (use negative to deduct)</label>
                        <input type="number" name="amount" class="sh-input" placeholder="e.g. 100 or -50" required>
                    </div>
                    <div class="sh-field">
                        <label class="sh-label">Reason</label>
                        <input type="text" name="reason" class="sh-input" placeholder="e.g. Bonus credits" required>
                    </div>
                    <button type="submit" class="sh-btn sh-btn--primary">Apply Credit Adjustment</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- User clips --}}
<div class="sh-card">
    <div class="sh-card__header">
        Clips by {{ $user->name }}
        <span class="sh-badge">{{ $user->clips->count() }}</span>
    </div>
    <div class="sh-card__body" style="padding:0;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Language</th>
                    <th>Status</th>
                    <th>Visibility</th>
                    <th>Created</th>
                </tr>
            </thead>
            <tbody>
                @forelse($user->clips()->latest()->take(20)->get() as $clip)
                <tr>
                    <td>{{ $clip->title }}</td>
                    <td><span class="sh-badge sh-badge--lang">{{ strtoupper($clip->language) }}</span></td>
                    <td><span class="sh-badge sh-badge--status-{{ $clip->status }}">{{ ucfirst($clip->status) }}</span></td>
                    <td><span class="sh-badge">{{ ucfirst($clip->visibility) }}</span></td>
                    <td>{{ $clip->created_at->format('d M Y') }}</td>
                </tr>
                @empty
                <tr><td colspan="5" style="text-align:center;color:var(--sh-text-muted);">No clips yet</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
