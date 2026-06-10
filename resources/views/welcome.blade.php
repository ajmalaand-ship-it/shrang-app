@extends('layouts.public')

@section('title', 'Shrang — Turn Any Words Into Original Music')

@section('content')

{{-- HERO --}}
<section class="home-hero">
    <div class="home-hero__inner">
        <div class="home-hero__waveform">
            @for($i = 0; $i < 40; $i++)
                <div class="home-hero__bar" style="animation-delay: {{ $i * 0.05 }}s; height: {{ rand(20, 100) }}%"></div>
            @endfor
        </div>
        <h1 class="home-hero__headline-en">Turn Words Into <em>Music & Reels</em></h1>
        <p class="home-hero__sub">Paste a poem, write lyrics, share a script, or type a few lines — Shrang turns your words into music, cover art, and downloadable reels for your own social posts. In Pashto, Dari, Urdu, Arabic, Hindi, or English.</p>
        <div class="home-hero__actions">
            @auth
                <a href="{{ route('create') }}" class="sh-btn sh-btn--primary home-hero__cta">Create New</a>
                <a href="{{ route('dashboard') }}" class="home-hero__ghost">My Clips</a>
            @else
                <a href="{{ route('register') }}" class="sh-btn sh-btn--primary home-hero__cta">Start Free — No Sign Up Needed</a>
                <a href="#how-it-works" class="home-hero__ghost">See how it works</a>
            @endauth
        </div>
        @guest
            <p class="home-hero__login">Already have an account? <a href="{{ route('login') }}">Log in</a></p>
        @endguest
    </div>
</section>

{{-- FEATURED CLIPS --}}
@php
    $featured = App\Models\Clip::join('clip_features', 'clips.id', '=', 'clip_features.clip_id')
        ->leftJoin('clip_stats', 'clips.id', '=', 'clip_stats.clip_id')
        ->where('clips.visibility', 'public')
        ->where('clips.status', 'ready')
        ->where('clip_features.is_blocked', false)
        ->orderByDesc('clip_features.is_pinned')
        ->orderByDesc('clip_features.featured_at')
        ->limit(5)
        ->get(['clips.*', 'clip_stats.play_count', 'clip_stats.like_count']);
@endphp

@if($featured->count() > 0)
<section class="home-section home-section--featured">
    <div class="home-section__inner">
        <p class="home-section__label">Community Creations</p>
        <h3 class="home-section__title">Listen to What People Are Making</h3>
        <div class="home-featured">
            @foreach($featured as $clip)
            @php
                $cover = $clip->mediaAssets()->where('type','cover_image')->where('is_primary',true)->first();
                $audio = $clip->mediaAssets()->whereIn('type',['song_audio','bed_audio'])->where('is_primary',true)->first();
                $reel  = $clip->mediaAssets()->where('type','reel_video')->where('is_primary',true)->first();
                $type  = $audio?->type === 'bed_audio' ? 'Background Music' : 'Song';
                $langNames = ["ps"=>"Pashto","fa"=>"Dari","ur"=>"Urdu","ar"=>"Arabic","hi"=>"Hindi","en"=>"English"];
            @endphp
            <a href="{{ route('player.show', $clip->slug) }}" class="home-featured__card" aria-label="Open {{ $clip->title }}">
                <div class="home-featured__cover">
                    @if($reel)
                        <video class="home-featured__img home-featured__reel" src="{{ $reel->cdn_url }}" muted loop playsinline preload="metadata" @if($cover) poster="{{ $cover->cdn_url }}" @endif></video>
                    @elseif($cover)
                        <img src="{{ $cover->cdn_url }}" alt="{{ $clip->title }}" class="home-featured__img">
                    @else
                        <div class="home-featured__placeholder">
                            <span>♪</span>
                            <small>Shrang</small>
                        </div>
                    @endif
                </div>
                <div class="home-featured__info">
                    <div class="home-featured__title">{{ $clip->title }}</div>
                    <div class="home-featured__meta">
                        <span class="sh-badge sh-badge--lang">{{ $langNames[$clip->language] ?? strtoupper($clip->language) }}</span>
                        <span class="sh-badge">{{ $type }}</span>
                        <span class="home-featured__likes">♥ {{ number_format($clip->like_count ?? 0) }}</span>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
        <div style="text-align:center;margin-top:2rem;">
            <a href="{{ route('discover') }}" class="home-hero__ghost">Explore all →</a>
        </div>
    </div>
</section>
@endif

