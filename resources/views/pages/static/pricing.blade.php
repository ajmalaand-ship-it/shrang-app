@extends('layouts.public')
@section('title', 'Pricing — Shrang')
@section('content')
<div class="sh-page-wrap sh-page-wrap--narrow">
    <div style="margin-bottom:2rem;">
        <h1 class="sh-heading">Simple, Transparent Pricing</h1>
    </div>
    
<p class="sh-text-muted" style="font-size:1.1rem;max-width:640px;margin-bottom:2rem;">Start free. Buy credits when you need more. No subscriptions, no hidden fees.</p>

<div class="sh-card" style="margin-bottom:2rem;border-color:rgba(232,115,42,0.3);">
<div class="sh-card__header" style="color:var(--sh-orange);">Free to Start</div>
<div class="sh-card__body">
<p class="sh-text-muted" style="margin-bottom:1rem;">Every new account gets <strong style="color:var(--sh-text);">20 free credits</strong> — enough to create 2 songs or 4 background music tracks.</p>
<ul style="list-style:none;display:flex;flex-direction:column;gap:0.5rem;">
<li style="display:flex;gap:0.75rem;"><span style="color:var(--sh-orange);">✓</span><span class="sh-text-muted">2 songs (10 credits each)</span></li>
<li style="display:flex;gap:0.75rem;"><span style="color:var(--sh-orange);">✓</span><span class="sh-text-muted">4 background music tracks (5 credits each)</span></li>
<li style="display:flex;gap:0.75rem;"><span style="color:var(--sh-orange);">✓</span><span class="sh-text-muted">Cover image generation (3 credits each)</span></li>
<li style="display:flex;gap:0.75rem;"><span style="color:var(--sh-orange);">✓</span><span class="sh-text-muted">Full studio features — download, share, reel</span></li>
</ul>
</div></div>

<h2 class="sh-heading--sm" style="margin-bottom:1rem;">Credit Packages</h2>
<div class="credits-packages" style="margin-bottom:2rem;">
@foreach(App\Models\CreditPackage::where('is_active',true)->orderBy('sort_order')->get() as $package)
<div class="sh-card credits-package">
<div class="sh-card__body" style="text-align:center;">
<div class="credits-package__name">{{ $package->name }}</div>
<div class="credits-package__credits">{{ number_format($package->credits) }}<span class="sh-text-muted" style="font-size:1rem;font-weight:400;"> credits</span></div>
<div class="credits-package__price">\${{ number_format($package->price_cents/100,2) }}</div>
<div class="sh-text-muted" style="font-size:0.8rem;margin-bottom:1rem;">~{{ floor($package->credits/10) }} songs</div>
<a href="{{ route('register') }}" class="sh-btn sh-btn--primary sh-btn--full">Get Started</a>
</div></div>
@endforeach
</div>

<div class="sh-card"><div class="sh-card__body">
<h2 class="sh-heading--sm" style="margin-bottom:1rem;">Credit Costs</h2>
<div style="display:flex;flex-direction:column;gap:0;">
<div class="studio-details__row"><span class="sh-label">Song generation</span><span>10 credits</span></div>
<div class="studio-details__row"><span class="sh-label">Background music</span><span>5 credits</span></div>
<div class="studio-details__row"><span class="sh-label">Cover image</span><span>3 credits</span></div>
<div class="studio-details__row"><span class="sh-label">Reel creation</span><span>5 credits</span></div>
</div></div></div>

</div>
@endsection
