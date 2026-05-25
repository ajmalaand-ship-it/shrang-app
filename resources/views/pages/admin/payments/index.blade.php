@extends('layouts.admin')
@section('title', 'Payments')
@section('content')

<div class="sh-card">
    <div class="sh-card__header">
        All Payments
        <span class="sh-badge">{{ $payments->total() }}</span>
    </div>
    <div class="sh-card__body" style="padding:0;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>User</th>
                    <th>Package</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Stripe ID</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                <tr>
                    <td>{{ $payment->created_at->format('d M Y H:i') }}</td>
                    <td>
                        <a href="{{ route('admin.users.show', $payment->user) }}" style="color:var(--sh-orange);">
                            {{ $payment->user->name }}
                        </a>
                        <div style="font-size:0.75rem;color:var(--sh-text-muted);">{{ $payment->user->email }}</div>
                    </td>
                    <td>{{ $payment->creditPackage->name ?? '—' }}</td>
                    <td>${{ number_format($payment->amount_cents / 100, 2) }} {{ $payment->currency }}</td>
                    <td>
                        <span class="sh-badge sh-badge--status-{{ $payment->status === 'paid' ? 'ready' : ($payment->status === 'failed' ? 'failed' : 'processing') }}">
                            {{ ucfirst($payment->status) }}
                        </span>
                    </td>
                    <td style="font-size:0.75rem;color:var(--sh-text-muted);">{{ Str::limit($payment->stripe_payment_intent_id, 20) }}</td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center;color:var(--sh-text-muted);padding:2rem;">No payments yet.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div style="padding:1rem;">{{ $payments->links() }}</div>
    </div>
</div>

@endsection
