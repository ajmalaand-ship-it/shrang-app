@extends('layouts.app')
@section('title', 'Buy Credits')
@section('head_extra')
@endsection
@section('content')
<div class="sh-page-wrap sh-page-wrap--narrow">

    <div style="margin-bottom:2rem;">
        <h1 class="sh-heading">Credits</h1>
        <p class="sh-text-muted">Use credits to create songs, bed music, covers, and reels.</p>
    </div>

    @if(session('success') || request('success'))
        <div class="sh-notice sh-notice--success">Payment successful! Your credits have been added.</div>
    @endif
    @if(request('cancelled'))
        <div class="sh-notice sh-notice--warning">Payment cancelled. No charges were made.</div>
    @endif

    {{-- Current balance --}}
    <div class="sh-card" style="margin-bottom:2rem;">
        <div class="sh-card__body" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
            <div>
                <div class="sh-label">Your current balance</div>
                <div style="font-size:2.5rem;font-weight:700;color:var(--sh-orange);">{{ number_format($balance) }}</div>
                <div class="sh-text-muted" style="font-size:0.85rem;">credits available</div>
            </div>
            <div style="font-size:0.85rem;color:var(--sh-text-muted);line-height:2;">
                <div>Song — 10 credits</div>
                <div>Bed Music — 5 credits</div>
                <div>Cover Image — 3 credits</div>
                <div>Reel — 5 credits</div>
            </div>
        </div>
    </div>

    {{-- Packages --}}
    <h2 class="sh-heading--sm" style="margin-bottom:1rem;">Buy Credits</h2>
    <div class="credits-packages" style="margin-bottom:2rem;">
        @foreach($packages as $package)
        <div class="sh-card credits-package">
            <div class="sh-card__body" style="text-align:center;">
                <div class="credits-package__name">{{ $package->name }}</div>
                <div class="credits-package__credits">
                    {{ number_format($package->credits) }}
                    <span class="sh-text-muted" style="font-size:1rem;font-weight:400;"> credits</span>
                </div>
                <div class="credits-package__price">${{ number_format($package->price_cents / 100, 2) }}</div>
                <div class="sh-text-muted" style="font-size:0.8rem;margin-bottom:1.25rem;">
                    ~{{ floor($package->credits / 10) }} songs or {{ floor($package->credits / 5) }} bed tracks
                </div>
                <form method="POST" action="{{ route('credits.buy') }}">
                    @csrf
                    <input type="hidden" name="package_id" value="{{ $package->id }}">
                    <button type="submit" class="sh-btn sh-btn--primary sh-btn--full">
                        Buy {{ $package->name }}
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Credit history --}}
    <h2 class="sh-heading--sm" style="margin-bottom:1rem;">Credit History</h2>
    <div class="sh-card">
        <div class="sh-card__body" style="padding:0;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Reason</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse(auth()->user()->creditTransactions()->latest()->take(20)->get() as $tx)
                    <tr>
                        <td>{{ $tx->created_at->format('d M Y') }}</td>
                        <td>
                            <span class="sh-badge {{ $tx->type === 'credit' ? 'sh-badge--status-ready' : 'sh-badge--status-failed' }}">
                                {{ ucfirst($tx->type) }}
                            </span>
                        </td>
                        <td style="font-weight:600;color:{{ $tx->type === 'credit' ? 'var(--sh-success)' : 'var(--sh-danger)' }};">
                            {{ $tx->type === 'credit' ? '+' : '-' }}{{ number_format($tx->amount) }}
                        </td>
                        <td class="sh-text-muted">{{ str_replace('_', ' ', $tx->reason) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align:center;color:var(--sh-text-muted);padding:2rem;">
                            No transactions yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
