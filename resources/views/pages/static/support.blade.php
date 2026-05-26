@extends('layouts.public')
@section('title', 'Support — Shrang')
@section('content')
<div class="sh-page-wrap sh-page-wrap--narrow">
    <div style="margin-bottom:2rem;">
        <h1 class="sh-heading">Support</h1>
    </div>
    
<p class="sh-text-muted" style="font-size:1.1rem;max-width:640px;margin-bottom:2rem;">Need help? We are here for you. Send us a message and we will respond within 24 hours.</p>

<div class="sh-grid" style="grid-template-columns:1fr 1fr;gap:1.5rem;margin-bottom:2rem;">
<div class="sh-card"><div class="sh-card__body" style="text-align:center;">
<div style="font-size:2rem;margin-bottom:0.75rem;">📖</div>
<h3 class="sh-heading--sm" style="margin-bottom:0.5rem;">FAQ</h3>
<p class="sh-text-muted" style="margin-bottom:1rem;font-size:0.875rem;">Find answers to common questions.</p>
<a href="{{ route('faq') }}" class="sh-btn sh-btn--ghost sh-btn--sm">Read FAQ</a>
</div></div>

<div class="sh-card"><div class="sh-card__body" style="text-align:center;">
<div style="font-size:2rem;margin-bottom:0.75rem;">📧</div>
<h3 class="sh-heading--sm" style="margin-bottom:0.5rem;">Email Support</h3>
<p class="sh-text-muted" style="margin-bottom:1rem;font-size:0.875rem;">Contact our team directly.</p>
<a href="mailto:support@shrang.com" class="sh-btn sh-btn--ghost sh-btn--sm">Email Us</a>
</div></div>
</div>

<div class="sh-card">
<div class="sh-card__header">Send a Message</div>
<div class="sh-card__body">
@if(session('support_sent'))
<div class="sh-notice sh-notice--success">Your message has been sent. We will get back to you within 24 hours.</div>
@else
<form method="POST" action="{{ route('support.store') }}">
@csrf
<div class="sh-field">
<label class="sh-label">Your name</label>
<input type="text" name="name" class="sh-input" value="{{ auth()->user()->name ?? '' }}" required>
</div>
<div class="sh-field">
<label class="sh-label">Email address</label>
<input type="email" name="email" class="sh-input" value="{{ auth()->user()->email ?? '' }}" required>
</div>
<div class="sh-field">
<label class="sh-label">Subject</label>
<select name="subject" class="sh-select">
<option>I have a question about credits</option>
<option>My song generation failed</option>
<option>Payment issue</option>
<option>Account problem</option>
<option>Other</option>
</select>
</div>
<div class="sh-field">
<label class="sh-label">Message</label>
<textarea name="message" class="sh-textarea" rows="5" required></textarea>
</div>
<button type="submit" class="sh-btn sh-btn--primary">Send Message</button>
</form>
@endif
</div></div>

</div>
@endsection
