@extends('layouts.public')
@section('title', 'Terms of Service — Shrang')
@section('content')
<div class="sh-page-wrap sh-page-wrap--narrow">
    <div style="margin-bottom:2rem;">
        <h1 class="sh-heading">Terms of Service</h1>
    </div>
    
<p class="sh-text-muted" style="margin-bottom:1.5rem;">Last updated: May 2026</p>

<div class="sh-stack">
<div class="sh-card"><div class="sh-card__body">
<h3 class="sh-heading--sm" style="margin-bottom:0.75rem;">1. Acceptance of Terms</h3>
<p class="sh-text-muted">By creating an account or using Shrang, you agree to these Terms of Service. If you do not agree, please do not use the platform.</p>
</div></div>

<div class="sh-card"><div class="sh-card__body">
<h3 class="sh-heading--sm" style="margin-bottom:0.75rem;">2. Use of the Service</h3>
<p class="sh-text-muted">You may use Shrang to create AI-generated music from your own original poetry and lyrics. You must not upload content that violates copyright, contains hate speech, or is otherwise unlawful.</p>
</div></div>

<div class="sh-card"><div class="sh-card__body">
<h3 class="sh-heading--sm" style="margin-bottom:0.75rem;">3. Credits and Payments</h3>
<p class="sh-text-muted">Credits are non-refundable except where required by law. Failed generations do not consume credits. Payments are processed securely by Stripe.</p>
</div></div>

<div class="sh-card"><div class="sh-card__body">
<h3 class="sh-heading--sm" style="margin-bottom:0.75rem;">4. Intellectual Property</h3>
<p class="sh-text-muted">You retain ownership of the lyrics and poetry you submit. The AI-generated music is licensed to you for personal, non-commercial use. Commercial licensing is available — contact us for details.</p>
</div></div>

<div class="sh-card"><div class="sh-card__body">
<h3 class="sh-heading--sm" style="margin-bottom:0.75rem;">5. Account Termination</h3>
<p class="sh-text-muted">We reserve the right to suspend or terminate accounts that violate these terms. You may delete your account at any time from your Account Settings page.</p>
</div></div>

<div class="sh-card"><div class="sh-card__body">
<h3 class="sh-heading--sm" style="margin-bottom:0.75rem;">6. Limitation of Liability</h3>
<p class="sh-text-muted">Shrang is provided as-is. We are not liable for any indirect, incidental, or consequential damages arising from use of the platform.</p>
</div></div>

<div class="sh-card"><div class="sh-card__body">
<h3 class="sh-heading--sm" style="margin-bottom:0.75rem;">7. Contact</h3>
<p class="sh-text-muted">For questions about these terms, contact us via the <a href="{{ route('support') }}" style="color:var(--sh-orange);">Support page</a>.</p>
</div></div>
</div>

</div>
@endsection
