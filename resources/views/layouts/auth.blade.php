<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ $direction ?? 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') — Shrang</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @if($isRtl ?? false)
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;600;700&family=Amiri:wght@400;700&display=swap" rel="stylesheet">
    @endif
    <link rel="stylesheet" href="{{ asset('css/shrang.css') }}">
</head>
<body class="sh-auth-body">
    <div class="sh-auth-wrap">
        <div class="sh-auth-card">
            <a href="/" class="sh-auth-logo">
                <span class="sh-auth-logo__ar">شرنګ</span>
                <span class="sh-auth-logo__en">Shrang</span>
            </a>
            @yield('content')
        </div>
    </div>
</body>
</html>
