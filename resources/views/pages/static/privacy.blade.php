@extends('layouts.public')
@section('title', 'Privacy Policy — Shrang')
@section('content')
<div class="sh-page-wrap sh-page-wrap--narrow">
    <div style="margin-bottom:2rem;">
        <h1 class="sh-heading">Privacy Policy</h1>
    </div>
    
<p class="sh-text-muted" style="margin-bottom:1.5rem;">Last updated: May 2026</p>

<div class="sh-stack">
<div class="sh-card"><div class="sh-card__body">
<h3 class="sh-heading--sm" style="margin-bottom:0.75rem;">What We Collect</h3>
<p class="sh-text-muted">We collect your name, email address, and the lyrics/poetry you submit for music generation. We also collect usage data to improve the service.</p>
</div></div>

<div class="sh-card"><div class="sh-card__body">
<h3 class="sh-heading--sm" style="margin-bottom:0.75rem;">How We Use Your Data</h3>
<p class="sh-text-muted">Your data is used to provide the Shrang service — generating music, managing your account, and processing payments. We do not sell your personal data to third parties.</p>
</div></div>

<div class="sh-card"><div class="sh-card__body">
<h3 class="sh-heading--sm" style="margin-bottom:0.75rem;">Third-Party Services</h3>
<p class="sh-text-muted">We use Google (Lyria AI, OAuth), Stripe (payments), and standard cloud hosting. Each has their own privacy policy governing their data use.</p>
</div></div>

<div class="sh-card"><div class="sh-card__body">
<h3 class="sh-heading--sm" style="margin-bottom:0.75rem;">Your Rights</h3>
<p class="sh-text-muted">You can delete your account and all associated data at any time from your Account Settings page. You can also request a copy of your data by contacting support.</p>
</div></div>

<div class="sh-card"><div class="sh-card__body">
<h3 class="sh-heading--sm" style="margin-bottom:0.75rem;">Cookies</h3>
<p class="sh-text-muted">We use essential cookies for authentication and session management. We do not use advertising cookies or tracking pixels.</p>
</div></div>

<div class="sh-card"><div class="sh-card__body">
<h3 class="sh-heading--sm" style="margin-bottom:0.75rem;">Contact</h3>
<p class="sh-text-muted">For privacy questions, contact us via the <a href="{{ route('support') }}" style="color:var(--sh-orange);">Support page</a>.</p>
</div></div>
</div>

</div>
@endsection
