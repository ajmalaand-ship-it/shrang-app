@extends("layouts.admin")
@section("title", "Audit Log — Admin")
@section("content")
<div class="sh-page-wrap admin-page">
    <header class="sh-section">
        <h1 class="sh-heading">Audit Log</h1>
    </header>
    <div class="sh-card">
        <div class="sh-card__body">
            <table class="admin-page__table">
                <thead>
                    <tr><th>Actor</th><th>Action</th><th>IP</th><th>Time</th></tr>
                </thead>
                <tbody>
                    @foreach ($logs as $log)
                        <tr>
                            <td>{{ $log->actor?->email ?? "system" }}</td>
                            <td>{{ $log->action }}</td>
                            <td>{{ $log->ip_address }}</td>
                            <td>{{ $log->created_at->format("Y-m-d H:i:s") }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div style="margin-top:1rem;">{{ $logs->links() }}</div>
        </div>
    </div>
</div>
@endsection
