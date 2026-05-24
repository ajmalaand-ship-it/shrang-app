@extends("layouts.app")
@section("title", "Users — Admin")
@section("content")
<div class="sh-page-wrap admin-page">
    <header class="sh-section">
        <h1 class="sh-heading">Users</h1>
        <form method="GET" action="{{ route("admin.users.index") }}" style="margin-top:1rem;">
            <input type="text" name="search" class="sh-input" placeholder="Search by name or email"
                   value="{{ request("search") }}" style="max-width:320px;">
            <button type="submit" class="sh-btn sh-btn--ghost">Search</button>
        </form>
    </header>
    <div class="sh-card">
        <div class="sh-card__body">
            <table class="admin-page__table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Credits</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td><span class="sh-badge">{{ $user->role }}</span></td>
                            <td>{{ number_format($user->credit_balance) }}</td>
                            <td>
                                <span class="sh-badge {{ $user->is_active ? "sh-badge--status" : "sh-notice--danger" }}">
                                    {{ $user->is_active ? "Active" : "Banned" }}
                                </span>
                            </td>
                            <td>{{ $user->created_at->format("Y-m-d") }}</td>
                            <td>
                                <a href="{{ route("admin.users.show", $user) }}" class="sh-btn sh-btn--sm sh-btn--ghost">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div style="margin-top:1rem;">{{ $users->links() }}</div>
        </div>
    </div>
</div>
@endsection
