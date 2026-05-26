<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ in_array(app()->getLocale(), ['ps','fa','ur','ar']) ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Shrang') — Shrang</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&family=Inter:wght@300;400;500;600;700&family=Vazirmatn:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/shrang.css') }}">
    @yield('head_extra')
</head>
<body>

    <nav class="sh-nav">
        <a href="/" class="sh-nav__logo">
            <span class="sh-nav__logo-arabic">شرنګ</span>
            <span class="sh-nav__logo-latin">Shrang</span>
        </a>
        <ul class="sh-nav__links">
            <li><a href="{{ route('create') }}" class="{{ request()->routeIs('create*') ? 'active' : '' }}">Create</a></li>
            <li><a href="{{ route('discover') }}" class="{{ request()->routeIs('discover*') ? 'active' : '' }}">Discover</a></li>
            <li><a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">My Clips</a></li>
            <li><a href="{{ route('credits') }}" class="{{ request()->routeIs('credits*') ? 'active' : '' }}">Credits</a></li>
        </ul>
        <div class="sh-nav__right">
            @auth
                <div class="sh-nav__credits">
                    <strong>{{ auth()->user()->credit_balance }}</strong> credits
                </div>
                <div class="sh-nav__account">
                    <button class="sh-nav__account-btn">
                        {{ auth()->user()->name }} ▾
                    </button>
                    <div class="sh-nav__dropdown">
                        <a href="{{ route('dashboard') }}">My Clips</a>
                        <a href="{{ route('credits') }}">Credits & Billing</a>
                        <a href="{{ route('account') }}">Account Settings</a>
                        <div class="sh-nav__dropdown-divider"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit">Log out</button>
                        </form>
                    </div>
                </div>
            @endauth
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

    <footer class="sh-footer">
        <span>&copy; {{ date('Y') }} Shrang</span>
        <a href="{{ route('create') }}">Create</a>
        <a href="{{ route('dashboard') }}">My Clips</a>
    </footer>

    @auth
<nav class="sh-bottom-nav">
    <div class="sh-bottom-nav__items">
        <a href="{{ route('create') }}" class="sh-bottom-nav__item {{ request()->routeIs('create*') ? 'active' : '' }}">
            <span class="sh-bottom-nav__icon">🎵</span>
            <span>Create</span>
        </a>
        <a href="{{ route('discover') }}" class="sh-bottom-nav__item {{ request()->routeIs('discover*') ? 'active' : '' }}">
            <span class="sh-bottom-nav__icon">🔍</span>
            <span>Discover</span>
        </a>
        <a href="{{ route('dashboard') }}" class="sh-bottom-nav__item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <span class="sh-bottom-nav__icon">📂</span>
            <span>My Clips</span>
        </a>
        <a href="{{ route('account') }}" class="sh-bottom-nav__item {{ request()->routeIs('account*') ? 'active' : '' }}">
            <span class="sh-bottom-nav__icon">👤</span>
            <span>Account</span>
        </a>
    </div>
</nav>
@endauth
@yield("page_js")
</body>
</html>
