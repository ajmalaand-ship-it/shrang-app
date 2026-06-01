@extends('layouts.admin')
@section('title', 'Payment Detail')
@section('content')
<div class="sh-page-wrap" style="max-width:800px;">

    @if(session('success'))
        <div class="sh-notice sh-notice--success" style="margin-bottom:1.5rem;">{{ session('success') }}</div>
    @endif

    <div class="sh-card" style="margin-bottom:1.5rem;">
        <div class="sh-card__header">Payment Detail</div>
        <div class="sh-card__body">
            <table class="admin-table">
                <tr><td style="color:var(--sh-text-muted);width:180px;">Order ID</td><td>{{ $payment->id }}</td></tr>
                <tr><td style="color:var(--sh-text-muted);">User</td><td>{{ $payment->user->name }} — {{ $payment->user->email }}</td></tr>
                <tr><td style="color:var(--sh-text-muted);">Package</td><td>{{ $payment->creditPackage->name ?? '—' }}</td></tr>
                <tr><td style="color:var(--sh-text-muted);">Amount</td><td>${{ number_format($payment->amount_cents / 100, 2) }} {{ $payment->currency }}</td></tr>
                <tr><td style="color:var(--sh-text-muted);">Status</td><td><span class="sh-badge sh-badge--status-{{ $payment->status === 'paid' ? 'ready' : 'processing' }}">{{ ucfirst($payment->status) }}</span></td></tr>
                <tr><td style="color:var(--sh-text-muted);">Paid At</td><td>{{ $payment->paid_at ? $payment->paid_at->format('d M Y H:i') : '—' }}</td></tr>
                <tr><td style="color:var(--sh-text-muted);">Stripe ID</td><td style="font-size:0.8rem;">{{ $payment->stripe_payment_intent_id }}</td></tr>
                @if($payment->refund_status)
                <tr><td style="color:var(--sh-text-muted);">Refund Status</td><td><span class="sh-badge sh-badge--status-failed">{{ ucfirst(str_replace('_', ' ', $payment->refund_status)) }}</span></td></tr>
                <tr><td style="color:var(--sh-text-muted);">Refund Amount</td><td>{{ $payment->refund_amount_cents ? '$' . number_format($payment->refund_amount_cents / 100, 2) : '—' }}</td></tr>
                <tr><td style="color:var(--sh-text-muted);">Stripe Refund ID</td><td>{{ $payment->stripe_refund_id ?? '—' }}</td></tr>
                <tr><td style="color:var(--sh-text-muted);">Reason</td><td>{{ $payment->refund_reason }}</td></tr>
                <tr><td style="color:var(--sh-text-muted);">Refunded At</td><td>{{ $payment->refunded_at ? $payment->refunded_at->format('d M Y H:i') : '—' }}</td></tr>
                @endif
            </table>
        </div>
    </div>

    <div class="sh-card">
        <div class="sh-card__header">Record Refund / Correction</div>
        <div class="sh-card__body">
            @if($errors->any())
                <div class="sh-notice sh-notice--danger" style="margin-bottom:1rem;">
                    @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
                </div>
            @endif
            <form method="POST" action="{{ route('admin.payments.refund', $payment) }}">
                @csrf
                <div class="sh-field">
                    <label class="sh-label">Type</label>
                    <select name="refund_status" class="sh-input" required>
                        <option value="">— Select —</option>
                        <option value="refunded">Refunded</option>
                        <option value="partially_refunded">Partially Refunded</option>
                        <option value="disputed">Disputed</option>
                        <option value="manually_corrected">Manually Corrected</option>
                    </select>
                </div>
                <div class="sh-field">
                    <label class="sh-label">Refund Amount in cents (e.g. 299 = $2.99) — optional</label>
                    <input type="number" name="refund_amount_cents" class="sh-input" min="0" placeholder="Leave blank if not applicable">
                </div>
                <div class="sh-field">
                    <label class="sh-label">Credits Adjustment — optional (negative to remove, positive to add)</label>
                    <input type="number" name="credits_adjustment" class="sh-input" placeholder="e.g. -50 to remove 50 credits">
                </div>
                <div class="sh-field">
                    <label class="sh-label">Reason / Note <span style="color:var(--sh-orange);">*required</span></label>
                    <textarea name="refund_reason" class="sh-input" rows="3" required minlength="5" placeholder="Explain why this refund or correction is being made..."></textarea>
                </div>
                <button type="submit" class="sh-btn sh-btn--primary" onclick="return confirm('Record this refund/correction? This cannot be undone.')">Record Refund / Correction</button>
                <a href="{{ route('admin.payments.index') }}" class="sh-btn sh-btn--ghost" style="margin-left:0.5rem;">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection
