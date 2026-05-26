@extends('layouts.admin')
@section('title', 'Generation Jobs')
@section('content')

<div style="display:flex;gap:0.5rem;flex-wrap:wrap;margin-bottom:1.5rem;">
    <a href="{{ route('admin.jobs.index') }}"
       class="sh-btn sh-btn--sm {{ !request('status') ? 'sh-btn--primary' : 'sh-btn--ghost' }}">All</a>
    @foreach(['pending','running','done','failed'] as $s)
        <a href="{{ route('admin.jobs.index', ['status' => $s]) }}"
           class="sh-btn sh-btn--sm {{ request('status') === $s ? 'sh-btn--primary' : 'sh-btn--ghost' }}">
            {{ ucfirst($s) }}
        </a>
    @endforeach
</div>

<div class="sh-card">
    <div class="sh-card__header">
        Jobs
        <span class="sh-badge">{{ $jobs->total() }}</span>
    </div>
    <div class="sh-card__body" style="padding:0;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Clip</th>
                    <th>User</th>
                    <th>Provider</th>
                    <th>Status</th>
                    <th>Progress</th>
                    <th>Created</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($jobs as $job)
                <tr>
                    <td>
                        <div style="font-weight:500;font-size:0.875rem;">{{ $job->clip?->title ?? '—' }}</div>
                        <div style="font-size:0.75rem;color:var(--sh-text-muted);">{{ substr($job->id, 0, 8) }}...</div>
                    </td>
                    <td style="font-size:0.85rem;">{{ $job->user?->email ?? '—' }}</td>
                    <td><span class="sh-badge">{{ $job->ai_provider }}</span></td>
                    <td>
                        <span class="sh-badge sh-badge--status-{{ $job->status === 'done' ? 'ready' : ($job->status === 'failed' ? 'failed' : 'processing') }}">
                            {{ ucfirst($job->status) }}
                        </span>
                    </td>
                    <td>{{ $job->progress_pct }}%</td>
                    <td style="font-size:0.8rem;color:var(--sh-text-muted);">{{ $job->created_at->diffForHumans() }}</td>
                    <td>
                        @if($job->status === 'failed')
                            <form method="POST" action="{{ route('admin.jobs.retry', $job) }}">
                                @csrf
                                <button type="submit" class="sh-btn sh-btn--sm sh-btn--primary">↺ Retry</button>
                            </form>
                        @endif
                        @if($job->error_message)
                            <div style="font-size:0.75rem;color:var(--sh-danger);margin-top:0.25rem;max-width:200px;">
                                {{ Str::limit($job->error_message, 60) }}
                            </div>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center;color:var(--sh-text-muted);padding:2rem;">No jobs found.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div style="padding:1rem;">{{ $jobs->links() }}</div>
    </div>
</div>

@endsection
