@extends('layouts.public')

@section('title', 'شرنګ — ستاسو شعر، ستاسو سندره')

@section('content')

{{-- HERO --}}
<section class="home-hero" dir="rtl">
    <div class="home-hero__inner">
        <div class="home-hero__badge">● د ګوګل Lyria 3 AI لخوا چلول کیږي</div>
        <h1 class="home-hero__headline-ar">ستاسو شعر، ستاسو سندره</h1>
        <h2 class="home-hero__headline-en" style="font-family:var(--sh-font-rtl);font-size:clamp(1.5rem,3vw,2.25rem);">خپل شعر او خپله موسیقي جوړه کړئ</h2>
        <p class="home-hero__sub" style="font-family:var(--sh-font-rtl);">د خپل پښتو، دري، اردو، عربي، هندي او انګلیسي شعرونو او ترانو نه اصلي سندرې جوړ کړئ — د یو دقیقې دننه.</p>
        <div class="home-hero__actions">
            @auth
                <a href="{{ route('create') }}" class="pub-nav__cta home-hero__cta">نوې سندره جوړه کړئ</a>
                <a href="{{ route('dashboard') }}" class="home-hero__ghost">زما کلیپونه</a>
            @else
                <a href="{{ route('register') }}" class="pub-nav__cta home-hero__cta">وړیا سندره جوړه کړئ</a>
                <a href="#how-it-works" class="home-hero__ghost">دلته وګورئ چې څنګه کار کوي</a>
            @endauth
        </div>
        <div class="home-hero__waveform">
            @for($i = 0; $i < 40; $i++)
                <div class="home-hero__bar" style="animation-delay: {{ $i * 0.05 }}s; height: {{ rand(20, 100) }}%"></div>
            @endfor
        </div>
    </div>
</section>

{{-- FEATURED CLIPS --}}
@php
    $featured = App\Models\Clip::join('clip_features', 'clips.id', '=', 'clip_features.clip_id')
        ->leftJoin('clip_stats', 'clips.id', '=', 'clip_stats.clip_id')
        ->where('clips.visibility', 'public')
        ->where('clips.status', 'ready')
        ->where('clip_features.is_blocked', false)
        ->where('clips.language', 'ps')
        ->orderByDesc('clip_features.is_pinned')
        ->orderByDesc('clip_features.featured_at')
        ->limit(4)
        ->get(['clips.*', 'clip_stats.play_count', 'clip_stats.like_count']);
    if ($featured->count() === 0) {
        $featured = App\Models\Clip::join('clip_features', 'clips.id', '=', 'clip_features.clip_id')
            ->leftJoin('clip_stats', 'clips.id', '=', 'clip_stats.clip_id')
            ->where('clips.visibility', 'public')
            ->where('clips.status', 'ready')
            ->where('clip_features.is_blocked', false)
            ->orderByDesc('clip_features.featured_at')
            ->limit(4)
            ->get(['clips.*', 'clip_stats.play_count', 'clip_stats.like_count']);
    }
@endphp

