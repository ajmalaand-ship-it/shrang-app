<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — Shrang Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/shrang.css') }}">
</head>
<body class="admin-body">

<div class="admin-layout">

    {{-- Sidebar --}}
    <aside class="admin-sidebar">
        <div class="admin-sidebar__logo">
            <a href="{{ route('admin.dashboard') }}">
                <span class="admin-sidebar__logo-arabic">شرنګ</span>
                <span class="admin-sidebar__logo-latin">Admin</span>
            </a>
        </div>

        <nav class="admin-sidebar__nav">
            <a href="{{ route('admin.dashboard') }}"
               class="admin-sidebar__link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                📊 Dashboard
            </a>
            <a href="{{ route('admin.users.index') }}"
               class="admin-sidebar__link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                👥 Users
            </a>
            <a href="{{ route('admin.jobs.index') }}"
               class="admin-sidebar__link {{ request()->routeIs('admin.jobs.*') ? 'active' : '' }}">
                ⚙️ Generation Jobs
            </a>
            <a href="{{ route('admin.ai-usage.index') }}"
               class="admin-sidebar__link {{ request()->routeIs('admin.ai-usage.*') ? 'active' : '' }}">
                🤖 AI Usage
            </a>
            <a href="{{ route('admin.settings.index') }}"
               class="admin-sidebar__link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                ⚙️ Settings
            </a>
            <a href="{{ route('admin.language-hints.index') }}"
               class="admin-sidebar__link {{ request()->routeIs('admin.language-hints.*') ? 'active' : '' }}">
                🌐 Language Hints
            </a>
            <a href="{{ route('admin.discover.index') }}"
               class="admin-sidebar__link {{ request()->routeIs('admin.discover.*') ? 'active' : '' }}">
                🔍 Discover
            </a>
            <a href="{{ route('admin.packages.index') }}"
               class="admin-sidebar__link {{ request()->routeIs('admin.packages.*') ? 'active' : '' }}">
                💳 Credit Packages
            </a>
            <a href="{{ route('admin.payments.index') }}"
               class="admin-sidebar__link {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
                💰 Payments
            </a>
            <a href="{{ route('admin.audit-log.index') }}"
               class="admin-sidebar__link {{ request()->routeIs('admin.audit-log.*') ? 'active' : '' }}">
                📋 Audit Log
            </a>
        </nav>

        <div class="admin-sidebar__footer">
            <a href="{{ route('dashboard') }}" class="admin-sidebar__link">← Back to App</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="admin-sidebar__logout">Log out</button>
            </form>
        </div>
    </aside>

    {{-- Main content --}}
    <div class="admin-main">
        <header class="admin-topbar">
            <h1 class="admin-topbar__title">@yield('title', 'Admin')</h1>
            <div class="admin-topbar__user">
                {{ auth()->user()->name }}
                <span class="sh-badge">Admin</span>
            </div>
        </header>

        <div class="admin-content">
            @if(session('success'))
                <div class="sh-notice sh-notice--success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="sh-notice sh-notice--danger">{{ session('error') }}</div>
            @endif

            @yield('content')
        </div>
    </div>

</div>

</body>
</html>
