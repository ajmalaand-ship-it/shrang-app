<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ $direction ?? 'ltr' }}">
<head>
    {{-- Shrang favicon --}}
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicons/favicon-16x16.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicons/favicon-32x32.png">
    <link rel="apple-touch-icon" href="/favicons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/favicons/android-chrome-192x192.png">
    <link rel="icon" type="image/png" sizes="512x512" href="/favicons/android-chrome-512x512.png">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="google-site-verification" content="nqSEyOMBTJZYxW6itl0sSyXmPOgVwLKwpu-CnUx9QQw">
    <link rel="canonical" href="{{ url()->current() }}">
    <title>@yield('title', 'Shrang')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @if($isRtl ?? false)
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;600;700&family=Amiri:wght@400;700&display=swap" rel="stylesheet">
    @endif
    <link rel="stylesheet" href="{{ asset('css/shrang.css') }}">
    @yield('head_extra')
</head>
<body class="public-body">

@include('components.nav')

<main class="pub-main">
    @yield('content')
</main>

<footer class="pub-footer">
    <div class="pub-footer__inner">
        <div class="pub-footer__brand">
            <a href="/" class="pub-nav__logo" style="margin-bottom:1rem;">
                <span class="pub-nav__logo-ar">شرنګ</span>
                <span class="pub-nav__logo-en">Shrang</span>
            </a>
            <p class="pub-footer__tagline">Turn your poetry and lyrics into downloadable music, reels, and visual assets.</p>
            <div class="pub-footer__langs">
                @foreach(['ps'=>'پښتو','fa'=>'دری','ur'=>'اردو','ar'=>'عربي','hi'=>'हि','en'=>'EN'] as $code=>$label)
                    <a href="{{ route('lang.switch', $code) }}">{{ $label }}</a>
                @endforeach
            </div>
        </div>
        <div class="pub-footer__cols">
            <div class="pub-footer__col">
                <h4>Product</h4>
                <a href="{{ route('how-it-works') }}">How it works</a>
                <a href="{{ route('pricing') }}">Pricing</a>
                <a href="{{ route('discover') }}">Discover</a>
                <a href="{{ route('create') }}">Create Music</a>
            </div>
            <div class="pub-footer__col">
                <h4>Support</h4>
                <a href="{{ route('faq') }}">FAQ</a>
                <a href="{{ route('support') }}">Contact us</a>
                <a href="{{ route('about') }}">About Shrang</a>
            </div>
            <div class="pub-footer__col">
                <h4>Legal</h4>
                <a href="{{ route('terms') }}">Terms of Service</a>
                <a href="{{ route('privacy') }}">Privacy Policy</a>
            </div>
        </div>
    </div>
    <div class="pub-footer__bottom">
        <span>&copy; {{ date('Y') }} Shrang. All rights reserved.</span>
    </div>
</footer>



    @auth
    <nav class="sh-bottom-nav">
        <a href="{{ route('create') }}" class="sh-bottom-nav__item {{ request()->routeIs('create*') ? 'active' : '' }}">
            <span class="sh-bottom-nav__icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
            </span>
            <span class="sh-bottom-nav__label">Create</span>
        </a>
        <a href="{{ route('discover') }}" class="sh-bottom-nav__item {{ request()->routeIs('discover*') ? 'active' : '' }}">
            <span class="sh-bottom-nav__icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            </span>
            <span class="sh-bottom-nav__label">Discover</span>
        </a>
        <a href="{{ route('dashboard') }}" class="sh-bottom-nav__item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <span class="sh-bottom-nav__icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
            </span>
            <span class="sh-bottom-nav__label">My Clips</span>
        </a>
        <a href="{{ route('account') }}" class="sh-bottom-nav__item {{ request()->routeIs('account*') ? 'active' : '' }}">
            <span class="sh-bottom-nav__icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            </span>
            <span class="sh-bottom-nav__label">Account</span>
        </a>
    </nav>
    @endauth
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.sh-select').forEach(function(sel) {
        if (sel.closest('.sh-select-wrap')) return;
        var wrap = document.createElement('div');
        wrap.className = 'sh-select-wrap' + (sel.classList.contains('sh-select--sm') ? ' sh-select-wrap--sm' : '');
        sel.parentNode.insertBefore(wrap, sel);
        wrap.appendChild(sel);
    });
});
</script>
@yield('page_js')
</body>
</html>
