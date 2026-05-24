@extends("layouts.app")
@section("title", "Admin Dashboard")
@section("content")
<div class="sh-page-wrap admin-page">
    <header class="sh-section">
        <h1 class="sh-heading">Admin Dashboard</h1>
    </header>
    @if (session("success"))
        <div class="sh-notice sh-notice--success">{{ session("success") }}</div>
    @endif
    <div class="admin-page__stats">
        <div class="sh-card admin-page__stat">
            <div class="sh-card__body">
                <p class="admin-page__stat-label">Total Users</p>
                <p class="admin-page__stat-number">{{ number_format($stats["total_users"]) }}</p>
            </div>
        </div>
        <div class="sh-card admin-page__stat">
            <div class="sh-card__body">
                <p class="admin-page__stat-label">Total Clips</p>
                <p class="admin-page__stat-number">{{ number_format($stats["total_clips"]) }}</p>
            </div>
        </div>
        <div class="sh-card admin-page__stat">
            <div class="sh-card__body">
                <p class="admin-page__stat-label">Pending Jobs</p>
                <p class="admin-page__stat-number">{{ $stats["pending_jobs"] }}</p>
            </div>
        </div>
        <div class="sh-card admin-page__stat">
            <div class="sh-card__body">
                <p class="admin-page__stat-label">Failed Jobs</p>
                <p class="admin-page__stat-number">{{ $stats["failed_jobs"] }}</p>
            </div>
        </div>
        <div class="sh-card admin-page__stat">
            <div class="sh-card__body">
                <p class="admin-page__stat-label">AI Errors Today</p>
                <p class="admin-page__stat-number">{{ $stats["ai_errors_today"] }}</p>
            </div>
        </div>
    </div>
    <div class="admin-page__nav sh-section">
        <a href="{{ route("admin.users.index") }}" class="sh-btn sh-btn--ghost">Users</a>
        <a href="{{ route("admin.jobs.index") }}" class="sh-btn sh-btn--ghost">Jobs</a>
        <a href="{{ route("admin.ai-usage.index") }}" class="sh-btn sh-btn--ghost">AI Usage</a>
        <a href="{{ route("admin.settings.index") }}" class="sh-btn sh-btn--ghost">Settings</a>
        <a href="{{ route("admin.language-hints.index") }}" class="sh-btn sh-btn--ghost">Language Hints</a>
        <a href="{{ route("admin.audit-log.index") }}" class="sh-btn sh-btn--ghost">Audit Log</a>
    </div>
</div>
@endsection
