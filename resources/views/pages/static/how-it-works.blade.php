@extends('layouts.public')
@section('title', 'How It Works')
@section('content')
<div class="sh-page-wrap sh-page-wrap--narrow">
    <div style="margin-bottom:2rem;">
        <h1 class="sh-heading">How It Works</h1>
    </div>
    
<p class="sh-text-muted" style="font-size:1.1rem;max-width:640px;margin-bottom:2rem;">Creating a song with Shrang takes less than 2 minutes. Here is the full process.</p>

<div class="sh-stack">
<div class="sh-card"><div class="sh-card__body" style="display:flex;gap:1.5rem;align-items:flex-start;">
<div style="font-size:2rem;flex-shrink:0;">✍️</div>
<div><h3 class="sh-heading--sm" style="margin-bottom:0.5rem;">Step 1 — Write Your Poetry</h3>
<p class="sh-text-muted">Enter your poem, lyrics, or any creative text in any supported language. Shrang understands Pashto, Dari, Urdu, Arabic, Hindi, and English — including right-to-left scripts.</p></div>
</div></div>

<div class="sh-card"><div class="sh-card__body" style="display:flex;gap:1.5rem;align-items:flex-start;">
<div style="font-size:2rem;flex-shrink:0;">🎵</div>
<div><h3 class="sh-heading--sm" style="margin-bottom:0.5rem;">Step 2 — Choose Your Style</h3>
<p class="sh-text-muted">Select the language and let Shrang handle the rest. Our AI understands cultural musical traditions and creates music that fits your words naturally.</p></div>
</div></div>

<div class="sh-card"><div class="sh-card__body" style="display:flex;gap:1.5rem;align-items:flex-start;">
<div style="font-size:2rem;flex-shrink:0;">⚡</div>
<div><h3 class="sh-heading--sm" style="margin-bottom:0.5rem;">Step 3 — AI Generates Your Song</h3>
<p class="sh-text-muted">Google Lyria 3 AI creates an original song from your words — melody, rhythm, vocals, and full musical arrangement. This usually takes 60–90 seconds.</p></div>
</div></div>

<div class="sh-card"><div class="sh-card__body" style="display:flex;gap:1.5rem;align-items:flex-start;">
<div style="font-size:2rem;flex-shrink:0;">🎨</div>
<div><h3 class="sh-heading--sm" style="margin-bottom:0.5rem;">Step 4 — Add Cover Art</h3>
<p class="sh-text-muted">Generate a beautiful AI cover image for your song. You can describe what you want or let the AI create something inspired by your lyrics.</p></div>
</div></div>

<div class="sh-card"><div class="sh-card__body" style="display:flex;gap:1.5rem;align-items:flex-start;">
<div style="font-size:2rem;flex-shrink:0;">📤</div>
<div><h3 class="sh-heading--sm" style="margin-bottom:0.5rem;">Step 5 — Share & Download</h3>
<p class="sh-text-muted">Download your song as an MP3, share it with a link, create a short reel for social media, or publish it to the Shrang Discover page for the community to enjoy.</p></div>
</div></div>
</div>

<div style="text-align:center;margin-top:2rem;">
<a href="{{ route('register') }}" class="sh-btn sh-btn--primary sh-btn--lg">Start Creating Free</a>
</div>

</div>
@endsection
