@extends("layouts.app")
@section("title", "AI Usage — Admin")
@section("content")
<div class="sh-page-wrap admin-page">
    <header class="sh-section">
        <h1 class="sh-heading">AI Usage</h1>
    </header>
    <div class="admin-page__stats" style="margin-bottom:2rem;">
        <div class="sh-card admin-page__stat">
            <div class="sh-card__body">
                <p class="admin-page__stat-label">Total Calls</p>
                <p class="admin-page__stat-number">{{ number_format($stats["total_calls"]) }}</p>
            </div>
        </div>
        <div class="sh-card admin-page__stat">
            <div class="sh-card__body">
                <p class="admin-page__stat-label">Total Errors</p>
                <p class="admin-page__stat-number">{{ number_format($stats["total_errors"]) }}</p>
            </div>
        </div>
        <div class="sh-card admin-page__stat">
            <div class="sh-card__body">
                <p class="admin-page__stat-label">Total Cost (USD)</p>
                <p class="admin-page__stat-number">${{ number_format($stats["total_cost_usd"], 2) }}</p>
            </div>
        </div>
        <div class="sh-card admin-page__stat">
            <div class="sh-card__body">
                <p class="admin-page__stat-label">Avg Latency</p>
                <p class="admin-page__stat-number">{{ number_format($stats["avg_latency_ms"]) }}ms</p>
            </div>
        </div>
    </div>
    <div class="sh-card">
        <div class="sh-card__header">Recent Calls (last 50)</div>
        <div class="sh-card__body">
            <table class="admin-page__table">
                <thead>
                    <tr><th>Provider</th><th>Capability</th><th>Status</th><th>Latency</th><th>Cost</th><th>Time</th></tr>
                </thead>
                <tbody>
                    @foreach ($recent as $log)
                        <tr>
                            <td>{{ $log->provider }}</td>
                            <td>{{ $log->capability }}</td>
                            <td><span class="sh-badge sh-badge--status">{{ $log->status }}</span></td>
                            <td>{{ $log->latency_ms }}ms</td>
                            <td>${{ number_format($log->provider_cost_usd, 4) }}</td>
                            <td>{{ $log->created_at->format("H:i:s") }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
