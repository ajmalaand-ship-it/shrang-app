<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Shrang — AI Music from Your Poetry</title>
    <meta name="description" content="Create original AI music from your Pashto, Dari, Urdu, Arabic, Hindi, and English poetry and lyrics.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&family=Inter:wght@300;400;500;600;700&family=Vazirmatn:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --orange: #E8732A;
            --orange-light: #F0924F;
            --orange-dark: #C4622D;
            --gold: #D4A843;
            --dark: #0F0E0C;
            --dark-2: #161410;
            --dark-3: #1E1B16;
            --dark-4: #28231C;
            --text: #F5F0E8;
            --text-muted: #9A8E7E;
            --border: #2C2720;
        }
        html { scroll-behavior: smooth; }
        body {
            background: var(--dark);
            color: var(--text);
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* NAV */
        .nav {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 100;
            padding: 1.25rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: rgba(15, 14, 12, 0.9);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border);
        }
        .nav__logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
        }
        .nav__logo-arabic {
            font-family: 'Amiri', serif;
            font-size: 1.5rem;
            color: var(--orange);
        }
        .nav__logo-latin {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text);
            letter-spacing: -0.02em;
        }
        .nav__links {
            display: flex;
            align-items: center;
            gap: 2rem;
            list-style: none;
        }
        .nav__links a {
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.875rem;
            transition: color 0.2s;
        }
        .nav__links a:hover { color: var(--text); }
        .nav__cta {
            background: var(--orange) !important;
            color: #fff !important;
            padding: 0.5rem 1.25rem !important;
            border-radius: 9999px !important;
            font-weight: 600 !important;
            font-size: 0.875rem !important;
            transition: background 0.2s !important;
        }
        .nav__cta:hover { background: var(--orange-light) !important; color: #fff !important; }
        .lang-switcher {
            display: flex;
            gap: 0.25rem;
        }
        .lang-switcher a {
            font-size: 0.7rem;
            color: var(--text-muted);
            text-decoration: none;
            padding: 0.25rem 0.4rem;
            border-radius: 4px;
            transition: color 0.2s;
        }
        .lang-switcher a:hover { color: var(--orange); }

        /* HERO */
        .hero {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 8rem 2rem 5rem;
            position: relative;
            overflow: hidden;
        }
        .hero__bg {
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 70% 50% at 50% -10%, rgba(232, 115, 42, 0.18) 0%, transparent 70%),
                radial-gradient(ellipse 50% 40% at 90% 90%, rgba(212, 168, 67, 0.06) 0%, transparent 60%);
        }
        .hero__eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(232, 115, 42, 0.1);
            border: 1px solid rgba(232, 115, 42, 0.25);
            padding: 0.4rem 1rem;
            border-radius: 9999px;
            font-size: 0.8rem;
            color: var(--orange-light);
            margin-bottom: 2rem;
            position: relative;
        }
        .hero__eyebrow::before {
            content: '';
            width: 6px; height: 6px;
            background: var(--orange);
            border-radius: 50%;
            animation: pulse 2s ease-in-out infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(0.8); }
        }
        .hero__poetry {
            font-family: 'Amiri', serif;
            font-size: clamp(2.5rem, 7vw, 5rem);
            color: var(--orange-light);
            line-height: 1.3;
            margin-bottom: 1rem;
            position: relative;
            direction: rtl;
        }
        .hero__title {
            font-size: clamp(1.5rem, 3.5vw, 2.5rem);
            font-weight: 600;
            margin-bottom: 1.25rem;
            position: relative;
            color: var(--text);
            line-height: 1.3;
        }
        .hero__title em {
            font-style: normal;
            color: var(--orange);
        }
        .hero__tagline {
            font-size: clamp(0.9rem, 2vw, 1.1rem);
            color: var(--text-muted);
            max-width: 520px;
            margin: 0 auto;
            position: relative;
        }
        .hero__actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 2.5rem;
            position: relative;
        }
        .btn-primary {
            background: var(--orange);
            color: #fff;
            padding: 0.875rem 2.5rem;
            border-radius: 9999px;
            font-weight: 600;
            font-size: 1rem;
            text-decoration: none;
            transition: all 0.2s;
            display: inline-block;
            border: none;
            cursor: pointer;
        }
        .btn-primary:hover {
            background: var(--orange-light);
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(232, 115, 42, 0.35);
        }
        .btn-ghost {
            border: 1px solid var(--border);
            color: var(--text-muted);
            padding: 0.875rem 2rem;
            border-radius: 9999px;
            font-size: 1rem;
            text-decoration: none;
            transition: all 0.2s;
            display: inline-block;
        }
        .btn-ghost:hover {
            border-color: var(--orange);
            color: var(--orange);
        }

        /* WAVEFORM */
        .waveform {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 3px;
            height: 56px;
            margin-top: 3rem;
            position: relative;
        }
        .waveform__bar {
            width: 3px;
            background: linear-gradient(to top, var(--orange-dark), var(--orange-light));
            border-radius: 9999px;
            animation: wave 1.6s ease-in-out infinite;
        }
        @keyframes wave {
            0%, 100% { transform: scaleY(0.25); opacity: 0.4; }
            50% { transform: scaleY(1); opacity: 0.9; }
        }

        /* LANGUAGES */
        .languages {
            padding: 5rem 2rem;
            text-align: center;
            background: var(--dark-2);
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
        }
        .section-label {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.25em;
            color: var(--text-muted);
            margin-bottom: 2.5rem;
        }
        .languages__grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.25rem;
            max-width: 580px;
            margin: 0 auto;
        }
        .lang-badge {
            padding: 0.875rem 1.75rem;
            border: 1px solid var(--border);
            border-radius: 9999px;
            background: var(--dark-3);
            transition: all 0.2s;
            text-align: center;
        }
        .lang-badge:hover {
            border-color: var(--orange);
            background: rgba(232, 115, 42, 0.06);
        }
        .lang-badge__name {
            font-size: 1.1rem;
            color: var(--text);
            display: block;
        }
        .lang-badge__label {
            font-size: 0.65rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.12em;
            display: block;
            margin-top: 0.2rem;
        }

        /* STEPS */
        .steps {
            padding: 6rem 2rem;
            max-width: 1100px;
            margin: 0 auto;
        }
        .section-heading {
            text-align: center;
            font-size: clamp(1.5rem, 3vw, 2.25rem);
            font-weight: 700;
            margin-bottom: 0.75rem;
        }
        .section-sub {
            text-align: center;
            color: var(--text-muted);
            margin-bottom: 4rem;
            font-size: 1rem;
        }
        .steps__grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
        }
        .step-card {
            background: var(--dark-3);
            border: 1px solid var(--border);
            border-radius: 1.25rem;
            padding: 2rem;
            transition: all 0.2s;
            position: relative;
            overflow: hidden;
        }
        .step-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 2px;
            background: linear-gradient(90deg, var(--orange), var(--gold));
            transform: scaleX(0);
            transition: transform 0.3s;
        }
        .step-card:hover::before { transform: scaleX(1); }
        .step-card:hover { border-color: rgba(232, 115, 42, 0.3); }
        .step-card__number {
            font-family: 'Amiri', serif;
            font-size: 3rem;
            color: var(--orange);
            opacity: 0.25;
            line-height: 1;
            margin-bottom: 1.25rem;
        }
        .step-card__title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.75rem;
            color: var(--text);
        }
        .step-card__text {
            font-size: 0.875rem;
            color: var(--text-muted);
            line-height: 1.75;
        }

        /* CTA */
        .cta-section {
            padding: 7rem 2rem;
            text-align: center;
            background: var(--dark-2);
            border-top: 1px solid var(--border);
            position: relative;
            overflow: hidden;
        }
        .cta-section::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse 60% 50% at 50% 50%, rgba(232, 115, 42, 0.08) 0%, transparent 70%);
        }
        .cta-section__poetry {
            font-family: 'Vazirmatn', 'Amiri', sans-serif;
            font-size: clamp(1.75rem, 4vw, 3rem);
            color: var(--orange-light);
            direction: rtl;
            margin-bottom: 1.5rem;
            position: relative;
            opacity: 0.85;
        }
        .cta-section__title {
            font-size: clamp(1.5rem, 3vw, 2.25rem);
            font-weight: 700;
            margin-bottom: 1rem;
            position: relative;
        }
        .cta-section__sub {
            color: var(--text-muted);
            margin-bottom: 2.5rem;
            max-width: 440px;
            margin-left: auto;
            margin-right: auto;
            position: relative;
        }

        /* FOOTER */
        .footer {
            padding: 2rem;
            text-align: center;
            border-top: 1px solid var(--border);
            color: var(--text-muted);
            font-size: 0.8rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1.5rem;
            flex-wrap: wrap;
        }
        .footer a {
            color: var(--text-muted);
            text-decoration: none;
            transition: color 0.2s;
        }
        .footer a:hover { color: var(--orange); }

        /* MOBILE */
        @media (max-width: 640px) {
            .nav { padding: 1rem; }
            .nav__links { display: none; }
            .hero { padding: 6rem 1.25rem 4rem; }
            .steps { padding: 4rem 1.25rem; }
            .languages { padding: 3rem 1.25rem; }
            .cta-section { padding: 4rem 1.25rem; }
            .footer { flex-direction: column; gap: 0.5rem; }
        }
    </style>
