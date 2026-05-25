@extends('layouts.admin')
@section('title', 'Dashboard')
@section('content')

<div class="admin-stats">
    <div class="sh-card admin-stat">
        <div class="sh-card__body">
            <div class="admin-stat__label">Total Users</div>
            <div class="admin-stat__number">{{ number_format($stats['total_users']) }}</div>
        </div>
    </div>
    <div class="sh-card admin-stat">
        <div class="sh-card__body">
            <div class="admin-stat__label">Total Clips</div>
            <div class="admin-stat__number">{{ number_format($stats['total_clips']) }}</div>
        </div>
    </div>
    <div class="sh-card admin-stat">
        <div class="sh-card__body">
            <div class="admin-stat__label">Pending Jobs</div>
            <div class="admin-stat__number">{{ $stats['pending_jobs'] }}</div>
        </div>
    </div>
    <div class="sh-card admin-stat">
        <div class="sh-card__body">
            <div class="admin-stat__label">Failed Jobs</div>
            <div class="admin-stat__number" style="color:var(--sh-danger);">{{ $stats['failed_jobs'] }}</div>
        </div>
    </div>
    <div class="sh-card admin-stat">
        <div class="sh-card__body">
            <div class="admin-stat__label">AI Errors Today</div>
            <div class="admin-stat__number" style="color:var(--sh-warning);">{{ $stats['ai_errors_today'] }}</div>
        </div>
    </div>
</div>

<div class="sh-grid" style="gap:1rem;">
    <a href="{{ route('admin.users.index') }}" class="sh-card" style="text-decoration:none;padding:1.5rem;display:block;">
        <div style="font-size:2rem;margin-bottom:0.5rem;">👥</div>
        <div style="font-weight:600;margin-bottom:0.25rem;">Users</div>
        <div class="sh-text-muted" style="font-size:0.85rem;">Manage accounts, credits, bans</div>
    </a>
    <a href="{{ route('admin.jobs.index') }}" class="sh-card" style="text-decoration:none;padding:1.5rem;display:block;">
        <div style="font-size:2rem;margin-bottom:0.5rem;">⚙️</div>
        <div style="font-weight:600;margin-bottom:0.25rem;">Generation Jobs</div>
        <div class="sh-text-muted" style="font-size:0.85rem;">Monitor and retry AI jobs</div>
    </a>
    <a href="{{ route('admin.ai-usage.index') }}" class="sh-card" style="text-decoration:none;padding:1.5rem;display:block;">
        <div style="font-size:2rem;margin-bottom:0.5rem;">🤖</div>
        <div style="font-weight:600;margin-bottom:0.25rem;">AI Usage</div>
        <div class="sh-text-muted" style="font-size:0.85rem;">Cost, errors, and latency</div>
    </a>
    <a href="{{ route('admin.settings.index') }}" class="sh-card" style="text-decoration:none;padding:1.5rem;display:block;">
        <div style="font-size:2rem;margin-bottom:0.5rem;">🔧</div>
        <div style="font-weight:600;margin-bottom:0.25rem;">Settings</div>
        <div class="sh-text-muted" style="font-size:0.85rem;">Credit costs, limits, features</div>
    </a>
    <a href="{{ route('admin.language-hints.index') }}" class="sh-card" style="text-decoration:none;padding:1.5rem;display:block;">
        <div style="font-size:2rem;margin-bottom:0.5rem;">🌐</div>
        <div style="font-weight:600;margin-bottom:0.25rem;">Language Hints</div>
        <div class="sh-text-muted" style="font-size:0.85rem;">Pashto, Dari, Urdu pronunciation</div>
    </a>
    <a href="{{ route('admin.audit-log.index') }}" class="sh-card" style="text-decoration:none;padding:1.5rem;display:block;">
        <div style="font-size:2rem;margin-bottom:0.5rem;">📋</div>
        <div style="font-weight:600;margin-bottom:0.25rem;">Audit Log</div>
        <div class="sh-text-muted" style="font-size:0.85rem;">All admin actions recorded</div>
    </a>
</div>

@endsection
