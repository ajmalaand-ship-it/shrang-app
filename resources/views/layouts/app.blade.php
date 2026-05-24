<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ in_array(app()->getLocale(), ['ps', 'fa', 'ur', 'ar']) ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Shrang') }} - @yield('title', 'AI Music')</title>
    <link rel="stylesheet" href="{{ asset('css/shrang.css') }}">
    @yield('page_css')
    @yield('head_extra')
</head>
<body class="sh-body">
    <nav class="sh-nav">
        <div class="sh-page-wrap">
            <a href="/" class="sh-nav__logo">Shrang</a>
            <div class="sh-lang-selector">
                @foreach (['ps' => 'پښتو', 'fa' => 'دری', 'ur' => 'اردو', 'ar' => 'عربي', 'hi' => 'हि', 'en' => 'EN'] as $code => $label)
                    <a href="{{ route('lang.switch', $code) }}"
                       class="sh-lang-selector__option {{ app()->getLocale() === $code ? 'sh-lang-selector__option--active' : '' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>
    </nav>
    <main class="sh-main">
        @yield('content')
    </main>
    <footer class="sh-footer">
        <div class="sh-page-wrap">
            <p>&copy; {{ date('Y') }} Shrang</p>
        </div>
    </footer>
    @yield('page_js')
</body>
</html>
