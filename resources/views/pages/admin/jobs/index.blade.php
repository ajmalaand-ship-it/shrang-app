@extends("layouts.admin")
@section("title", "Jobs — Admin")
@section("content")
<div class="sh-page-wrap admin-page">
    <header class="sh-section">
        <h1 class="sh-heading">Generation Jobs</h1>
        <div style="margin-top:1rem; display:flex; gap:0.5rem; flex-wrap:wrap;">
            @foreach (["pending","running","done","failed"] as $s)
                <a href="{{ route("admin.jobs.index", ["status" => $s]) }}"
                   class="sh-btn sh-btn--sm {{ request("status") === $s ? "sh-btn--primary" : "sh-btn--ghost" }}">
                    {{ ucfirst($s) }}
                </a>
            @endforeach
            <a href="{{ route("admin.jobs.index") }}" class="sh-btn sh-btn--sm sh-btn--ghost">All</a>
        </div>
    </header>
    <div class="sh-card">
        <div class="sh-card__body">
            <table class="admin-page__table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Provider</th>
                        <th>Status</th>
                        <th>Progress</th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($jobs as $job)
                        <tr>
                            <td><code>{{ substr($job->id, 0, 8) }}...</code></td>
                            <td>{{ $job->user?->email ?? "—" }}</td>
                            <td>{{ $job->ai_provider }}</td>
                            <td><span class="sh-badge sh-badge--status">{{ $job->status }}</span></td>
                            <td>{{ $job->progress_pct }}%</td>
                            <td>{{ $job->created_at->format("Y-m-d H:i") }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div style="margin-top:1rem;">{{ $jobs->links() }}</div>
        </div>
    </div>
</div>
@endsection