{{-- HOW IT WORKS --}}
<section class="home-section home-section--dark" id="how-it-works">
    <div class="home-section__inner">
        <p class="home-section__label">Simple Process</p>
        <h3 class="home-section__title">How Shrang Works</h3>
        <p class="home-section__sub">A simple path from your words to music, cover art, and downloadable social-ready reels.</p>
        <div class="home-steps">
            <div class="home-step">
                <div class="home-step__number">1</div>
                <h4 class="home-step__title">Write Your Lyrics</h4>
                <p class="home-step__text">Type or paste your poem, lyrics, or idea in your language. Pashto, Dari, Urdu, Arabic, Hindi, and English are supported.</p>
            </div>
            <div class="home-step">
                <div class="home-step__number">2</div>
                <h4 class="home-step__title">Shrang Shapes the Music</h4>
                <p class="home-step__text">Shrang turns your words into melody, rhythm, and vocals tailored to your language and creative style.</p>
            </div>
            <div class="home-step">
                <div class="home-step__number">3</div>
                <h4 class="home-step__title">Create Your Reel</h4>
                <p class="home-step__text">Turn your song into a short video reel, cover image, or audio file you can download and post on your own social accounts.</p>
            </div>
        </div>
        <div class="home-section__more">
            <a href="{{ route('how-it-works') }}" class="home-hero__ghost">Learn more about how Shrang works →</a>
        </div>
    </div>
</section>

{{-- LANGUAGES --}}
<section class="home-section">
    <div class="home-section__inner" style="text-align:center;">
        <p class="home-section__label">Languages</p>
        <h3 class="home-section__title">Create in Your Language</h3>
        <div class="home-langs">
            <div class="home-lang"><span class="home-lang__script">پښتو</span><span class="home-lang__name">Pashto</span></div>
            <div class="home-lang"><span class="home-lang__script">دری</span><span class="home-lang__name">Dari</span></div>
            <div class="home-lang"><span class="home-lang__script">اردو</span><span class="home-lang__name">Urdu</span></div>
            <div class="home-lang"><span class="home-lang__script">العربية</span><span class="home-lang__name">Arabic</span></div>
            <div class="home-lang"><span class="home-lang__script">हिन्दी</span><span class="home-lang__name">Hindi</span></div>
            <div class="home-lang"><span class="home-lang__script">English</span><span class="home-lang__name">English</span></div>
        </div>
    </div>
</section>

{{-- PRICING --}}
<section class="home-section home-section--dark">
    <div class="home-section__inner">
        <p class="home-section__label">Pricing</p>
        <h3 class="home-section__title">Simple, Transparent Pricing</h3>
        <p class="home-section__sub">Start free. Buy credits only when you need more. No subscriptions.</p>
        <div class="home-pricing">
            <div class="home-pricing__free">
                <div class="home-pricing__free-badge">Free to Start</div>
                <div class="home-pricing__free-credits">20 Credits</div>
                <p class="home-pricing__free-desc">Every new account gets 20 free credits to try Shrang and create your first music assets.</p>
                @guest
                <a href="{{ route('register') }}" class="sh-btn sh-btn--primary" style="margin-top:1rem;">Get Started Free</a>
                @endguest
            </div>
            <div class="home-pricing__packages">
                @foreach(App\Models\CreditPackage::where('is_active',true)->orderBy('sort_order')->get() as $pkg)
                <div class="home-pricing__pkg">
                    <div class="home-pricing__pkg-name">{{ $pkg->name }}</div>
                    <div class="home-pricing__pkg-credits">{{ number_format($pkg->credits) }} credits</div>
                    <div class="home-pricing__pkg-price">${{ number_format($pkg->price_cents/100,2) }}</div>
                </div>
                @endforeach
            </div>
        </div>
        <div style="text-align:center;margin-top:1.5rem;">
            <a href="{{ route('pricing') }}" class="home-hero__ghost">See full pricing →</a>
        </div>
    </div>
</section>

{{-- FINAL CTA --}}
<section class="home-section home-cta-section">
    <div class="home-section__inner" style="text-align:center;">
        <h3 class="home-section__title">Your Words. Your Reel. Your Music.</h3>
        <p class="home-section__sub">Join creators turning poetry and lyrics into downloadable music, reels, and visual assets in their own language.</p>
        @auth
            <a href="{{ route('create') }}" class="pub-nav__cta" style="font-size:1rem;padding:0.875rem 2.5rem;">Create New Song</a>
        @else
            <a href="{{ route('register') }}" class="pub-nav__cta" style="font-size:1rem;padding:0.875rem 2.5rem;">Start Creating for Free</a>
        @endauth
    </div>
</section>

@endsection

@section('page_js')
<script>
var homePlayer = new Audio();
var homePlaying = null;
function homePlay(btn, url) {
    if (homePlaying === btn) {
        homePlayer.pause();
        btn.textContent = '▶';
        homePlaying = null;
        return;
    }
    if (homePlaying) { homePlaying.textContent = '▶'; }
    homePlayer.src = url;
    homePlayer.play();
    btn.textContent = '⏸';
    homePlaying = btn;
}
</script>
@endsection