</head>
<body>

    <nav class="nav">
        <a href="/" class="nav__logo">
            <span class="nav__logo-arabic">شرنګ</span>
            <span class="nav__logo-latin">Shrang</span>
        </a>
        <ul class="nav__links">
            <li><a href="#how-it-works">How it works</a></li>
            <li><a href="{{ route('discover') }}">Discover</a></li>
            @auth
                <li><a href="{{ route('dashboard') }}">My Clips</a></li>
                <li><a href="{{ route('credits') }}">Credits</a></li>
                <li><a href="{{ route('account') }}">Account</a></li>
                <li><a href="{{ route('create') }}" class="nav__cta">Create Music</a></li>
            @else
                <li><a href="{{ route('login') }}">Log in</a></li>
                <li><a href="{{ route('register') }}" class="nav__cta">Start Creating Free</a></li>
            @endauth
        </ul>
        <div class="lang-switcher">
            @foreach (['ps' => 'پښتو', 'fa' => 'دری', 'ur' => 'اردو', 'ar' => 'عربي', 'hi' => 'हि', 'en' => 'EN'] as $code => $label)
                <a href="{{ route('lang.switch', $code) }}">{{ $label }}</a>
            @endforeach
        </div>
    </nav>

    <section class="hero">
        <div class="hero__bg"></div>
        <div class="hero__eyebrow">Powered by Google Lyria 3 AI</div>
        <p class="hero__poetry">ستاسو شعر، ستاسو سندره</p>
        <h1 class="hero__title">Turn Your Poetry Into <em>Original Music</em></h1>
        <p class="hero__tagline">Create AI-generated songs from your Pashto, Dari, Urdu, Arabic, Hindi, and English lyrics — in seconds.</p>
        <div class="hero__actions">
            <a href="{{ route('login') }}" class="btn-primary">Create Your Song Free</a>
            <a href="#how-it-works" class="btn-ghost">See how it works</a>
        </div>
        <div class="waveform">
            @for ($i = 0; $i < 36; $i++)
                <div class="waveform__bar" style="height:{{ rand(10,48) }}px;animation-delay:{{ number_format($i * 0.045, 3) }}s;"></div>
            @endfor
        </div>
    </section>

    <section class="languages">
        <p class="section-label">Supported Languages</p>
        <div class="languages__grid">
            <div class="lang-badge"><span class="lang-badge__name">پښتو</span><span class="lang-badge__label">Pashto</span></div>
            <div class="lang-badge"><span class="lang-badge__name">دری</span><span class="lang-badge__label">Dari</span></div>
            <div class="lang-badge"><span class="lang-badge__name">اردو</span><span class="lang-badge__label">Urdu</span></div>
            <div class="lang-badge"><span class="lang-badge__name">العربية</span><span class="lang-badge__label">Arabic</span></div>
            <div class="lang-badge"><span class="lang-badge__name">हिन्दी</span><span class="lang-badge__label">Hindi</span></div>
            <div class="lang-badge"><span class="lang-badge__name">English</span><span class="lang-badge__label">English</span></div>
        </div>
    </section>

    <section class="steps" id="how-it-works">
        <h2 class="section-heading">How Shrang Works</h2>
        <p class="section-sub">From poetry to music in three simple steps</p>
        <div class="steps__grid">
            <div class="step-card">
                <div class="step-card__number">١</div>
                <h3 class="step-card__title">Write Your Lyrics</h3>
                <p class="step-card__text">Type or paste your poetry in any of our six supported languages. Pashto and Dari are fully supported with RTL text direction.</p>
            </div>
            <div class="step-card">
                <div class="step-card__number">٢</div>
                <h3 class="step-card__title">AI Composes Your Song</h3>
                <p class="step-card__text">Google Lyria 3 creates an original song around your words — melody, rhythm, vocals, and full musical structure tailored to your language.</p>
            </div>
            <div class="step-card">
                <div class="step-card__number">٣</div>
                <h3 class="step-card__title">Share & Download</h3>
                <p class="step-card__text">Your song is ready in seconds. Add a cover image, create a short reel, share the link, or download the audio file.</p>
            </div>
        </div>
    </section>

    <section class="cta-section">
        <p class="cta-section__poetry">خپل شعر، خپله موسیقي</p>
        <h2 class="cta-section__title">Your Words. Your Music.</h2>
        <p class="cta-section__sub">Join poets and musicians creating original AI music in their own language.</p>
        <a href="{{ route('login') }}" class="btn-primary">Start Creating for Free</a>
    </section>

    <footer class="footer">
        <span>&copy; {{ date('Y') }} Shrang</span>
        <a href="{{ route('login') }}">Log in</a>
        <a href="#how-it-works">How it works</a>
        <a href="{{ route('lang.switch', 'ps') }}">پښتو</a>
        <a href="{{ route('lang.switch', 'fa') }}">دری</a>
    </footer>

</body>
</html>
