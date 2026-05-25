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
        <a href="{{ route('create') }}" class="sh-nav__logo">
            <span class="sh-nav__logo-arabic">شرنګ</span>
            <span class="sh-nav__logo-latin">Shrang</span>
        </a>
        <ul class="sh-nav__links">
            <li><a href="{{ route('create') }}">Create</a></li>
            <li><a href="{{ route('dashboard') }}">My Clips</a></li>
            <li><a href="{{ route('credits') }}">Credits</a></li>
        </ul>
        <div class="sh-nav__right">
            @auth
                <div class="sh-nav__credits">
                    <strong>{{ auth()->user()->credit_balance }}</strong> credits
                </div>
                <a href="{{ route('account') }}" class="sh-btn sh-btn--ghost sh-btn--sm">Account</a>
                <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="sh-btn sh-btn--ghost sh-btn--sm">Log out</button>
                </form>
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

</body>
</html>
