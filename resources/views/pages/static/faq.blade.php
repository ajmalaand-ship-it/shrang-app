@extends('layouts.public')
@section('title', 'FAQ — Shrang')
@section('content')
<div class="sh-page-wrap sh-page-wrap--narrow">
    <div style="margin-bottom:2rem;">
        <h1 class="sh-heading">Frequently Asked Questions</h1>
    </div>
    
<div class="sh-stack">
<div class="sh-card"><div class="sh-card__body">
<h3 class="sh-heading--sm" style="margin-bottom:0.75rem;">What languages does Shrang support?</h3>
<p class="sh-text-muted">Shrang supports Pashto, Dari/Farsi, Urdu, Arabic, Hindi, and English. RTL (right-to-left) scripts are fully supported for Pashto, Dari, Urdu, and Arabic.</p>
</div></div>

<div class="sh-card"><div class="sh-card__body">
<h3 class="sh-heading--sm" style="margin-bottom:0.75rem;">How long does it take to generate a song?</h3>
<p class="sh-text-muted">Song generation typically takes 60–90 seconds. Background music takes up to 3 minutes. The page updates automatically when your song is ready.</p>
</div></div>

<div class="sh-card"><div class="sh-card__body">
<h3 class="sh-heading--sm" style="margin-bottom:0.75rem;">Do I own the music I create?</h3>
<p class="sh-text-muted">You retain full rights to share and use the music you create on Shrang for personal and non-commercial purposes. For commercial use rights, please contact us.</p>
</div></div>

<div class="sh-card"><div class="sh-card__body">
<h3 class="sh-heading--sm" style="margin-bottom:0.75rem;">What happens to my credits if a song fails?</h3>
<p class="sh-text-muted">Credits are only deducted when a song is successfully generated. If generation fails for any reason, your credits are automatically released back to your balance.</p>
</div></div>

<div class="sh-card"><div class="sh-card__body">
<h3 class="sh-heading--sm" style="margin-bottom:0.75rem;">Can I download my songs?</h3>
<p class="sh-text-muted">Yes. All generated songs can be downloaded as MP3 files from the Clip Studio page. Downloads are available for as long as your account is active.</p>
</div></div>

<div class="sh-card"><div class="sh-card__body">
<h3 class="sh-heading--sm" style="margin-bottom:0.75rem;">How do I get more credits?</h3>
<p class="sh-text-muted">Visit the Credits page to purchase credit packages using Stripe — secure payment by card. Credits are added to your account instantly after payment.</p>
</div></div>

<div class="sh-card"><div class="sh-card__body">
<h3 class="sh-heading--sm" style="margin-bottom:0.75rem;">Is my payment information secure?</h3>
<p class="sh-text-muted">Yes. All payments are processed by Stripe — a certified PCI-DSS Level 1 payment provider. Shrang never stores your card details.</p>
</div></div>
</div>

<div style="text-align:center;margin-top:2rem;">
<p class="sh-text-muted" style="margin-bottom:1rem;">Still have questions?</p>
<a href="{{ route('support') }}" class="sh-btn sh-btn--ghost">Contact Support</a>
</div>

</div>
@endsection