@if($featured->count() > 0)
<section class="home-section" dir="rtl">
    <div class="home-section__inner">
        <p class="home-section__label">د ټولنې جوړونې</p>
        <h3 class="home-section__title" style="font-family:var(--sh-font-rtl);">واورئ چې خلک څه جوړوي</h3>
        <div class="home-featured">
            @foreach($featured as $clip)
            @php
                $cover = $clip->mediaAssets()->where('type','cover_image')->where('is_primary',true)->first();
                $audio = $clip->mediaAssets()->whereIn('type',['song_audio','bed_audio'])->where('is_primary',true)->first();
            @endphp
            <div class="home-featured__card">
                <div class="home-featured__cover">
                    @if($cover)
                        <img src="{{ $cover->cdn_url }}" alt="{{ $clip->title }}" class="home-featured__img">
                    @else
                        <div class="home-featured__placeholder">♪</div>
                    @endif
                    @if($audio)
                    <button class="home-featured__play" onclick="homePlay(this, '{{ $audio->cdn_url }}')" aria-label="Play">▶</button>
                    @endif
                </div>
                <div class="home-featured__info">
                    <div class="home-featured__title" dir="rtl" style="font-family:var(--sh-font-rtl);">{{ $clip->title }}</div>
                    <div class="home-featured__meta">
                        <span class="sh-badge sh-badge--lang">{{ strtoupper($clip->language) }}</span>
                        <span class="home-featured__likes">♥ {{ number_format($clip->like_count ?? 0) }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div style="text-align:center;margin-top:2rem;">
            <a href="{{ route('discover') }}" class="home-hero__ghost">ټول وګورئ ←</a>
        </div>
    </div>
</section>
@endif

{{-- HOW IT WORKS --}}
<section class="home-section home-section--dark" id="how-it-works" dir="rtl">
    <div class="home-section__inner">
        <p class="home-section__label">اسانه پروسه</p>
        <h3 class="home-section__title" style="font-family:var(--sh-font-rtl);">شرنګ څنګه کار کوي</h3>
        <p class="home-section__sub" style="font-family:var(--sh-font-rtl);">له شعر نه موسیقي ته — د درو ساده ګامونو سره</p>
        <div class="home-steps">
            <div class="home-step">
                <div class="home-step__number">١</div>
                <h4 class="home-step__title" style="font-family:var(--sh-font-rtl);">خپل شعر ولیکئ</h4>
                <p class="home-step__text" style="font-family:var(--sh-font-rtl);">خپل شعر یا ترانه زموږ د ملاتړ شوي ژبو کې ولیکئ. پښتو او دري د RTL متن لوري سره بشپړ ملاتړ لري.</p>
            </div>
            <div class="home-step">
                <div class="home-step__number">٢</div>
                <h4 class="home-step__title" style="font-family:var(--sh-font-rtl);">AI ستاسو سندره جوړوي</h4>
                <p class="home-step__text" style="font-family:var(--sh-font-rtl);">د ګوګل Lyria 3 ستاسو د کلماتو شاوخوا اصلي سندره جوړوي — سور، ریتم، غږ، او بشپړ موسیقي جوړښت.</p>
            </div>
            <div class="home-step">
                <div class="home-step__number">٣</div>
                <h4 class="home-step__title" style="font-family:var(--sh-font-rtl);">شریک کړئ او ډاونلوډ کړئ</h4>
                <p class="home-step__text" style="font-family:var(--sh-font-rtl);">ستاسو سندره د یو دقیقې دننه چمتو ده. د پوښ انځور اضافه کړئ، لینک شریک کړئ، یا MP3 ډاونلوډ کړئ.</p>
            </div>
        </div>
    </div>
</section>

{{-- LANGUAGES --}}
<section class="home-section" dir="rtl">
    <div class="home-section__inner" style="text-align:center;">
        <p class="home-section__label">ژبې</p>
        <h3 class="home-section__title" style="font-family:var(--sh-font-rtl);">خپله ژبه وکاروئ</h3>
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

{{-- FINAL CTA --}}
<section class="home-section home-cta-section" dir="rtl">
    <div class="home-section__inner" style="text-align:center;">
        <h3 class="home-section__title" style="font-family:var(--sh-font-rtl);">ستاسو کلمې. ستاسو موسیقي.</h3>
        <p class="home-section__sub" style="font-family:var(--sh-font-rtl);">هغه شاعران او موسیقاران چې خپله ژبه کې اصلي AI موسیقي جوړوي سره یو ځای شئ.</p>
        @auth
            <a href="{{ route('create') }}" class="pub-nav__cta" style="font-size:1rem;padding:0.875rem 2.5rem;">نوې سندره جوړه کړئ</a>
        @else
            <a href="{{ route('register') }}" class="pub-nav__cta" style="font-size:1rem;padding:0.875rem 2.5rem;">وړیا پیل کړئ</a>
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
