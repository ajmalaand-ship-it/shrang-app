<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ in_array(app()->getLocale(), ['ps','fa','ur','ar']) ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Shrang')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&family=Inter:wght@300;400;500;600;700&family=Vazirmatn:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/shrang.css') }}">
    @yield('head_extra')
</head>
<body class="public-body">

<nav class="pub-nav">
    <div class="pub-nav__inner">
        <a href="/" class="pub-nav__logo">
            <span class="pub-nav__logo-ar">شرنګ</span>
            <span class="pub-nav__logo-en">Shrang</span>
        </a>
        <ul class="pub-nav__links">
            <li><a href="{{ route('how-it-works') }}" class="{{ request()->routeIs('how-it-works') ? 'active' : '' }}">How it works</a></li>
            <li><a href="{{ route('discover') }}" class="{{ request()->routeIs('discover*') ? 'active' : '' }}">Discover</a></li>
            <li><a href="{{ route('pricing') }}" class="{{ request()->routeIs('pricing') ? 'active' : '' }}">Pricing</a></li>
            <li><a href="{{ route('faq') }}" class="{{ request()->routeIs('faq') ? 'active' : '' }}">FAQ</a></li>
        </ul>
        <div class="pub-nav__actions">
            @auth
                <a href="{{ route('dashboard') }}" class="pub-nav__login">My Clips</a>
                <a href="{{ route('create') }}" class="pub-nav__cta">Create Music</a>
            @else
                <a href="{{ route('login') }}" class="pub-nav__login">Log in</a>
                <a href="{{ route('register') }}" class="pub-nav__cta">Start Free</a>
            @endauth
        </div>
        <button class="pub-nav__burger" id="pub-burger" aria-label="Open menu">
            <span></span><span></span><span></span>
        </button>
    </div>
</nav>

<div class="pub-nav__drawer" id="pub-drawer">
    <div class="pub-nav__drawer-inner">
        <a href="/">Home</a>
        <a href="{{ route('how-it-works') }}">How it works</a>
        <a href="{{ route('discover') }}">Discover</a>
        <a href="{{ route('pricing') }}">Pricing</a>
        <a href="{{ route('faq') }}">FAQ</a>
        <a href="{{ route('about') }}">About</a>
        <a href="{{ route('support') }}">Support</a>
        <div class="pub-nav__drawer-divider"></div>
        @auth
            <a href="{{ route('create') }}" class="pub-nav__cta">Create Music</a>
            <a href="{{ route('dashboard') }}">My Clips</a>
            <a href="{{ route('account') }}">Account</a>
        @else
            <a href="{{ route('login') }}">Log in</a>
            <a href="{{ route('register') }}" class="pub-nav__cta">Start Free</a>
        @endauth
    </div>
</div>
<div class="pub-nav__overlay" id="pub-overlay"></div>

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
            <p class="pub-footer__tagline">Turn your poetry into original AI music. Powered by Google Lyria 3.</p>
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

<script>
var burger = document.getElementById('pub-burger');
var drawer = document.getElementById('pub-drawer');
var overlay = document.getElementById('pub-overlay');
function toggleDrawer(){
    drawer.classList.toggle('open');
    overlay.classList.toggle('open');
    burger.classList.toggle('open');
}
burger.addEventListener('click', toggleDrawer);
overlay.addEventListener('click', toggleDrawer);
</script>
@yield('page_js')
</body>
</html>
